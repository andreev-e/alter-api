<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NullableColumnsPoiTable extends Migration
{
    private $columns = [
        'route_o',
        'route',
        'addon',
        'links',
        'rating',
        'user_rating',
    ];

    public function up()
    {
        Schema::table('poi', function(Blueprint $table) {
            foreach ($this->columns as $column) {
                $table->text($column)->nullable()->change();
            }
        });
    }

    public function down()
    {
    }
}
