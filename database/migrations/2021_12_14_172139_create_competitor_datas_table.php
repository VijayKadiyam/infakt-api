<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompetitorDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('competitor_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('competitor', 100)->nullable();
            $table->string('amount', 100)->nullable();
            $table->string('month', 100)->nullable();
            $table->string('year', 100)->nullable();
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
        Schema::dropIfExists('competitor_datas');
    }
}
