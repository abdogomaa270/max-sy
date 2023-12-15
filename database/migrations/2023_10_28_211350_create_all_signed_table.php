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
        Schema::create('all_signed', function (Blueprint $table) {
            $table->id();
            $table->string('parent_id');
            $table->string('child_id');
            $table->string('direction');
            $table->unsignedInteger('level');
            $table->timestamps();
            $table->foreign('parent_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('child_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('all_signed');
    }
};
