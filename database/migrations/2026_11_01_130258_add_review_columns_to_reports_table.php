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
            $table->foreignId('reviewer_id')
                ->nullable()
                ->after('area_id')
                ->constrained('users')
                ->nullOnDelete();
            $table->text('review_note')->nullable()->after('status');
            $table->timestamp('reviewed_at')->nullable()->after('review_note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reviewer_id');
            $table->dropColumn(['review_note', 'reviewed_at']);
        });
    }
};
