<?php

namespace App\Console\Commands;

use App\Enums\Commentables;
use App\Models\Comment;
use App\Models\RouteComment;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MigrateComments extends Command
{
    protected $signature = 'comments:migrate';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        foreach (Comment::query()->whereNull('created_at')->limit(1000)->get() as $comment) {
            $comment->created_at = new Carbon($comment->time);
            $comment->save();
        };
    }
}
