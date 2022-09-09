<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCrudeStudents1Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crude_students', function (Blueprint $table) {
            $table->string('optional_classcode_1')->nullable();
            $table->string('optional_classcode_2')->nullable();
            $table->string('optional_classcode_3')->nullable();
            $table->string('optional_classcode_4')->nullable();
            $table->string('optional_classcode_5')->nullable();
            $table->string('optional_classcode_6')->nullable();
            $table->string('optional_classcode_7')->nullable();
            $table->string('optional_classcode_8')->nullable();
            $table->string('optional_classcode_9')->nullable();
            $table->string('optional_classcode_10')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crude_students', function (Blueprint $table) {
            //
        });
    }
}
