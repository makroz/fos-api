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
        Schema::create('lives', function (Blueprint $table) {
            $table->id();
            $table->datetime('open_date');
            $table->datetime('close_date')->nullable();
            $table->string('meet_link')->nullable();
            $table->integer('cant')->default(0);
            $table->integer('cant_asist')->default(0);
            $table->integer('cant_aproved')->default(0);
            $table->integer('cant_cancel')->default(0);
            $table->char('status', 1)->default('A');
            $table->foreignUuid('user_id')->constrained();
            $table->foreignId('challenge_id')->constrained();
            $table->timestamps();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('meet_link');
            $table->dropColumn('executed_date');
            $table->foreignId('live_id')->nullable();
            $table->datetime('start_date')->nullable();
            $table->datetime('ended_date')->nullable();
            $table->foreignUuid('user_id')->nullable();
            $table->char('type', 1)->default('L');
        });

        Schema::table('challenges', function (Blueprint $table) {
            $table->char('type', 1)->default('L');
            $table->string('video_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lives');
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['live_id',  'start_date', 'ended_date', 'user_id', 'type']);
            $table->string('meet_link')->nullable();
            $table->datetime('executed_date')->nullable();
        });
        Schema::table('challenges', function (Blueprint $table) {
            $table->dropColumn(['type', 'video_link']);
        });
    }
};
