  <?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCompanies2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('pds_word_path')->nullable();
            $table->string('pds_pdf_path')->nullable();
            $table->string('form_2_word_path')->nullable();
            $table->string('form_2_pdf_path')->nullable();
            $table->string('form_11_word_path')->nullable();
            $table->string('form_11_pdf_path')->nullable();
            $table->string('pf_word_path')->nullable();
            $table->string('pf_pdf_path')->nullable();
            $table->string('esic_benefit_word_path')->nullable();
            $table->string('esic_benefit_pdf_path')->nullable();
            $table->string('insurance_claim_word_path')->nullable();
            $table->string('insurance_claim_pdf_path')->nullable();
            $table->string('salary_slip_word_path')->nullable();
            $table->string('salary_slip_pdf_path')->nullable();
            $table->string('pms_policies_word_path')->nullable();
            $table->string('pms_policies_pdf_path')->nullable();
            $table->string('act_of_misconduct_word_path')->nullable();
            $table->string('act_of_misconduct_pdf_path')->nullable();
            $table->string('uan_activation_word_path')->nullable();
            $table->string('uan_activation_pdf_path')->nullable();
            $table->string('online_claim_word_path')->nullable();
            $table->string('online_claim_pdf_path')->nullable();
            $table->string('kyc_update_word_path')->nullable();
            $table->string('kyc_update_pdf_path')->nullable();
            $table->string('graduity_form_word_path')->nullable();
            $table->string('graduity_form_pdf_path')->nullable();
            $table->longText('welcome_note')->nullable();

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
