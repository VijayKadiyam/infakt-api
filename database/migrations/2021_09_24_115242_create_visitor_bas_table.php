<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitorBasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitor_bas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('visitor_id')->nullable();
            $table->integer('ba_id')->nullable();
            $table->string('ba_status', 100)->nullable();
            $table->boolean('is_grooming')->nullable();
            $table->integer('grooming_value')->nullable();
            $table->boolean('is_uniform')->nullable();
            $table->boolean('is_planogram')->nullable();
            $table->integer('product_knowledge_value')->nullable();
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
        Schema::dropIfExists('visitor_bas');
    }
}
