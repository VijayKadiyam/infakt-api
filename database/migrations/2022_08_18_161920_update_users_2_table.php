<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsers2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('doj', 'joining_date');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('dob', 'first_name');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('company_designation_id', 'last_name');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('company_state_branch_id', 'gender');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('pf_no', 'image_path');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('uan_no', 'id_given_by_school');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('esi_no', 'contact_number');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_deleted')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
