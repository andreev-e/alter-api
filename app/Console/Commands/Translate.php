<?php

namespace App\Console\Commands;

use App\Models\Poi;
use App\Services\Translation\TranslationInterface;
use Illuminate\Console\Command;

class Translate extends Command
{
    protected $signature = 'translate:all';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(TranslationInterface $translator)
    {
        $pois = Poi::query()->whereNull('name_en')
            ->orderBy('views', 'desc')
            ->limit(1000)->cursor();
        foreach ($pois as $poi) {
            $poi->name_en = $translator->translate($poi->name, 'ru', 'en');
            $poi->timestamps = false;
            $poi->save();
            echo $poi->id . ' ' . $poi->name . ' ' . $poi->name_en . PHP_EOL;
        }
    }
}
