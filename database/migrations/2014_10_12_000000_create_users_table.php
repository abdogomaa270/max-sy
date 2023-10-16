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
        Schema::create('users', function (Blueprint $table) {
            $table->string('id')->primary();

            $table->string('name');
            $table->string('nickname');
            $table->string('father_name');
            $table->string('mother_name');
            $table->string('email')->nullable()->unique();
            $table->string('password');
            $table->string('bocket_password')->nullable();
            $table->string('left_user_id')->nullable();
            $table->string('right_user_id')->nullable();
            $table->string('parent')->nullable();
            $table->unsignedInteger('left_points')->default(0);
            $table->unsignedInteger('right_points')->default(0);
            $table->unsignedBigInteger('total_points')->default(0);
            $table->boolean('ispunished')->default(false);
            $table->rememberToken();
            $table->timestamps();

//            $table->foreign('left_user_id')->references('id')->on('users')
//                ->onUpdate('cascade')->onDelete('cascade');
//            $table->foreign('right_user_id')->references('id')->on('users')
//                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
