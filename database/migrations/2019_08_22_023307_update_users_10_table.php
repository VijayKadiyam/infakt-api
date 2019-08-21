<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsers10Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('birth_certificate_path')->nullable();
            $table->string('passport_path')->nullable();
            $table->string('driving_license_path')->nullable();
            $table->string('school_leaving_certificate_path')->nullable();
            $table->string('mark_sheet_path')->nullable();
            $table->string('experience_certificate_path')->nullable();
            $table->string('prev_emp_app_letter_path')->nullable();
            $table->string('form_2_path')->nullable();
            $table->string('form_11_path')->nullable();
            $table->string('graduity_form_path')->nullable();
            $table->string('app_letter_path')->nullable();
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
