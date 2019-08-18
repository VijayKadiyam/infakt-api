<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsers9Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('contract_expiry')->nullable();
            $table->string('resume_path')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('residential_proof_path')->nullable();
            $table->string('education_proof_path')->nullable();
            $table->string('pan_card_path')->nullable();
            $table->string('adhaar_card_path')->nullable();
            $table->string('esi_card_path')->nullable();
            $table->string('cancelled_cheque_path')->nullable();
            $table->string('salary_slip_path')->nullable();
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
