<?php

namespace App\Console\Commands;

use App\Models\Location;
use App\Models\Tag;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

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
        $tags = Location::query()
            ->where('scale', 0)
            ->orderBy('count', 'desc')
            ->limit(20)->get();
        foreach ($tags as $tag) {
            echo $tag->id . ' ' . $tag->name;
            $url = 'https://geocode-maps.yandex.ru/1.x/?geocode=' . $tag->name . '&apikey=7483ad1f-f61c-489b-a4e5-815eb06d5961';

            $result = $client->get($url);
            if ($result->getStatusCode() === 200) {

                $file = $result->getBody();

                $file = getBetween($file, "<Envelope>", "</Envelope>");

                $low = explode(" ", getBetween($file, "<lowerCorner>", "</lowerCorner>"));
                $upp = explode(" ", getBetween($file, "<upperCorner>", "</upperCorner>"));

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
                ]);
                echo " - ok\n\r";
                continue;
            }
            echo $result->getStatusCode();
            echo " - fail\n\r";
        }
    }
}
