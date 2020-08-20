<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrudeSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crude_salaries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->nullable();
            $table->string('emp_id')->nullable();
            $table->string('basic_salary')->nullable();
            $table->string('dearness_allowance')->nullable();
            $table->string('hra')->nullable();
            $table->string('conveyance_allowance')->nullable();
            $table->string('mobile_charges')->nullable();
            $table->string('communication')->nullable();
            $table->string('medical_allowance')->nullable();
            $table->string('variable_pay')->nullable();
            $table->string('special_allowance')->nullable();
            $table->string('bonus_y')->nullable();
            $table->string('incentives')->nullable();
            $table->string('expense_reimbursement')->nullable();
            $table->string('other_earnings')->nullable();
            $table->string('bonus_m')->nullable();
            $table->string('incentive_1')->nullable();
            $table->string('lta')->nullable();
            $table->string('travelling')->nullable();
            $table->string('daily_allowance')->nullable();
            $table->string('other_allowance')->nullable();
            $table->string('educational_allowance')->nullable();
            $table->string('deputation_allowance')->nullable();
            $table->string('leave_salary')->nullable();
            $table->string('m_special')->nullable();
            $table->string('fixed_allowance_1')->nullable();
            $table->string('gross_salary')->nullable();
            $table->string('other_deduction')->nullable();
            $table->string('lwf')->nullable();
            $table->string('tds')->nullable();
            $table->string('esic')->nullable();
            $table->string('profession')->nullable();
            $table->string('provident_fund')->nullable();
            $table->string('total_deductions')->nullable();
            $table->string('net_pay')->nullable();
            $table->string('epf_employer')->nullable();
            $table->string('eps_employer')->nullable();
            $table->string('esic_employer')->nullable();
            $table->string('mlwf')->nullable();
            $table->string('edli_employer')->nullable();
            $table->string('pf_admin_charge')->nullable();
            $table->string('m_bonus')->nullable();
            $table->string('m_m_bonus')->nullable();
            $table->string('m_bonus_m')->nullable();
            $table->string('insurance')->nullable();
            $table->string('wc_policy')->nullable();
            $table->string('ctc')->nullable();
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
        Schema::dropIfExists('crude_salaries');
    }
}
