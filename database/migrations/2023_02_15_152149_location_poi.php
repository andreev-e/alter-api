<?php

use App\Models\Location;
use App\Models\Poi;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LocationPoi extends Migration
{
    public function up()
    {
        Schema::create('location_poi', function (Blueprint $table) {
            $table->foreignIdFor(Location::class);
            $table->foreignIdFor(Poi::class);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('location_poi');
    }
}
