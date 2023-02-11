<?php

namespace App\Jobs;

use App\Models\Poi;
use App\Models\Tag;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
//        if ($curl = curl_init()) {
//            curl_setopt($curl, CURLOPT_URL, $url);
//            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//            curl_setopt($curl, CURLOPT_AUTOREFERER, true);
//            curl_setopt($curl, CURLOPT_TIMEOUT, 10);
//            curl_setopt($curl, CURLOPT_HEADER, true);
//            curl_setopt($curl, CURLOPT_FRESH_CONNECT, false);
//
//            $file = curl_exec($curl);
//        }

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

            if ($country) {
                $this->addTag($country, 1);
            }
            if ($adm_area) {
                $this->addTag($adm_area, 2);
            }
            if ($locality) {
                $this->addTag($locality, 3);
            }
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

    private function addTag(string $tagName, int $type): void
    {
        $tag = Tag::query()->firstOrCreate([
            'name' => $tagName,
            'type' => $type
        ]);

        $this->poi->tags()->attach($tag);
            Tag::query()->where('name', $tag)
                ->where('type', $type)->count();
    }
}
