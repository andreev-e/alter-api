<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NullableColumnsPoiTable extends Migration
{
    private array $columns = [
        'route_o',
        'route',
        'addon',
        'links',
        'photo_text1',
        'photo_text2',
        'photo_text3',
        'rating',
        'ytb',
        'updated',
        'dominatecolor',
        'near',
        'near_date',
        'instagram',
        'instagram_time',
        'panoramio',
        'panoramio_time',
        'tel',
        'metki',
        'metki_time',
    ];

    private array $zeros = [
        'views',
        'views_month',
        'views_today',
        'user_rating',
        'bayan',
        'comments',
    ];


    public function up()
    {
        Schema::table('poi', function(Blueprint $table) {
            foreach ($this->columns as $column) {
                $table->text($column)->nullable()->change();
            }
            foreach ($this->zeros as $column) {
                $table->integer($column)->default(0)->change();
            }
        });
    }

    public function down()
    {
    }
}
