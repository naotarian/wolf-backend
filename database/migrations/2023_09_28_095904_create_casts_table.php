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
        Schema::create('casts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('room_user_id')->comment('ルームユーザーテーブルID');
            $table->unsignedBigInteger('position_id')->comment('役職ID');
            $table->boolean('confirmed')->default(false)->comment('ゲーム開始前の確認');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('room_user_id')->references('id')->on('room_user')->onDelete('cascade');
            $table->foreign('position_id')->references('id')->on('positions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('casts');
    }
};
