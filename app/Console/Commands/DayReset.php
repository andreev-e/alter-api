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
        Poi::query()->update(['views_today' => 0, 'timestamps' => false]);
    }
}
