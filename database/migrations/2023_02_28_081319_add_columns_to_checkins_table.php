<?php

use App\Models\Checkin;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToCheckinsTable extends Migration
{
    public function up()
    {
        Schema::table('checkins', function (Blueprint $table) {
            $table->timestamps();
            $table->foreignIdFor(User::class);
        });

        Checkin::query()->each(function($checkin) {
            $user = User::query()->where('username', strtolower($checkin->userid))->first();
            if ($user) {
                $checkin->user_id = $user->id;
                $checkin->save();
            }
        });
    }

    public function down()
    {
        Schema::table('checkins', function (Blueprint $table) {
            $table->dropTimestamps();
            $table->dropColumn('user_id');
        });
    }
}
