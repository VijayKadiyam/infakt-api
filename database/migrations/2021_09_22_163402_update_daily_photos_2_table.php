<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDailyPhotos2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('daily_photos', function (Blueprint $table) {
            $table->string('image_path1',100)->nullable();
            $table->string('image_path2',100)->nullable();
            $table->string('image_path3',100)->nullable();
            $table->string('image_path4',100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('daily_photos', function (Blueprint $table) {
            //
        });
    }
}
