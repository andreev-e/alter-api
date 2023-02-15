<?php

namespace App\Console\Commands;

use App\Models\Route;
use Illuminate\Console\Command;

class AttachPoisToRoutes extends Command
{
    protected $signature = 'routes:pois';

    protected $description = 'Command description';

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
        $routes = Route::query()->select('id', 'POINTS')->get();
        foreach ($routes as $route) {
            $pois = explode('|', $route->POINTS);
            $pois = array_filter($pois, function($item) {
                return $item;
            });
            $route->pois()->sync($pois);
        }
    }
}
