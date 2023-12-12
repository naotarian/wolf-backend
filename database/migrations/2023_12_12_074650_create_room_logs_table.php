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
        Schema::create('room_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('room_id')->comment('ルームID');
            $table->integer('day_number')->comment('○日目');
            $table->integer('turn')->comment('1: 朝, 2: 夜');
            $table->string('people_voted')->nullable()->comment('投票された人のユーザーID');
            $table->string('person_bitten')->nullable()->comment('噛まれた人のユーザーID');
            $table->string('person_firing')->nullable()->comment('発砲された人のユーザーID');
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
        Schema::dropIfExists('room_logs');
    }
};
