<?php

namespace App\Console\Commands;

use App\Models\Location;
use App\Models\Tag;
use Illuminate\Console\Command;

class TagCount extends Command
{
    protected $signature = 'tag:count';

    protected $description = 'Tag count';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $tags = Tag::query()->where('TYPE', 0)->get();
        foreach ($tags as $tag) {
            $tag->COUNT = $tag->pois()->count();
            $tag->save();
        }

        $locations = Location::all();
        foreach ($locations as $location) {
            $location->count = $location->pois()->count();
            $location->save();
        }
    }
}
