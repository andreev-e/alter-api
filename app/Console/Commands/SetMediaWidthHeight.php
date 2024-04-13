<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Intervention\Image\Facades\Image;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Storage;

class SetMediaWidthHeight extends Command
{
    protected $signature = 'media:calculate-dimensions';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $media = Media::query()
            ->where('custom_properties', '[]')
            ->where('model_type', '=', 'App\Models\User')
            ->cursor();
        foreach ($media as $image) {
            /*  @var Media $image */
            try {
                $imageData = Storage::disk('public')->get($image->getPath('thumb'));
                $img = Image::make($imageData);
                $image->setCustomProperty('width', $img->width());
                $image->setCustomProperty('height', $img->height());
                var_dump($image->id);
            } catch (Exception $e) {
                echo $e->getMessage();
            }

            $image->save();

        }

        $media = Media::query()
            ->where('custom_properties', '[]')
            ->where('model_type', '<>', 'App\Models\User')
            ->cursor();
        foreach ($media as $image) {
            /*  @var Media $image */
            try {
                $imageData = Storage::disk('public')->get($image->getPath('full'));
                $img = Image::make($imageData);
                $image->setCustomProperty('width', $img->width());
                $image->setCustomProperty('height', $img->height());
                var_dump($image->id);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            if ($image->model->author) {
                $image->setCustomProperty('author', $image->model->author);
            }
            if ($image->model->username) {
                $image->setCustomProperty('author', $image->model->username);
            }

            $image->save();

        }
    }
}
