<?php

namespace App\Console\Commands;

use App\Models\Location;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Console\Command;

class CountStats extends Command
{
    protected $signature = 'count:stats';

    protected $description = 'Tag count';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $users = User::query()->cursor();
        foreach ($users as $user) {
            $user->publications = $user->pois()->count();
            $user->save();
        }

        $tags = Tag::query()->cursor();
        foreach ($tags as $tag) {
            $tag->COUNT = $tag->pois()->count();
            $tag->save();
        }

        $locations = Location::query()->cursor();
        foreach ($locations as $location) {
            $location->count = $location->pois()->count();
            $location->save();
        }
    }
}
