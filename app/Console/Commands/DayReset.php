<?php

namespace App\Console\Commands;

use App\Models\Poi;
use Illuminate\Console\Command;

class DayReset extends Command
{
    protected $signature = 'reset:day';

    protected $description = 'Day reset';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Массовый update через Eloquent-билдер сам проставляет updated_at = now(),
        // из-за чего у всех точек каждый день появлялась ложная дата «Обновлено».
        // toBase() выполняет тот же запрос без неявного обновления таймстемпов.
        Poi::query()->toBase()->update(['views_today' => 0]);
    }
}
