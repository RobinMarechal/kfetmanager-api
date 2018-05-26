<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductRestockingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_restocking', function (Blueprint $table) {
            $table->integer('product_id')->unsigned();
            $table->integer('restocking_id')->unsigned();
            $table->float('quantity')->default(0);
            $table->primary(['product_id', 'restocking_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_restocking');
    }
}
