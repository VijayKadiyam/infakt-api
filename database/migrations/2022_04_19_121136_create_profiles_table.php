<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('visit_call', 100)->nullable();
            $table->string('mark_in_lat', 100)->nullable();
            $table->string('mark_in_lng', 100)->nullable();
            $table->string('mark_out_lat', 100)->nullable();
            $table->string('mark_out_lng', 100)->nullable();
            $table->string('date', 100)->nullable();
            $table->integer('mobile_1')->nullable();
            $table->string('email_1', 100)->nullable();
            $table->string('photo_1_path', 100)->nullable();
            $table->string('company_name', 100)->nullable();
            $table->string('industry', 100)->nullable();
            $table->integer('employee_size')->nullable();
            $table->string('turnover', 100)->nullable();
            $table->string('head_office', 100)->nullable();
            $table->string('address', 100)->nullable();
            $table->string('website', 100)->nullable();
            $table->integer('contact_1_mobile')->nullable();
            $table->string('contact_1_email', 100)->nullable();
            $table->integer('contact_2_mobile')->nullable();
            $table->string('contact_2_email', 100)->nullable();
            $table->string('contact_1_name', 100)->nullable();
            $table->string('contact_2_name', 100)->nullable();
            $table->string('product_offered', 100)->nullable();
            $table->string('deal_date', 100)->nullable();
            $table->string('agreement_date', 100)->nullable();
            $table->string('terms', 100)->nullable();
            $table->string('remarks', 100)->nullable();
            $table->string('next_meeting_date', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_deleted')->default(false);
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
        Schema::dropIfExists('profiles');
    }
}
