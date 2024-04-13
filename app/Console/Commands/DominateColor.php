<?php

namespace App\Console\Commands;

use App\Models\Poi;
use Exception;
use Illuminate\Console\Command;
use Intervention\Image\Facades\Image;
use Storage;

class DominateColor extends Command
{
    protected $signature = 'media:dominate-color';

    protected $description = 'Count main color of the image';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $pois = Poi::query()->whereNull('dominatecolor')
            ->limit(10)
            ->cursor();

        foreach ($pois as $poi) {
            /*  @var Poi $poi */
            $image = $poi->media->first();
            $poi->dominatecolor = '-';
            if ($image) {
                try {
                    dump($poi->id);
                    $imageData = Storage::disk('public')->get($image->getPath('thumb'));
                    $img = Image::make($imageData);
                    $img->resize(1,1);
                    $rgb = $img->pickColor(0,0);
                    $poi->dominatecolor = sprintf("#%02x%02x%02x", $rgb[0], $rgb[1], $rgb[2]);;
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }
            $poi->timestamps = false;
            $poi->save();
        }
    }
}
