<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCrudeFocusedTargets4Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crude_focused_targets', function (Blueprint $table) {
            $table->renameColumn('ubtang_range', 'ubtan_range');
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
