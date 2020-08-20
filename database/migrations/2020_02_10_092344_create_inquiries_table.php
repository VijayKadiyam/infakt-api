<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInquiriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->string('date')->nullable();
            $table->string('company_name')->nullable();
            $table->string('industry')->nullable();
            $table->string('employee_size')->nullable();
            $table->string('turnover')->nullable();
            $table->string('head_office')->nullable();
            $table->string('address')->nullable();
            $table->string('website')->nullable();
            $table->string('contact_person_1')->nullable();
            $table->string('designation')->nullable();
            $table->string('landline')->nullable();
            $table->string('mobile_1')->nullable();
            $table->string('mobile_2')->nullable();
            $table->string('email_1')->nullable();
            $table->string('email_2')->nullable();
            $table->string('contact_person_2')->nullable();
            $table->string('contact_person_3')->nullable();
            $table->string('date_of_contact')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inquiries');
    }
}
