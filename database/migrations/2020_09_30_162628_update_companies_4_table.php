<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCompanies4Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          Schema::table('companies', function (Blueprint $table) {
            $table->integer('attendance')->default(0);
            $table->integer('leave')->default(0);
            $table->integer('expenses')->default(0);
            $table->integer('orders')->default(0);
            $table->integer('recruiters')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
