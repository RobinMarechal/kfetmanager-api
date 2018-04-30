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
            $table->enum('movement_type', ['ORDER', 'CASH_FLOW', 'RESTOCKING'])->nullable(false)->default('ORDER');
            $table->enum('movement_operation', ['INSERT', 'UPDATE', 'DELETE'])->nullable(false)->default('INSERT');
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
