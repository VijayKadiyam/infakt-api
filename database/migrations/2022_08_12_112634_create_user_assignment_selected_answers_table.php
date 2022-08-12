<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAssignmentSelectedAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_assignment_selected_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('assignment_id')->nullable();
            $table->integer('assignment_question_id')->nullable();
            $table->integer('selected_option_sr_no')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->integer('marks_obtained')->default(false);
            $table->string('documentpath', 100)->nullable();
            $table->longText('description')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_assignment_selected_answers');
    }
}
