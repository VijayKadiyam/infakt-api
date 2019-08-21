<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCompanies3Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('welcome_email_subject')->nullable();
            $table->string('welcome_email_body')->nullable();
            $table->string('df_1_email_subject')->nullable();
            $table->string('df_1_email_body')->nullable();
            $table->string('df_2_email_subject')->nullable();
            $table->string('df_2_email_body')->nullable();
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
