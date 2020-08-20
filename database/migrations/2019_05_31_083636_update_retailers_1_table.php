<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRetailers1Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('retailers', 'company_id'))
        {
            Schema::table('retailers', function (Blueprint $table)
            {
                $table->dropColumn('company_id');
            });
        }

        Schema::table('retailers', function (Blueprint $table) {
            $table->integer('reference_plan_id')->nullable();
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
