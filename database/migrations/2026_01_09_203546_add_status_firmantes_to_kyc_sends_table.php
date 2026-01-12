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
            $table->string('status_firmante01')->default('WAITING')->after('kyc_status');
            $table->string('status_firmante02')->default('WAITING')->after('status_firmante01');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kyc_sends', function (Blueprint $table) {
            $table->dropColumn(['status_firmante01', 'status_firmante02']);
        });
    }
};
