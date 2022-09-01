<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentMetadatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_metadatas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('content_id')->nullable();
            $table->string('metadata_type')->nullable();
            $table->string('color_class')->nullable();
            $table->string('selected_text')->nullable();
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
        Schema::dropIfExists('content_metadatas');
    }
}
