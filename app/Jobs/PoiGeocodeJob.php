<?php

namespace App\Jobs;

use App\Models\Location;
use App\Models\Poi;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class PoiGeocodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private Poi $poi)
    {
        $this->queue = 'geocode';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Client $client)
    {
        error_reporting(E_ERROR | E_PARSE);
        $url = 'https://geocode-maps.yandex.ru/1.x/?geocode=' . $this->poi->lng . ',' . $this->poi->lat . '&apikey=7483ad1f-f61c-489b-a4e5-815eb06d5961';

        $result = $client->get($url);

        if ($result->getStatusCode() !== 200) {
            $this->fail();
        }

        $file = $result->getBody();

        if ($file) {
            $file = getBetween($file, "<featureMember xmlns=\"http://www.opengis.net/gml\">",
                "</featureMember>");
            $country = str_replace("xml:lang=\"ru\">", "", getBetween($file, "<CountryName>", "</CountryName>"));
            $adm_area = str_replace("xml:lang=\"ru\">", "",
                getBetween($file, "<AdministrativeAreaName>", "</AdministrativeAreaName>"));
            $locality = str_replace("xml:lang=\"ru\">", "",
                getBetween($file, "<LocalityName>", "</LocalityName>"));
            if (trim($locality) === "") {
                $locality = str_replace("xml:lang=\"ru\">", "",
                    getBetween($file, "<SubAdministrativeAreaName>", "</SubAdministrativeAreaName>"));
            }

            if ($country != '') {
                $countryLocation = $this->addLocation($country, 1);
            }
            if ($countryLocation && $adm_area != '') {
                $admAreaLocation = $this->addLocation($adm_area, 2, $countryLocation);
            }
            if ($admAreaLocation && $locality != '') {
                $localityLocation = $this->addLocation($locality, 3, $admAreaLocation);
            }

            $this->poi->locations()->sync(array_filter([$countryLocation, $admAreaLocation, $localityLocation]));
        }

    }

    private function addLocation(string $locationName, int $type, int $parent = 0): int
    {
        $location = Location::query()->firstOrCreate([
            'name' => $locationName,
        ], [
            'parent' => $parent,
            'type' => $type,
            'url' => Str::slug($locationName),
            'lat' => $this->poi->lat,
            'lng' => $this->poi->lng,
            'count' => 1,
        ]);

        return $location->id;
    }
}
