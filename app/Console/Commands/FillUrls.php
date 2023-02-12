<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tag;
use Illuminate\Support\Str;

class FillUrls extends Command
{
    protected $signature = 'fill:url';

    protected $description = 'Fill urls';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $tags = Tag::query()->select('id', 'name')->where('url', '')->get();
        foreach($tags as $tag) {
            $tag->url = Str::slug($tag->name);
            $tag->save();
        }
        echo "Done";
    }
}
