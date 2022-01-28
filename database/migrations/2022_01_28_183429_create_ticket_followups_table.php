<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketFollowupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_followups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->integer('ticket_id')->nullable();
            $table->string('description', 100)->nullable();
            $table->string('imagepath1')->nullable();
            $table->string('imagepath2', 100)->nullable();
            $table->string('imagepath3', 100)->nullable();
            $table->string('imagepath4', 100)->nullable();
            $table->integer('replied_by_id')->nullable();
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
        Schema::dropIfExists('ticket_followups');
    }
}
