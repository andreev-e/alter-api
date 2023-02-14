<?php

namespace App\Console\Commands;

use App\Models\Route;
use App\Models\User;
use Exception;
use Illuminate\Console\Command;

class ImportUserImages extends Command
{
    protected $signature = 'import:user-images';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $left = User::query()->whereNull('image_processed')
            ->count();
        $users = User::query()->select('id', 'username')->whereNull('image_processed')
            ->limit(1000)->get();
        foreach ($users as $user) {
            echo 'Left:' . $left-- . ' ' . $user->id . "\n\r";

            $user->clearMediaCollection('user-image');

            try {
                $user->addMediaFromUrl('https://altertravel.ru/authors/' . $user->username . '.jpg',
                    'image/jpeg')
                    ->storingConversionsOnDisk('s3')
                    ->toMediaCollection('user-image', 's3');
            } catch (Exception $e) {
                echo $e->getMessage() . "\n\r";
            }

            try {
                $user->addMediaFromUrl('https://altertravel.ru/authors/' . $user->username . '_full.jpg',
                    'image/jpeg')
                    ->storingConversionsOnDisk('s3')
                    ->toMediaCollection('user-image', 's3');
            } catch (Exception $e) {
                echo $e->getMessage() . "\n\r";
            }
            $user->image_processed = 1;
            $user->save();
        }

    }
}
