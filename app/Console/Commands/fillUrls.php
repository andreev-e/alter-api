<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tag;
use Illuminate\Support\Str;

class fillUrls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fill:url';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill urls';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tags = Tag::all(); 
        foreach($tags as $tag) {
            $tag->url = Str::slug($tag->NAME);
            $tag->save();
        }
        echo "Done";
    }
}
