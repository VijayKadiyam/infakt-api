<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersTable24 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name',100)->change();
            $table->string('doj',100)->change();
            $table->string('dob',100)->change();
            $table->string('company_designation_id',100)->change();
            $table->string('company_state_branch_id',100)->change();
            $table->string('pf_no',100)->change();
            $table->string('uan_no',100)->change();
            $table->string('esi_no',100)->change();
            $table->string('salary',100)->change();
            $table->string('employee_code',100)->change();
            $table->string('asm_area',100)->change();
            $table->string('asm_name',100)->change();
            $table->string('uid_no',100)->change();
            $table->string('terms_accepted',100)->change();
            $table->string('company_state_id',100)->change();
            $table->string('unique_id',100)->change();
            $table->string('appointment_letter',100)->change();
            $table->string('contract_expiry',100)->change();
            $table->string('gender',50)->change();
            $table->string('region',50)->change();
            $table->string('state_code',50)->change();
            $table->string('supervisor_id',50)->change();
            
            $table->string('channel', 10)->nullable();
            $table->string('chain_name', 50)->nullable();
            $table->string('billing_code', 50)->nullable();
            $table->string('ba_name', 50)->nullable();
            $table->string('location', 100)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('state', 50)->nullable();
            $table->string('rsm', 50)->nullable();
            $table->string('asm', 50)->nullable();
            $table->string('supervisor_name', 50)->nullable();
            $table->string('store_type', 50)->nullable();
            $table->string('brand', 50)->nullable();
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
