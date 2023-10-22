<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCart extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_cart', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resto_id')->references('id')->on('table_resto');
            $table->integer('total_table');
            $table->integer('total_price');
            $table->foreignId('carted_by')->references('id')->on('users');
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
        Schema::dropIfExists('table_cart');
    }
}
