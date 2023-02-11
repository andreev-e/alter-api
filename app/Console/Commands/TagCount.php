<?php

namespace App\Console\Commands;

use App\Models\Tag;
use Illuminate\Console\Command;

class TagCount extends Command
{
    protected $signature = 'tag:count';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $tags = Tag::all();
        foreach ($tags as $tag) {
            $tag->COUNT = $tag->pois()->count();
            $tag->save();
        }
    }
}
