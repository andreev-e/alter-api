<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnsTags extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::table('tags', function (Blueprint $table) {
//            $table->renameColumn('ID', 'id');
//            $table->renameColumn('NAME', 'name');
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->renameColumn('id', 'ID');
            $table->renameColumn('name', 'NAME');
        });
    }
}
