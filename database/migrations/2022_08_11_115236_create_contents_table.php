<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('content_name', 100)->nullable();
            $table->string('content_type', 100)->nullable();
            $table->integer('written_by_id')->nullable();
            $table->string('reading_time', 100)->nullable();
            $table->string('content_metadata', 100)->nullable();
            $table->longText('easy_content')->nullable();
            $table->longText('med_content')->nullable();
            $table->longText('hard_content')->nullable();
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
        Schema::dropIfExists('contents');
    }
}
