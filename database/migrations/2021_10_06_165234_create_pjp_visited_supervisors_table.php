<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePjpVisitedSupervisorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pjp_visited_supervisors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->nullable();
            $table->integer('pjp_supervisor_id')->nullable();
            $table->integer('visited_pjp_id')->nullable();
            $table->integer('visited_pjp_market_id')->nullable();
            $table->string('remarks')->nullable();
            $table->string('gps_address')->nullable();
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
        Schema::dropIfExists('pjp_visited_supervisors');
    }
}
