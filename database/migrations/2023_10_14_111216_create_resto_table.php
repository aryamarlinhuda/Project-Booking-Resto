<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resto_table', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('decription');
            $table->string('load');
            $table->integer('price');
            $table->foreignId('resto_id')->references('id')->on('table_resto');
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
        Schema::dropIfExists('resto');
    }
}
