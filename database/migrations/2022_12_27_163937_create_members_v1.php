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
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('colors');
            $table->integer('points')->default(0);
            $table->char('status', 1)->default('A');
        });

        Schema::create('members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('icn')->unique();
            $table->string('pin');
            $table->string('password');
            $table->datetime('register_date')->nullable();
            $table->integer('points')->default(0);
            $table->char('status', 1)->default('A');
            $table->foreignId('level_id')->default(1);
            $table->foreignUuid('sponsor_id', 'members')->nullable();
            $table->timestamps();
        });

        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->time('time_begin')->default('06:00');
            $table->integer('duration')->default(30);
            $table->tinyInteger('repeat')->default(1);
            $table->tinyInteger('separation')->default(1);
            $table->tinyInteger('position')->default(1);
            $table->integer('points')->default(0);
            $table->char('status', 1)->default('A');
            $table->foreignId('level_id')->default(1);
            $table->timestamps();
        });

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->datetime('to_date');
            $table->datetime('executed_date')->nullable();
            $table->string('meet_link')->nullable();
            $table->integer('points')->default(0);
            $table->char('status', 1)->default('A');
            $table->foreignUuid('member_id')->constrained();
            $table->foreignId('challenge_id')->constrained();
            $table->foreignId('level_id')->default(1);
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
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('challenges');
        Schema::dropIfExists('members');
        Schema::dropIfExists('levels');
    }
};
