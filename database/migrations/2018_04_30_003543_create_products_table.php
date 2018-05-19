<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->softDeletes();
            $table->string('name', 30)->nullable(false);
            $table->integer('subcategory_id')->nullable(false)->unsigned();
            $table->float('purchase_price')->nullable(false)->default(0);
            $table->float('price')->nullable(false);
            $table->integer('stock')->nullable(false)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
