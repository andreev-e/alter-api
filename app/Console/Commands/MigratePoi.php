<?php

namespace App\Console\Commands;

use App\Enums\Commentables;
use App\Models\Comment;
use App\Models\Poi;
use App\Models\RouteComment;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MigratePoi extends Command
{
    protected $signature = 'poi:migrate';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        foreach (Poi::query()->whereNull('created_at')->cursor() as $poi) {
            $poi->created_at = new Carbon($poi->date);
            $poi->updated_at = null;
            echo "$poi->name\n";
            $poi->save();
        };
    }
}
