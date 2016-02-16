<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItems extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        //
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('key');
            $table->string('value')->nullable();
            $table->string('object_type');
            $table->integer('object_id');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        //
        Schema::drop('items');
    }
}
