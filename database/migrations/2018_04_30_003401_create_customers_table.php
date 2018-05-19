<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->softDeletes();
            $table->string('email', 50)->nullable(false);
            $table->string('name', 40)->nullable(false);
            $table->enum('year', ['PEIP','THIRD','FOURTH','FIFTH','PHD','PROFESSOR','OTHER'])->default('OTHER')->nullable(false);
            $table->enum('department', ['DI', 'DII', 'DMS', 'DEE', 'PEIP', 'DAE', 'OTHER'])->default('OTHER')->nullable(false);
            $table->float('balance')->nullable(false)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
