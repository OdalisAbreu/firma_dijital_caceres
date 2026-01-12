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
        Schema::table('kyc_sends', function (Blueprint $table) {
            $table->string('name_client')->nullable()->after('email');
            $table->string('lastname_client')->nullable()->after('name_client');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kyc_sends', function (Blueprint $table) {
            $table->dropColumn(['name_client', 'lastname_client']);
        });
    }
};
