<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// В varchar(255) не помещаются custom_properties с длинным автором фото
class WidenMediaJsonColumns extends Migration
{
    private const COLUMNS = ['manipulations', 'custom_properties', 'generated_conversions', 'responsive_images'];

    public function up()
    {
        Schema::table('media', function (Blueprint $table) {
            foreach (self::COLUMNS as $column) {
                $table->text($column)->change();
            }
        });
    }

    public function down()
    {
        Schema::table('media', function (Blueprint $table) {
            foreach (self::COLUMNS as $column) {
                $table->string($column)->change();
            }
        });
    }
}
