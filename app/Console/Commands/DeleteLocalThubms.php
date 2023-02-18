<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
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
            if ($file['type'] === 'file' && $file['timestamp'] < now()->getTimestamp()) {
                Storage::disk('public')->delete($file['path']);
            }
        }
        return 0;
    }
}
