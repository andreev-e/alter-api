<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeNullableTagsTable extends Migration
{
    private array $columns = [
        'lat',
        'lng',
        'NAME_ROD',
        'NAME_en',
        'NAME_en',
        'flag',
        'NAME_DAT_ED',
        'NAME_ROD_ED',
        'NAME_PREDLOZH_ED',
    ];

    private array $zeros = [
        'COUNT',
        'scale',
        'parent',
    ];


    public function up()
    {
        Schema::table('tags', function(Blueprint $table) {
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
