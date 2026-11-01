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
        Schema::table('reports', function (Blueprint $table) {
            $table->string('evidence_path')->nullable()->after('condition_level');
            $table->string('evidence_original_name')->nullable()->after('evidence_path');
            $table->string('evidence_mime', 100)->nullable()->after('evidence_original_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['evidence_path', 'evidence_original_name', 'evidence_mime']);
        });
    }
};
