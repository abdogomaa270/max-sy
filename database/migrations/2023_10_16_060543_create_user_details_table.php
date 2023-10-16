<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //name elNsaba fatherName  motherName&Nasba  gender
    //el raqam el watny
    //el qid
    //el warith
    //phone
    //el amana

    //balad madina mantka birthdate
    //3noan eleqama --> balade madina mantaka street
    //el btaka
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->string('user_id')->primary();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // Add the other details attributes here
            $table->string('gender')->nullable();
            $table->string('phone')->nullable();
            $table->string('inheritor')->nullable();
            $table->string('national_number',15)->nullable();
            $table->string('qid',15)->nullable();
            $table->string('amana',30)->nullable();
            $table->string('birth_country',40)->nullable();
            $table->string('birth_city',40)->nullable();
            $table->string('birth_street',100)->nullable();
            $table->string('birthday',11);
            $table->string('man7_history',11);
            $table->string('address_country',40)->nullable();
            $table->string('address_city',40)->nullable();
            $table->string('address_street',100)->nullable();
            $table->string('identity')->nullable(); // البطاقه


            // ...
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
