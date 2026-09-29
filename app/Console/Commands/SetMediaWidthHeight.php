<?php

namespace App\Console\Commands;

use App\Http\Controllers\Traits\SetsMediaCustomPropertiesTrait;
use Exception;
use Illuminate\Console\Command;
use Intervention\Image\Facades\Image;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Storage;

class SetMediaWidthHeight extends Command
{
    use SetsMediaCustomPropertiesTrait;

    protected $signature = 'media:calculate-dimensions {--fix-upscaled : Пересчитать размеры по orig_width/orig_height}';

    protected $description = 'Calculate width and height of media';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        if ($this->option('fix-upscaled')) {
            $this->fixUpscaled();

            return;
        }

        $media = Media::query()
            ->where('custom_properties', '[]')
            ->where('model_type', '=', 'App\Models\User')
            ->cursor();
        foreach ($media as $image) {
            /*  @var Media $image */
            try {
                $imageData = Storage::disk('public')->get($image->getPath('thumb'));
                $img = Image::make($imageData);
                $image->setCustomProperty('width', $img->width());
                $image->setCustomProperty('height', $img->height());
                var_dump($image->id);
            } catch (Exception $e) {
                echo $e->getMessage();
            }

            $image->save();

        }

        $media = Media::query()
            ->where('custom_properties', '[]')
            ->where('model_type', '<>', 'App\Models\User')
            ->cursor();
        foreach ($media as $image) {
            /*  @var Media $image */
            try {
                $imageData = Storage::disk('public')->get($image->getPath('full'));
                $img = Image::make($imageData);
                $image->setCustomProperty('width', $img->width());
                $image->setCustomProperty('height', $img->height());
                var_dump($image->id);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            if ($image->model->author) {
                $image->setCustomProperty('author', $image->model->author);
            }
            if ($image->model->username) {
                $image->setCustomProperty('author', $image->model->username);
            }

            $image->save();

        }
    }

    /**
     * Исправляет размеры маленьких картинок, которые раньше записывались увеличенными до FULL_SIZE
     */
    private function fixUpscaled(): void
    {
        $media = Media::query()
            ->where('model_type', '<>', 'App\Models\User')
            ->cursor();
        foreach ($media as $image) {
            /*  @var Media $image */
            $origWidth = $image->getCustomProperty('orig_width');
            $origHeight = $image->getCustomProperty('orig_height');
            if (!$origWidth || !$origHeight) {
                continue;
            }

            [$width, $height] = self::fullSizeDimensions($origWidth, $origHeight);
            if ($image->getCustomProperty('width') == $width && $image->getCustomProperty('height') == $height) {
                continue;
            }

            $this->info(sprintf(
                '%d: %dx%d -> %dx%d',
                $image->id,
                $image->getCustomProperty('width'),
                $image->getCustomProperty('height'),
                $width,
                $height,
            ));
            $image->setCustomProperty('width', $width);
            $image->setCustomProperty('height', $height);
            $image->save();
        }
    }
}
