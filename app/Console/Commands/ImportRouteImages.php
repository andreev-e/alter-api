<?php

namespace App\Console\Commands;

use App\Models\Route;
use Exception;
use Illuminate\Console\Command;

class ImportRouteImages extends Command
{
    protected $signature = 'import:route-images';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $left = Route::query()->select('id')->whereNull('image_processed')
            ->count();
        $routes = Route::query()->select('id')->whereNull('image_processed')
            ->limit(100)->get();
        foreach ($routes as $route) {
            echo 'Left:' . $left-- . ' ' . $route->id . "\n\r";

            $route->clearMediaCollection('route-image');

            for ($i = 1; $i < 20; $i++) {
                try {
                    $route->addMediaFromUrl('https://altertravel.ru/routes/' . $route->id . '/' . $i . '.jpg',
                        'image/jpeg')
                        ->storingConversionsOnDisk('public')
                        ->toMediaCollection('route-image', 'public');
                } catch (Exception $e) {
                    echo $e->getMessage() . "\n\r";
                    $route->image_processed = 1;
                    $route->save();
                    break;
                }
            }
            $route->image_processed = 1;
            $route->save();
        }


    }
}
