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

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $sitemap = Sitemap::create()
            ->add(Url::create('https://altertravel.pro')
                ->setChangeFrequency(0)
                ->setPriority(0));

        $items = Location::query()
            ->where('count', '>', 0)->get();
        foreach ($items as $item) {
            $sitemap->add(Url::create('https://altertravel.pro/region/' . $item->url)
                ->setChangeFrequency(0)
                ->setPriority(0));

            foreach ($item->tags as $tag) {
                $sitemap->add(Url::create('https://altertravel.pro/region/' . $item->url . '/' . $tag->url)
                    ->setChangeFrequency(0)
                    ->setPriority(0));
            }
        }

        $items = Tag::query()
            ->select('url')->where('COUNT', '>', 0)->get();
        foreach ($items as $item) {
            $sitemap->add(Url::create('https://altertravel.pro/tag/' . $item->url)
                ->setChangeFrequency(0)
                ->setPriority(0));
        }

        $items = Route::query()->select('id')->where('show', 1)->get();
        foreach ($items as $item) {
            $sitemap->add(Url::create('https://altertravel.pro/route/' . $item->id)
                ->setChangeFrequency(0)
                ->setPriority(0));
        }

        $items = Poi::query()->select('id')->where('show', 1)->get();
        foreach ($items as $item) {
            $sitemap->add(Url::create('https://altertravel.pro/poi/' . $item->id)
                ->setChangeFrequency(0)
                ->setPriority(0));
        }

        $sitemap
            ->writeToFile('public/sitemap.xml');
    }
}
