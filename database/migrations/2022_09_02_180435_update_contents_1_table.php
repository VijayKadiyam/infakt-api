<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateContents1Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->renameColumn('written_by_id', 'written_by_name');
        });
        Schema::table('contents', function (Blueprint $table) {
            $table->integer('grade_id')->nullable();
            $table->string('learning_outcome')->nullable();
            $table->string('for_school_type')->nullable();
            $table->integer('board_id')->nullable();
            $table->string('specific_to')->nullable();
            $table->integer('school_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contents', function (Blueprint $table) {
            //
        });
    }
}
