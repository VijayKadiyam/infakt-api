<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrudeCompetitorDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crude_competitor_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->nullable();
            $table->string('region')->nullable();
            $table->string('channel')->nullable();
            $table->string('chain_name')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('store_code')->nullable();
            $table->string('store_name')->nullable();
            $table->string('ba_name')->nullable();
            $table->string('pms_emp_id')->nullable();
            $table->string('supervisor_name')->nullable();
            $table->string('month')->nullable();
            $table->string('year')->nullable();
            $table->string('amount')->nullable();
            $table->string('bio_tech')->nullable();
            $table->string('derma_fique')->nullable();
            $table->string('nivea')->nullable();
            $table->string('neutrogena')->nullable();
            $table->string('olay')->nullable();
            $table->string('plum')->nullable();
            $table->string('wow')->nullable();
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
        Schema::dropIfExists('crude_competitor_datas');
    }
}
