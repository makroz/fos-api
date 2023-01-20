<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('abilities')->nullable();
            $table->char('status', 1)->default('A');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('rol');
            $table->foreignId('role_id')->default(1);
        });

        //crear tabla habilities que su id sea un string de 10 caracteres
        Schema::create('abilities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 10)->unique();
            $table->string('description')->nullable();
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
        Schema::dropIfExists('abilities');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role_id');
            $table->string('rol')->default('user');
        });
        Schema::dropIfExists('roles');
    }
};
