<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEpaperBookmarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('epaper_bookmarks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id',)->nullable();
            $table->integer('user_id',)->nullable();
            $table->integer('toi_article_id',)->nullable();
            $table->integer('et_article_id',)->nullable();
            $table->boolean('is_deleted',)->nullable()->default(false);
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
        Schema::dropIfExists('epaper_bookmarks');
    }
}
