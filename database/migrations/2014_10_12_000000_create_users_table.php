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
            $table->id();
            $table->string('fname')->nullable();
            $table->string('lname')->nullable();
            $table->string('email')->unique();
            $table->string('user_type')->nullable();
            $table->string('department')->nullable();
            $table->boolean('status')->default(false);

            //Entering Phone Number -----------------|
            $table->string('phone_iso2', 3)->nullable();
            $table->string('phone_dial_code', 7)->nullable();
            $table->string('phone_number', 100)->nullable();

            $table->date('dob')->nullable();
            $table->string('designation', 256)->nullable();

            //User Contact Information
            $table->string('country_iso2', 3)->nullable();
            $table->string('country_name', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('user_city', 100)->nullable();
            $table->string('postal_code', 100)->nullable();
            $table->text('user_address')->nullable();

            $table->string('photo', 256)->nullable();
            
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
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
