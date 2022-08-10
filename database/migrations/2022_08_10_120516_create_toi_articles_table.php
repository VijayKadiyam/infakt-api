<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateToiArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('toi_articles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('toi_xml_id')->nullable();
            $table->longText('edition_name')->nullable();
            $table->string('story_id')->nullable();
            $table->string('story_date')->nullable();
            $table->longText('headline')->nullable();
            $table->longText('byline')->nullable();
            $table->longText('category')->nullable();
            $table->longText('drophead')->nullable();
            $table->longText('content')->nullable();

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
        Schema::dropIfExists('toi_articles');
    }
}
