<?php

namespace App\Console\Commands;

use App\Jobs\PoiGeocodeJob;
use App\Models\Location;
use App\Models\Poi;
use App\Models\Tag;
use Illuminate\Console\Command;

class FillLocations extends Command
{
    protected $signature = 'fill:locations';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $pois = Poi::query()->limit(1)->get();
        foreach ($pois as $poi) {
            echo $poi->id;
            PoiGeocodeJob::dispatch($poi);
        }
        return 0;
    }
}
