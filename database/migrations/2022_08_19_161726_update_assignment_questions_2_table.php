<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAssignmentQuestions2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assignment_questions', function (Blueprint $table) {
            $table->longText('option1')->nullable();
            $table->longText('option2')->nullable();
            $table->longText('option3')->nullable();
            $table->longText('option4')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assignment_questions', function (Blueprint $table) {
            //
        });
    }
}
