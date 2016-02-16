<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModuleDepModules extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        //
        Schema::create('module_dep_modules', function (Blueprint $table) {
            $table->integer('module_id')->unsigned()->index();
            $table->foreign('module_id')->references('id')->on('modules');

            $table->integer('dep_module_id')->unsigned()->index();
            $table->foreign('dep_module_id')->references('id')->on('modules');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        //
        Schema::drop('module_dep_modules');
    }
}
