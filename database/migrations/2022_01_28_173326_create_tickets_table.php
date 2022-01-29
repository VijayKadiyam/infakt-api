<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->string('title', 100)->nullable();
            $table->string('description', 100)->nullable();
            $table->string('type', 100)->nullable();
            $table->string('status', 100)->nullable();
            $table->integer('assigned_to_id')->nullable();
            $table->string('imagepath1')->nullable();
            $table->string('imagepath2', 100)->nullable();
            $table->string('imagepath3', 100)->nullable();
            $table->string('imagepath4', 100)->nullable();
            $table->integer('created_by_id')->nullable();
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
        Schema::dropIfExists('tickets');
    }
}
