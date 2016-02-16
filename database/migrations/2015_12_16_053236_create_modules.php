<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModules extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        //
        Schema::create('modules', function (Blueprint $table) {
            $table->increments('id');       // 自增 ID
            $table->string('name');         // 名称
            $table->string('description');  // 描述
            $table->integer('product_id');  // 模块属于 product
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        //
        Schema::drop('modules');
    }
}
