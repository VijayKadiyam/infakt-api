<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCrudeFocusedTargets3Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crude_focused_targets', function (Blueprint $table) {
            $table->string('baby_care', 100)->nullable();
            $table->string('lip_serum', 100)->nullable();
            $table->string('ubtang_range', 100)->nullable();
            $table->string('color_range', 100)->nullable();
            $table->string('hair_range', 100)->nullable();
            $table->string('baby_range', 100)->nullable();
            $table->string('color_care', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('focused_targets', function (Blueprint $table) {
            //
        });
    }
}
