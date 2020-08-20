<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('sales', 'stock_id'))
        {
            Schema::table('sales', function (Blueprint $table)
            {
                $table->dropColumn('stock_id');
            });
        }

        Schema::table('sales', function (Blueprint $table) {
            $table->integer('sku_id')->nullable();
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
