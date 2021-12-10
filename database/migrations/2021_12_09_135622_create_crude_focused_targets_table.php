<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrudeFocusedTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crude_focused_targets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company_id')->nullable();
            $table->string('region')->nullable();
            $table->string('channel')->nullable();
            $table->string('chain_name')->nullable();
            $table->string('billing_code')->nullable();
            $table->string('store_code')->nullable();
            $table->string('store_name')->nullable();
            $table->string('location')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('rsm')->nullable();
            $table->string('asm')->nullable();
            $table->string('supervisor_code')->nullable();
            $table->string('supervisor_name')->nullable();
            $table->string('brand')->nullable();
            $table->string('ba_status')->nullable();
            $table->string('store_status')->nullable();
            $table->integer('month')->nullable();
            $table->integer('year')->nullable();
            $table->integer('target')->nullable();
            $table->integer('achieved')->nullable();
            $table->integer('baby')->nullable();
            $table->integer('baby_Kit')->nullable();
            $table->integer('baby_Oil')->nullable();
            $table->integer('bath_salt')->nullable();
            $table->integer('bathing')->nullable();
            $table->integer('body')->nullable();
            $table->integer('body_butter')->nullable();
            $table->integer('body_cream')->nullable();
            $table->integer('body_lotion')->nullable();
            $table->integer('body_scrub')->nullable();
            $table->integer('body_wash')->nullable();
            $table->integer('capsules')->nullable();
            $table->integer('cleanser')->nullable();
            $table->integer('cleansing')->nullable();
            $table->integer('combo_kit')->nullable();
            $table->integer('conditioner')->nullable();
            $table->integer('cream')->nullable();
            $table->integer('diaper')->nullable();
            $table->integer('dusting_powder')->nullable();
            $table->integer('face_cream')->nullable();
            $table->integer('face_free')->nullable();
            $table->integer('face_Mask')->nullable();
            $table->integer('face_Milk')->nullable();
            $table->integer('face_scrub')->nullable();
            $table->integer('face_Serum')->nullable();
            $table->integer('face_Spot')->nullable();
            $table->integer('face_toner')->nullable();
            $table->integer('facewash')->nullable();
            $table->integer('freebies')->nullable();
            $table->integer('gel')->nullable();
            $table->integer('gift_pack')->nullable();
            $table->integer('hair_care')->nullable();
            $table->integer('hair_Mask')->nullable();
            $table->integer('hair_Oil')->nullable();
            $table->integer('hair_Serum')->nullable();
            $table->integer('hand_cream')->nullable();
            $table->integer('hygine')->nullable();
            $table->integer('kajal')->nullable();
            $table->integer('kids_body')->nullable();
            $table->integer('lip_balm')->nullable();
            $table->integer('lotion')->nullable();
            $table->integer('mask')->nullable();
            $table->integer('moisturizer')->nullable();
            $table->integer('mosquito_protection')->nullable();
            $table->integer('oil')->nullable();
            $table->integer('oral',)->nullable();
            $table->integer('otc')->nullable();
            $table->integer('peeling')->nullable();
            $table->integer('serum')->nullable();
            $table->integer('shampoo')->nullable();
            $table->integer('sheet_mask')->nullable();
            $table->integer('sub_cat')->nullable();
            $table->integer('sun_cat')->nullable();
            $table->integer('sun_care')->nullable();
            $table->integer('sunscreen')->nullable();
            $table->integer('tablets')->nullable();
            $table->integer('toner')->nullable();
            $table->integer('yogurt_for')->nullable();
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
        Schema::dropIfExists('crude_focused_targets');
    }
}
