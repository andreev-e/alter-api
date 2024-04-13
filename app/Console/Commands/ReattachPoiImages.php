<?php

namespace App\Console\Commands;

use App\Models\Poi;
use Exception;
use Illuminate\Console\Command;

class ReattachPoiImages extends Command
{
    protected $signature = 'reattach';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $files = scandir(public_path('storage').'/OLD');
        foreach ($files as $file) {
            if (is_numeric(pathinfo($file, PATHINFO_FILENAME))) {
                $id = pathinfo($file, PATHINFO_FILENAME);
                $poi = Poi::query()->find($id);
                if ($poi instanceof Poi) {
                    echo 'Processing ' . $id . "\n\r";
                    $poi->clearMediaCollection('image');
                    try {
                        $poi->addMedia(public_path('storage').'/OLD/'.$file)
                            ->storingConversionsOnDisk('public')
                            ->toMediaCollection('image', 'public');
                    } catch (Exception $e) {
                        echo $e->getMessage(). "\n\r";
                    }
                    $poi->save();
                }
            }
        }
    }
}
