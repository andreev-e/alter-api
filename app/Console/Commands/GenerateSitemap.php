<?php

namespace App\Console\Commands;

use App\Models\Location;
use App\Models\Poi;
use App\Models\Route;
use App\Models\Tag;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate sitemaps for every language domain';

    /**
     * Языковые домены и файлы их карт сайта. Фронтенд отдаёт нужный файл как /sitemap.xml своего домена.
     */
    private const DOMAINS = [
        'en' => ['host' => 'https://altertravel.pro', 'file' => 'public/sitemap.xml'],
        'ru' => ['host' => 'https://altertravel.ru', 'file' => 'public/sitemap_ru.xml'],
    ];

    public function handle(): void
    {
        $paths = $this->paths();

        foreach (self::DOMAINS as $domain) {
            $sitemap = Sitemap::create();

            foreach ($paths as $path) {
                $url = Url::create($domain['host'] . $path);

                // Адреса на доменах совпадают, поэтому связываем языковые версии страницы
                foreach (self::DOMAINS as $locale => $alternate) {
                    $url->addAlternate($alternate['host'] . $path, $locale);
                }

                $sitemap->add($url);
            }

            $sitemap->writeToFile($domain['file']);
        }
    }

    /**
     * @return string[]
     */
    private function paths(): array
    {
        $paths = ['/'];

        $items = Location::query()
            ->where('count', '>', 0)->get();
        foreach ($items as $item) {
            $paths[] = '/region/' . $item->url;

            foreach ($item->tags as $tag) {
                $paths[] = '/region/' . $item->url . '/' . $tag->url;
            }
        }

        $items = Tag::query()
            ->select('url')->where('COUNT', '>', 0)->get();
        foreach ($items as $item) {
            $paths[] = '/tag/' . $item->url;
        }

        $items = Route::query()->select('id')->where('show', 1)->get();
        foreach ($items as $item) {
            $paths[] = '/route/' . $item->id;
        }

        $items = Poi::query()->select('id')->where('show', 1)->get();
        foreach ($items as $item) {
            $paths[] = '/poi/' . $item->id;
        }

        return $paths;
    }
}
