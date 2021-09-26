<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChannelCompetitionOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channel_competition_offers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->integer('channel_filter_id')->nullable();
            $table->string('competitor_name', 100)->nullable();
            $table->string('description', 100)->nullable();
            $table->string('top_articles', 100)->nullable();
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
        Schema::dropIfExists('channel_competition_offers');
    }
}
