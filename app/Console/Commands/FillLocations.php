<?php

namespace App\Console\Commands;

use App\Jobs\PoiGeocodeJob;
use App\Models\Poi;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

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
        $pois = Poi::query()
            ->where('show', 1)
            ->whereNull('cant_geocode')
            ->whereNotExists(function($query) {
                $query->select()->from('location_poi')
                    ->whereRaw('`poi`.`id` = `location_poi`.`poi_id`');
            })
            ->limit(950)->get();
        foreach ($pois as $poi) {
            PoiGeocodeJob::dispatch($poi);
        }
        return 0;
    }
}
