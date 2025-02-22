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
        Schema::create('mfg_recipes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('sub_unit_id')->nullable();
            $table->unsignedBigInteger('variation_id')->nullable();
            $table->double('extra_cost')->default(0);
            $table->double('final_price')->default(0);
            $table->double('ingredients_cost')->default(0);
            $table->string('instructions')->nullable();
            $table->integer('total_quantity')->nullable();
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
        Schema::dropIfExists('mfg_recipes');
    }
};
