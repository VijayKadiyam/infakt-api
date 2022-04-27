<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProfiles1Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('service_fees', 100)->nullable();
            $table->string('recruitment_fees', 100)->nullable();
            $table->string('onboarding_fees', 100)->nullable();
            $table->string('service_fee_on_reimbursements', 100)->nullable();
            $table->string('service_fee_on_incentive', 100)->nullable();
            $table->string('service_fee_on_ad_hoc', 100)->nullable();
            $table->string('absorption_fee', 100)->nullable();
            $table->string('agency_fee_for_junior_level', 100)->nullable();
            $table->string('agency_fee_for_middle_level', 100)->nullable();
            $table->string('agency_fee_for_senior_level', 100)->nullable();
            $table->string('hajiri_per_user_per_month', 100)->nullable();
            $table->string('dastavej_per_user_per_month', 100)->nullable();
            $table->string('sales_per_user_per_month', 100)->nullable();
            $table->string('merchandising_per_user_per_month', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profiles', function (Blueprint $table) {
            //
        });
    }
}
