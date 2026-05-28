<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_questions', function (Blueprint $table) {
            $table->decimal('marks', 6, 2)->nullable()->after('subject');
            $table->text('review_notes')->nullable()->after('notes');
            $table->foreignId('reviewed_by')->nullable()->after('submitted_by')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
        });

        // Change status enum from ['pending','reviewed'] to ['pending','revision_needed','approved']
        DB::statement("ALTER TABLE exam_questions DROP COLUMN status");
        Schema::table('exam_questions', function (Blueprint $table) {
            $table->enum('status', ['pending', 'revision_needed', 'approved'])
                ->default('pending')->after('reviewed_at');
        });
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE exam_questions DROP COLUMN status");
        Schema::table('exam_questions', function (Blueprint $table) {
            $table->enum('status', ['pending', 'reviewed'])->default('pending');
        });
        Schema::table('exam_questions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn(['marks', 'review_notes', 'reviewed_at']);
        });
    }
};
