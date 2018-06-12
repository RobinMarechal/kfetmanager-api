<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('category_menu', function (Blueprint $table) {
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('menu_id')
                  ->references('id')
                  ->on('menus')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });

        Schema::table('customer_group', function (Blueprint $table) {
            $table->foreign('customer_id')
                  ->references('id')
                  ->on('customers')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('group_id')
                  ->references('id')
                  ->on('groups')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });

        Schema::table('discounts', function (Blueprint $table) {
            $table->foreign('group_id')
                  ->references('id')
                  ->on('groups')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('subcategory_id')
                  ->references('id')
                  ->on('subcategories')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('customer_id')
                  ->references('id')
                  ->on('customers')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            $table->foreign('menu_id')
                  ->references('id')
                  ->on('menus')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });

        Schema::table('order_product', function (Blueprint $table) {
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('order_id')
                  ->references('id')
                  ->on('orders')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreign('subcategory_id')
                  ->references('id')
                  ->on('subcategories')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });

        Schema::table('product_restocking', function (Blueprint $table) {
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('restocking_id')
                  ->references('id')
                  ->on('restockings')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });

        Schema::table('subcategories', function (Blueprint $table) {
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('category_menu', function(Blueprint $table){
            $table->dropForeign('category_menu_category_id_foreign');
            $table->dropForeign('category_menu_menu_id_foreign');
        });

        Schema::table('customer_group', function(Blueprint $table){
            $table->dropForeign('customer_group_customer_id_foreign');
            $table->dropForeign('customer_group_group_id_foreign');
        });

        Schema::table('discounts', function(Blueprint $table){
            $table->dropForeign('discounts_group_id_foreign');
            $table->dropForeign('discounts_subcategory_id_foreign');
        });

        Schema::table('orders', function(Blueprint $table){
            $table->dropForeign('orders_menu_id_foreign');
            $table->dropForeign('orders_customer_id_foreign');
        });

        Schema::table('products', function(Blueprint $table){
            $table->dropForeign('products_subcategory_id_foreign');
        });

        Schema::table('product_restocking', function(Blueprint $table){
            $table->dropForeign('product_restocking_product_id_foreign');
            $table->dropForeign('product_restocking_restocking_id_foreign');
        });

        Schema::table('subcategories', function(Blueprint $table){
            $table->dropForeign('subcategories_category_id_foreign');
        });
    }
}
