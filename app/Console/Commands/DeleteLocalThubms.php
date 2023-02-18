<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Storage;

class DeleteLocalThubms extends Command
{
    protected $signature = 'delete:local-thumbs';

    protected $description = 'Delete local thumbs';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $files = Storage::disk('public')->listContents('tmp-img');
        foreach ($files as $file) {
            if ($file['type'] === 'file' && $file['timestamp'] < now()->subMinutes(10)->getTimestamp()) {
                Storage::disk('public')->delete($file['path']);
            }
        }
        $media = Media::query()->where('custom_properties', 'LIKE', '%temporary_url%')->get();
        foreach ($media as $image) {
            /*  @var Media $image */
            $image->forgetCustomProperty('temporary_url');
            $image->save();
        }
        return 0;
    }
}
