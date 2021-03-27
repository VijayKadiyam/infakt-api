<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->integer('user_id');
            $table->integer('asset_id')->nullable();
            $table->string('status')->nullable();
            $table->text('description')->nullable();
            $table->string('date')->nullable();
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
        Schema::dropIfExists('asset_statuses');
    }
}
