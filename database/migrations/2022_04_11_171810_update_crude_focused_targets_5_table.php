<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCrudeFocusedTargets5Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crude_focused_targets', function (Blueprint $table) {
            $table->string('tea_tree_range', 100)->nullable();
            $table->string('charcoal_range', 100)->nullable();
            $table->string('lipstick', 100)->nullable();
            $table->string('aqua_range', 100)->nullable();
            $table->string('ultra_spf50_80ml', 100)->nullable();
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
