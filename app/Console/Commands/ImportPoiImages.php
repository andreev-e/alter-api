<?php

namespace App\Console\Commands;

use App\Models\Poi;
use Exception;
use Illuminate\Console\Command;

class ImportPoiImages extends Command
{
    protected $signature = 'import:poi-images';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $left = Poi::query()->select('id')->whereNull('image_processed')
            ->count();
        $pois = Poi::query()->select('id')->whereNull('image_processed')
            ->limit(1000)->get();
        foreach ($pois as $poi) {
            echo 'Left:' . $left-- . ' ' . $poi->id . "\n\r";
            try {
            $poi->addMediaFromUrl('https://altertravel.ru/images/' . $poi->id . '.jpg', 'image/jpeg')
                ->storingConversionsOnDisk('s3')
                ->toMediaCollection('image', 's3');
            } catch (Exception $e) {
                echo $e->getMessage(). "\n\r";
            }

            foreach ([1, 2, 3] as $i) {
                try {
                    $poi->addMediaFromUrl('https://altertravel.ru/images/' . $poi->id . '/' . $i . '.jpg', 'image/jpeg')
                        ->storingConversionsOnDisk('s3')
                        ->toMediaCollection('image', 's3');
                } catch (Exception $e) {
                    echo $e->getMessage(). "\n\r";
                }
            }

            $poi->image_processed = 1;
            $poi->save();
        }
    }
}
