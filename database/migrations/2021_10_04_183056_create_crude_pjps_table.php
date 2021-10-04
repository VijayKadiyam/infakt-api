<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrudePjpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crude_pjps', function (Blueprint $table) {
            $table->increments('id');
            $table->string('visit_date',50)->nullable();
            $table->string('day',50)->nullable();
            $table->string('region',50)->nullable();
            $table->string('location',100)->nullable();
            $table->string('market_working_details',225)->nullable();
            $table->string('joint_working_with',100)->nullable();
            $table->string('employee_code',100)->nullable();
            $table->string('supervisor_name',100)->nullable();
            $table->string('remarks',100)->nullable();
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
        Schema::dropIfExists('crude_pjps');
    }
}
