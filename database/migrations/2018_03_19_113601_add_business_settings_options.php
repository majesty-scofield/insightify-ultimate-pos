<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business', function (Blueprint $table) {
            $table->boolean('enable_brand')->default(true)->after('keyboard_shortcuts');
            $table->boolean('enable_category')->default(true)->after('enable_brand');
            $table->boolean('enable_sub_category')->default(true)->after('enable_category');
            $table->boolean('enable_price_tax')->default(true)->after('enable_sub_category');
            $table->boolean('enable_purchase_status')->nullable()->default(true)->after('enable_price_tax');
            $table->integer('default_unit')->nullable()->after('enable_purchase_status');
            $table->string('repair_settings')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business', function (Blueprint $table) {
            //
        });
    }
};
