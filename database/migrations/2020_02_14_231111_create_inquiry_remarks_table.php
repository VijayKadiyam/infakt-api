<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInquiryRemarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inquiry_remarks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('inquiry_id');
            $table->integer('user_id');
            $table->string('meeting_time')->nullable();
            $table->string('date')->nullable();
            $table->string('venue')->nullable();
            $table->string('note')->nullable();
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
        Schema::dropIfExists('inquiry_remarks');
    }
}
