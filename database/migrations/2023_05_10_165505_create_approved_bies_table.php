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
        Schema::create('approved_bies', function (Blueprint $table) {
            $table->id()->from(9000);

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('name')->nullable();

            $table->unsignedBigInteger('clear_req_id');
            $table->foreign('clear_req_id')->references('id')->on('clearance_requests')->onDelete('cascade');

            $table->text('comments')->nullable();;

            $table->boolean('status')->default(false);


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approved_bies');
    }
};
