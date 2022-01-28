<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCrudeFocusedTargets1Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crude_focused_targets', function (Blueprint $table) {
            $table->string('rice_range',100)->nullable();
            $table->string('almond_range',100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crude_focused_targets', function (Blueprint $table) {
            //
        });
    }
}
