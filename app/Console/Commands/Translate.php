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
            ->limit(1)->get();
        foreach ($pois as $poi) {
            $poi->name_en = $translator->translate($poi->name, 'ru', 'en', 'Необходимо перевести заголовок для публикации https://altertravel.ru/poi/' . $poi->id);
            $poi->timestamps = false;
            $poi->save();
        }
    }
}
