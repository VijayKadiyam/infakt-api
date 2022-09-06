<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAboutUses1Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('about_uses', function (Blueprint $table) {
            $table->longText('tagline')->nullable()->change();
            $table->longText('info')->nullable()->change();
            $table->longText('info_1')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('about_uses', function (Blueprint $table) {
            //
        });
    }
}
