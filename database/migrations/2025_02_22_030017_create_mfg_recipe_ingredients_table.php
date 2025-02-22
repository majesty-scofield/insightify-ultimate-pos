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
        Schema::create('mfg_recipe_ingredients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mfg_recipe_id')->nullable();
            $table->unsignedBigInteger('sub_unit_id')->nullable();
            $table->unsignedBigInteger('variation_id')->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('waste_percent')->nullable();
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
        Schema::dropIfExists('mfg_recipe_ingredients');
    }
};
