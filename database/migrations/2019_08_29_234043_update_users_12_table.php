<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsers12Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('full_name', 100)->nullable();
            $table->string('father_name', 100)->nullable();
            $table->string('surname', 100)->nullable();
            $table->string('mother_name', 100)->nullable();
            $table->string('marital_status', 100)->nullable();
            $table->string('pan_no', 100)->nullable();
            $table->string('adhaar_no', 100)->nullable();
            $table->string('pre_room_no', 100)->nullable();
            $table->string('pre_building', 100)->nullable();
            $table->string('pre_area', 100)->nullable();
            $table->string('pre_road', 100)->nullable();
            $table->string('pre_city', 100)->nullable();
            $table->string('pre_state', 100)->nullable();
            $table->string('pre_pincode', 100)->nullable();
            $table->string('pre_mobile', 100)->nullable();
            $table->string('pre_email', 100)->nullable();
            $table->string('per_room_no', 100)->nullable();
            $table->string('per_building', 100)->nullable();
            $table->string('per_area', 100)->nullable();
            $table->string('per_road', 100)->nullable();
            $table->string('per_city', 100)->nullable();
            $table->string('per_state', 100)->nullable();
            $table->string('per_pincode', 100)->nullable();
            $table->string('per_mobile', 100)->nullable();
            $table->string('per_email', 100)->nullable();
            $table->string('blood_group', 100)->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_acc_no', 100)->nullable();
            $table->string('bank_ifsc_code', 100)->nullable();
            $table->string('bank_branch_name', 100)->nullable();
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
