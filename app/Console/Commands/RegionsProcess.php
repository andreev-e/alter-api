<?php

namespace App\Console\Commands;

use App\Models\Location;
use App\Models\Tag;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class RegionsProcess extends Command
{
    protected $signature = 'regions:process';

    protected $description = 'Regions process';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(Client $client)
    {
        $locations = Location::query()
            ->where(function(Builder $query) {
                $query->orWhere('scale', 0)
                    ->orWhereNull('name_en');
            })
            ->orderBy('count', 'desc')
            ->limit(900)->get();
        foreach ($locations as $tag) {
            echo $tag->id . ' ' . $tag->name . ' ' . $tag->count . ' ' . $tag->url;
            $url = 'https://geocode-maps.yandex.ru/1.x/?geocode='
                . $tag->name . ', ' . $tag->parentLocation?->name . '&lang=en_US&apikey=7483ad1f-f61c-489b-a4e5-815eb06d5961';
            $result = $client->get($url);
            if ($result->getStatusCode() === 200) {

                $file = $result->getBody();

                $xml = simplexml_load_string($file, "SimpleXMLElement", LIBXML_NOCDATA);
                $json = json_encode($xml);
                $array = json_decode($json, true);


                $data = array_key_exists('GeoObject', $array['GeoObjectCollection']['featureMember']) ? $array['GeoObjectCollection']['featureMember']['GeoObject']:  $array['GeoObjectCollection']['featureMember'][0]['GeoObject'];

                $low = explode(" ", $data['boundedBy']['Envelope']['lowerCorner']);
                $upp = explode(" ", $data['boundedBy']['Envelope']['upperCorner']);

                $lng = ((float)$low[0] + (float)$upp[0]) / 2;
                $lat = ((float)$low[1] + (float)$upp[1]) / 2;

                $for_scale = max(abs($low[0] - $upp[0]), abs($low[1] - $upp[1]));

                $scale = 0;
                if ($for_scale > 150) {
                    $scale = 3;
                }
                if ($for_scale < 150 && $for_scale > 10) {
                    $scale = 5;
                }
                if ($for_scale < 10 && $for_scale > 7) {
                    $scale = 7;
                }
                if ($for_scale < 7 && $for_scale > 3) {
                    $scale = 8;
                }
                if ($for_scale < 3 && $for_scale > 2) {
                    $scale = 9;
                }
                if ($for_scale < 2 && $for_scale > 1) {
                    $scale = 10;
                }
                if ($for_scale < 1 && $for_scale > 0) {
                    $scale = 11;
                }

                $tag->update([
                    'scale' => $scale,
                    'lat' => $lat,
                    'lng' => $lng,
                    'name_en' => $data['name'],
                ]);
                echo " - ok\n\r";
                continue;
            }
            echo $result->getStatusCode();
            echo " - fail\n\r";
        }
    }
}
