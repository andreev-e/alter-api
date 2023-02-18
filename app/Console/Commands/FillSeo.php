<?php

namespace App\Console\Commands;

use App\Models\Location;
use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class FillSeo extends Command
{
    protected $signature = 'fill:seo';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $locations = Location::query()->select('id', 'name')
            ->where(function(Builder $query) {
                $query->orWhereNull('name_en')
                    ->orWhereNull('name_dat_ed')
                    ->orWhereNull('name_rod_ed')
                    ->orWhereNull('code')
                    ->orWhereNull('name_predlozh_ed');
            })
            ->get();
        foreach ($locations as $location) {
            $tag = Tag::query()->where('name', $location->name)->first();
            if ($tag) {
                $location->update([
                    'name_en' => $tag->NAME_en,
                    'name_dat_ed' => $tag->NAME_DAT_ED,
                    'name_rod_ed' => $tag->NAME_ROD_ED,
                    'name_predlozh_ed' => $tag->NAME_PREDLOZH_ED,
                    'code' => $tag->code,
                ]);
                dump(implode([
                    'name_en' => $tag->NAME_en,
                    'name_dat_ed' => $tag->NAME_DAT_ED,
                    'name_rod_ed' => $tag->NAME_ROD_ED,
                    'name_predlozh_ed' => $tag->NAME_PREDLOZH_ED,
                    'code' => $tag->code,
                ]));
            }

        }
    }
}
