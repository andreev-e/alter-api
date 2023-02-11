<?php

namespace App\Console\Commands;

use App\Models\Poi;
use Illuminate\Console\Command;

class MonthReset extends Command
{
    protected $signature = 'reset:month';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Poi::query()->update(['views_month' => 0]);
    }
}
