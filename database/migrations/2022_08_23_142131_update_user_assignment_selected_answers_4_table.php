<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUserAssignmentSelectedAnswers4Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_assignment_selected_answers', function (Blueprint $table) {
            $table->longText('option5')->nullable();
            $table->longText('option6')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_assignment_selected_answers', function (Blueprint $table) {
            //
        });
    }
}
