<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCrudeUsers2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crude_users', function (Blueprint $table) {
            $table->string('ba_status', 100)->nullable();
        });

        if (Schema::hasColumn('crude_users', 'location'))
        {
            Schema::table('crude_users', function (Blueprint $table) {
                $table->string('location', 200)->nullable()->change();
            });
        } else {
            Schema::table('crude_users', function (Blueprint $table) {
                $table->string('location', 200)->nullable();
            });
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crude_users', function (Blueprint $table) {
            //
        });
    }
}
