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
            $table->string('full_name')->nullable();
            $table->string('father_name')->nullable();
            $table->string('surname')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('pan_no')->nullable();
            $table->string('adhaar_no')->nullable();
            $table->string('pre_room_no')->nullable();
            $table->string('pre_building')->nullable();
            $table->string('pre_area')->nullable();
            $table->string('pre_road')->nullable();
            $table->string('pre_city')->nullable();
            $table->string('pre_state')->nullable();
            $table->string('pre_pincode')->nullable();
            $table->string('pre_mobile')->nullable();
            $table->string('pre_email')->nullable();
            $table->string('per_room_no')->nullable();
            $table->string('per_building')->nullable();
            $table->string('per_area')->nullable();
            $table->string('per_road')->nullable();
            $table->string('per_city')->nullable();
            $table->string('per_state')->nullable();
            $table->string('per_pincode')->nullable();
            $table->string('per_mobile')->nullable();
            $table->string('per_email')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_acc_no')->nullable();
            $table->string('bank_ifsc_code')->nullable();
            $table->string('bank_branch_name')->nullable();
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
