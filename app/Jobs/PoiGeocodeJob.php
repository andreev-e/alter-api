<?php

namespace App\Jobs;

use App\Models\Poi;
use App\Models\Tag;
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
        $url = 'https://geocode-maps.yandex.ru/1.x/?geocode=' . $this->poi->lng . ',' . $this->poi->lng . '&apikey=7483ad1f-f61c-489b-a4e5-815eb06d5961';

        $result = $client->get($url);

        if ($result->getStatusCode() !== 200) {
            $this->fail();
        }

        $file = $result->getBody();

        if ($file) {
            $file = $this->getBetween($file, "<featureMember xmlns=\"http://www.opengis.net/gml\">",
                "</featureMember>");
            $country = str_replace("xml:lang=\"ru\">", "", $this->getBetween($file, "<CountryName>", "</CountryName>"));
            $adm_area = str_replace("xml:lang=\"ru\">", "",
                $this->getBetween($file, "<AdministrativeAreaName>", "</AdministrativeAreaName>"));
            $locality = str_replace("xml:lang=\"ru\">", "",
                $this->getBetween($file, "<LocalityName>", "</LocalityName>"));
            if (trim($locality) === "") {
                $locality = str_replace("xml:lang=\"ru\">", "",
                    $this->getBetween($file, "<SubAdministrativeAreaName>", "</SubAdministrativeAreaName>"));
            }

            if ($country != '') {
                $countryTag = $this->addTag($country, 1);
            }
            if ($countryTag && $adm_area != '') {
                $admAreaTag = $this->addTag($adm_area, 2, $countryTag);
            }
            if ($admAreaTag && $locality != '') {
                $localityTag = $this->addTag($locality, 3, $admAreaTag);
            }

            $this->poi->tags()->sync(array_filter([$countryTag, $admAreaTag, $localityTag]));
        }

    }

    private function getBetween($content, $start, $end): string
    {
        $r = explode($start, $content);
        if (isset($r[1])) {
            $r = explode($end, $r[1]);
            return $r[0];
        }
        return '';
    }

    private function addTag(string $tagName, int $type, int $parent = 0): int
    {
        $tag = Tag::query()->firstOrCreate([
            'name' => $tagName,
            'TYPE' => $type,
        ], [
            'parent' => $parent,
            'url' => Str::slug($tagName),
            'lat' => $this->poi->lat,
            'lng' => $this->poi->lng,
        ]);

        return $tag->id;
    }
}
