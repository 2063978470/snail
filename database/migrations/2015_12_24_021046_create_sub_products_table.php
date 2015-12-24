<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('sub_products', function(Blueprint $table) {
            $table->increments('id');       // 自增 ID
            $table->integer('product_id');  // 产品 ID
            $table->string('name');         // 子产品名称
            $table->string('description');  // 子产品描述
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::drop('sub_products');
    }
}
