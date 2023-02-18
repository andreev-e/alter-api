<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->integer('parent')->default(0);
            $table->integer('type')->default(0);
            $table->integer('count')->default(0);
            $table->float('lat');
            $table->float('lng');
            $table->integer('scale')->default(0);
            $table->string('name_en')->nullable();
            $table->string('name_dat_ed')->nullable();
            $table->string('name_rod_ed')->nullable();
            $table->string('name_predlozh_ed')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locations');
    }
}
