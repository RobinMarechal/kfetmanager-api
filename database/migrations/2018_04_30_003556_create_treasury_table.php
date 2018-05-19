<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTreasuryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('treasury', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('created_at')->default(\Illuminate\Support\Facades\DB::raw('CURRENT_TIMESTAMP'));

            $table->enum('movement_type', [
                \App\Order::MOVEMENT_TYPE_ORDER,
                \App\Order::MOVEMENT_TYPE_RESTOCKING,
                \App\Order::MOVEMENT_TYPE_CASH_FLOW,
            ])->nullable(false)->default('ORDER');

            $table->enum('movement_operation', [
                \App\Order::MOVEMENT_OPERATION_INSERT,
                \App\Order::MOVEMENT_OPERATION_UPDATE,
                \App\Order::MOVEMENT_OPERATION_DELETE,
            ])->nullable(false)->default('INSERT');

            $table->integer('movement_id')->nullable(false);
            $table->float('balance')->nullable(false);
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('treasury');
    }
}
