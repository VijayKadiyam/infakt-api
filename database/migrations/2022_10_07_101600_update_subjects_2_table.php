<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSubjects2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->renameColumn('imagepath', 'imagepath_1');
        });
        Schema::table('subjects', function (Blueprint $table) {
            $table->string('imagepath_2')->nullable();
            $table->string('imagepath_3')->nullable();
            $table->string('imagepath_4')->nullable();
            $table->string('imagepath_5')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subjects', function (Blueprint $table) {
            //
        });
    }
}
