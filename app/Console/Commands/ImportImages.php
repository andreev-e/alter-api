<?php

namespace App\Console\Commands;

use App\Models\Poi;
use Exception;
use Illuminate\Console\Command;

class ImportImages extends Command
{
    protected $signature = 'import:images';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $poi = Poi::query()->first();
        if ($poi) {
            echo $poi->id;
            try {
            $poi->addMediaFromUrl('https://altertravel.ru/images/' . $poi->id . '.jpg', 'image/jpeg')
                ->storingConversionsOnDisk('s3')
                ->toMediaCollection('image', 's3');
            } catch (Exception $e) {
                echo $e->getMessage(). '<br>';
            }

            foreach ([1, 2, 3] as $i) {
                try {
                    $poi->addMediaFromUrl('https://altertravel.ru/images/' . $poi->id . '/' . $i . '.jpg', 'image/jpeg')
                        ->storingConversionsOnDisk('s3')
                        ->toMediaCollection('image', 's3');
                } catch (Exception $e) {
                    echo $e->getMessage(). '<br>';
                }
            }
        }
    }
}
