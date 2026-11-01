<?php

namespace App\Console\Commands;

use App\Models\Poi;
use Illuminate\Console\Command;

class MonthReset extends Command
{
    protected $signature = 'reset:month';

    protected $description = 'Month reset';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // См. DayReset: обновляем счётчик без затирания updated_at у точек.
        Poi::query()->toBase()->update(['views_month' => 0]);
    }
}
