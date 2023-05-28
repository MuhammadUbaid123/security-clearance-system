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
        Schema::table('approved_bies', function (Blueprint $table) {
            $table->string('request_status')->after('clear_req_id')->default('pending');
            $table->text('miscellaneous')->after('comments')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('approved_bies', function (Blueprint $table) {
            $table->dropColumn(['request_status', 'miscellaneous']);
        });
    }
};
