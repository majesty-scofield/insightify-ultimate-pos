<?php

use App\Models\System;
use Illuminate\Database\Migrations\Migration;

class AddMinTerminationAlertDays extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        System::insert(['key' => 'package_expiry_alert_days', 'value' => 5]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('', function (Blueprint $table) {

        // });
    }
}
