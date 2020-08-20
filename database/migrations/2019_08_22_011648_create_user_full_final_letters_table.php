<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserFullFinalLettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_full_final_letters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->longText('letter');
            $table->integer('signed')->default(0);
            $table->string('sign_path')->nullable();
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
        Schema::dropIfExists('user_full_final_letters');
    }
}
