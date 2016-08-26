<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateApplogsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applogs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 50)->nullable();
            $table->string('model', 50)->nullable();
            $table->integer('foreign_id')->nullable();
            $table->string('name', 255)->nullable();
            $table->string('classname', 255)->nullable();
            $table->string('traitname', 255)->nullable();
            $table->string('functionname', 255)->nullable();
            $table->string('filename', 255)->nullable();
            $table->integer('linenumber')->nullable();
            $table->string('ipaddr', 32)->nullable();
            $table->longText('message')->nullable();
            $table->longText('details')->nullable();
            $table->string('created_by', 255)->nullable();
            $table->string('updated_by', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('applogs');
    }
}
