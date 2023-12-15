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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('sender');
            $table->foreign('sender')->references('id')->on('users')->onDelete('cascade');
            $table->string('receiver');
            $table->foreign('receiver')->references('id')->on('users')->onDelete('cascade');
            $table->integer('amount');
            $table->string('direction',15);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
