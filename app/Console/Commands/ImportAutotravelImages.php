<?php

namespace App\Console\Commands;

use App\Http\Controllers\Traits\SetsMediaCustomPropertiesTrait;
use App\Models\Poi;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Http;
use Intervention\Image\Facades\Image;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ImportAutotravelImages extends Command
{
    use SetsMediaCustomPropertiesTrait;

    private const SOURCE_HOST = 'https://autotravel.ru';

    protected $signature = 'import:autotravel-images
        {--poi= : ID одной публикации}
        {--limit=0 : Сколько публикаций обработать за запуск}
        {--max-photos=0 : Сколько фото брать с одной страницы (0 — все)}
        {--dry-run : Только показать, какие фото будут скачаны}';

    protected $description = 'Копирует фото с autotravel.ru/otklik.php/N в публикации, которые ссылаются на эту страницу';

    public function handle()
    {
        $query = Poi::query()
            ->where('links', 'like', '%autotravel.ru/otklik.php/%')
            // Уже импортированные публикации пропускаем
            ->whereDoesntHave('media', function (Builder $query) {
                $query->where('custom_properties', 'like', '%source_url%');
            })
            ->orderBy('id');

        if ($this->option('poi')) {
            $query->where('id', $this->option('poi'));
        }
        if ($this->option('limit')) {
            $query->limit((int)$this->option('limit'));
        }

        $left = $query->count();
        foreach ($query->cursor() as $poi) {
            /* @var Poi $poi */
            $this->line('Left: ' . $left-- . ' ' . $poi->id . ' ' . $poi->links);

            try {
                $this->importPoi($poi);
            } catch (Exception $e) {
                $this->error($e->getMessage());
            }
        }
    }

    private function importPoi(Poi $poi): void
    {
        if (!preg_match('~autotravel\.ru/otklik\.php/(\d+)~', $poi->links, $matches)) {
            return;
        }

        $html = Http::timeout(30)->get(self::SOURCE_HOST . '/otklik.php/' . $matches[1])->throw()->body();
        $photos = $this->parsePhotos($html);
        if ($this->option('max-photos')) {
            $photos = array_slice($photos, 0, (int)$this->option('max-photos'));
        }
        if (!$photos) {
            $this->warn('  no photos');
            return;
        }

        if ($this->option('dry-run')) {
            foreach ($photos as $photo) {
                $this->line('  ' . $photo['url'] . ' (' . $photo['author'] . ')');
            }
            return;
        }

        $oldMain = $poi->getMedia('image');
        $added = [];
        foreach ($photos as $photo) {
            try {
                $added[] = $this->addPhoto($poi, $photo, $added ? 'poi-image' : 'image');
            } catch (Exception $e) {
                $this->error('  ' . $photo['url'] . ': ' . $e->getMessage());
            }
        }
        $this->info('  added ' . count($added) . ' of ' . count($photos));

        if (!$added) {
            return;
        }

        // Старое главное фото — уменьшенная копия с водяным знаком, заменяем его оригиналом
        $oldMain->each(fn (Media $media) => $media->delete());

        $addedIds = collect($added)->pluck('id');
        $otherIds = $poi->media()->whereNotIn('id', $addedIds)->orderBy('order_column')->pluck('id');
        $poi->sortImages($addedIds->merge($otherIds)->all());

        $poi->dominatecolor = null;
        $poi->timestamps = false;
        $poi->save();
    }

    /**
     * Фото из блока «Фотографии»; фото из og:image на сайте главное, ставим его первым
     */
    private function parsePhotos(string $html): array
    {
        preg_match_all("~<a\s[^>]*class='atcbox'[^>]*>~", $html, $links);

        $photos = [];
        foreach ($links[0] as $link) {
            if (!preg_match("~href='(/phalbum/[^']+)'~", $link, $href)) {
                continue;
            }
            preg_match("~title='[^']*Фото:\s*([^']*)'~u", $link, $title);
            $url = self::SOURCE_HOST . $href[1];
            $photos[$url] = [
                'url' => $url,
                'author' => html_entity_decode(trim($title[1] ?? '')),
            ];
        }

        if (preg_match('~property="og:image"\s+content="([^"]+)"~', $html, $og)) {
            $mainUrl = preg_replace('~^https?://autotravel\.ru~', self::SOURCE_HOST, $og[1]);
            if (isset($photos[$mainUrl])) {
                $photos = [$mainUrl => $photos[$mainUrl]] + $photos;
            }
        }

        return array_values($photos);
    }

    private function addPhoto(Poi $poi, array $photo, string $collection): Media
    {
        $media = $poi->addMediaFromUrl($photo['url'], 'image/jpeg')
            ->storingConversionsOnDisk('public')
            ->toMediaCollection($collection, 'public');

        $img = Image::make($media->getPath());
        [$width, $height] = self::fullSizeDimensions($img->width(), $img->height());

        $author = $photo['author'] ? $photo['author'] . ' / autotravel.ru' : 'autotravel.ru';
        $media->setCustomProperty('author', $author);
        $media->setCustomProperty('width', $width);
        $media->setCustomProperty('height', $height);
        $media->setCustomProperty('orig_width', $img->width());
        $media->setCustomProperty('orig_height', $img->height());
        $media->setCustomProperty('source_url', $photo['url']);
        $media->save();

        return $media;
    }
}
