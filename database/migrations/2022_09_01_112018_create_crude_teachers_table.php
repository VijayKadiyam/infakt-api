<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrudeTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crude_teachers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->nullable();
            $table->integer('role_id')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('id_given_by_school')->nullable();
            $table->string('email')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('gender')->nullable();
            $table->string('active')->nullable();
            $table->string('joining_date')->nullable();
            $table->string('classcode_1')->nullable();
            $table->string('classcode_2')->nullable();
            $table->string('classcode_3')->nullable();
            $table->string('classcode_4')->nullable();
            $table->string('classcode_5')->nullable();
            $table->string('classcode_6')->nullable();
            $table->string('classcode_7')->nullable();
            $table->string('classcode_8')->nullable();
            $table->string('classcode_9')->nullable();
            $table->string('classcode_10')->nullable();
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
        Schema::dropIfExists('crude_teachers');
    }
}
