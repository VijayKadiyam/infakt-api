<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUserAssignments2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_assignments', function (Blueprint $table) {
            $table->string('total_questions', 100)->nullable();
            $table->string('answered_questions', 100)->nullable();
            $table->string('right_questions', 100)->nullable();
            $table->string('wrong_questions', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
