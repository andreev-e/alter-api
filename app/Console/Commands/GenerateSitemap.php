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

    protected $description = 'Generate sitemap for altertravel.ru';

    private const HOST = 'https://altertravel.ru';

    public function handle(): void
    {
        $sitemap = Sitemap::create();

        foreach ($this->paths() as $path) {
            $sitemap->add(Url::create(self::HOST . $path));
        }

        $sitemap->writeToFile('public/sitemap.xml');
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
