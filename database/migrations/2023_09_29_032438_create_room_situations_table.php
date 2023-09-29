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
        Schema::create('room_situations', function (Blueprint $table) {
            $table->id();
            $table->uuid('room_id');
            $table->boolean('is_night')->default(1)->comment('ルームの状態(夜:1昼:0)');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_situations');
    }
};
