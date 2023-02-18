<?php

namespace App\Console\Commands;

use App\Jobs\PoiGeocodeJob;
use App\Models\Poi;
use Illuminate\Console\Command;

class FillLocations extends Command
{
    protected $signature = 'fill:locations';

    protected $description = 'Fill locations';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $pois = Poi::query()->oldest()->limit(950)->get();
        foreach ($pois as $poi) {
            PoiGeocodeJob::dispatch($poi);
        }
        return 0;
    }
}
