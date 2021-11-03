<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePjpVisitedSupervisorExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pjp_visited_supervisor_expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->nullable();
            $table->integer('pjp_visited_supervisor_id')->nullable();
            $table->string('expense_type', 100)->nullable();
            $table->string('travelling_way', 100)->nullable();
            $table->string('transport_mode', 100)->nullable();
            $table->float('km_travelled')->default(false);
            $table->float('amount')->default(false);
            $table->string('description', 100)->nullable();
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
        Schema::dropIfExists('pjp_visited_supervisor_expenses');
    }
}
