<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('master_user_id')->comment('マスターユーザーID');
            $table->datetime('game_start_time')->nullable()->comment('ゲーム開始日時');
            $table->integer('phase')->default(0)->comment('フェーズ(0: 準備中, 1: 役職選択中, 2: プレイ中)');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('master_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
