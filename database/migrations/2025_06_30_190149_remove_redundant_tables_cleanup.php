<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This migration cleans up redundant tables and adds performance indexes.
     */
    public function up(): void
    {
        // Remove redundant feedback table (functionality moved to contacts table)
        if (Schema::hasTable('feedback')) {
            Schema::dropIfExists('feedback');
        }

        // Optionally remove cv_templates table if not using dynamic templates
        // Uncomment the next lines if you want to remove it:
        // if (Schema::hasTable('cv_templates')) {
        //     Schema::dropIfExists('cv_templates');
        // }

        // Add performance indexes to existing tables
        
        // CVs table indexes
        if (Schema::hasTable('cvs')) {
            Schema::table('cvs', function (Blueprint $table) {
                // Check if indexes don't already exist
                $indexes = DB::select("SHOW INDEX FROM cvs WHERE Key_name = 'idx_user_template'");
                if (empty($indexes)) {
                    $table->index(['user_id', 'selected_template'], 'idx_user_template');
                }
                
                $indexes = DB::select("SHOW INDEX FROM cvs WHERE Key_name = 'idx_user_created'");
                if (empty($indexes)) {
                    $table->index(['user_id', 'created_at'], 'idx_user_created');
                }
                
                $indexes = DB::select("SHOW INDEX FROM cvs WHERE Key_name = 'idx_finalized'");
                if (empty($indexes)) {
                    $table->index('is_finalized', 'idx_finalized');
                }
            });
        }

        // Work experiences table indexes
        if (Schema::hasTable('work_experiences')) {
            Schema::table('work_experiences', function (Blueprint $table) {
                $indexes = DB::select("SHOW INDEX FROM work_experiences WHERE Key_name = 'idx_cv_dates'");
                if (empty($indexes)) {
                    $table->index(['cv_id', 'start_date', 'end_date'], 'idx_cv_dates');
                }
            });
        }

        // Educations table indexes
        if (Schema::hasTable('educations')) {
            Schema::table('educations', function (Blueprint $table) {
                $indexes = DB::select("SHOW INDEX FROM educations WHERE Key_name = 'idx_cv_dates'");
                if (empty($indexes)) {
                    $table->index(['cv_id', 'start_date', 'end_date'], 'idx_cv_dates');
                }
            });
        }

        // Skills table indexes
        if (Schema::hasTable('skills')) {
            Schema::table('skills', function (Blueprint $table) {
                $indexes = DB::select("SHOW INDEX FROM skills WHERE Key_name = 'idx_cv_name'");
                if (empty($indexes)) {
                    $table->index(['cv_id', 'name'], 'idx_cv_name');
                }
            });
        }

        // Interests table indexes
        if (Schema::hasTable('interests')) {
            Schema::table('interests', function (Blueprint $table) {
                $indexes = DB::select("SHOW INDEX FROM interests WHERE Key_name = 'idx_cv_id'");
                if (empty($indexes)) {
                    $table->index('cv_id', 'idx_cv_id');
                }
            });
        }

        // Contacts table indexes
        if (Schema::hasTable('contacts')) {
            Schema::table('contacts', function (Blueprint $table) {
                $indexes = DB::select("SHOW INDEX FROM contacts WHERE Key_name = 'idx_email_created'");
                if (empty($indexes)) {
                    $table->index(['email', 'created_at'], 'idx_email_created');
                }
            });
        }

        // User activities table indexes
        if (Schema::hasTable('user_activities')) {
            Schema::table('user_activities', function (Blueprint $table) {
                $indexes = DB::select("SHOW INDEX FROM user_activities WHERE Key_name = 'idx_user_type'");
                if (empty($indexes)) {
                    $table->index(['user_id', 'type'], 'idx_user_type');
                }
                
                $indexes = DB::select("SHOW INDEX FROM user_activities WHERE Key_name = 'idx_created'");
                if (empty($indexes)) {
                    $table->index('created_at', 'idx_created');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate feedback table if needed
        if (!Schema::hasTable('feedback')) {
            Schema::create('feedback', function (Blueprint $table) {
                $table->increments('id');
                $table->string('contact_name')->nullable();
                $table->string('contact_email')->nullable();
                $table->string('feedback_subject');
                $table->text('feedback_message');
                $table->timestamp('submitted_at')->useCurrent();
            });
        }

        // Remove the indexes we added
        if (Schema::hasTable('cvs')) {
            Schema::table('cvs', function (Blueprint $table) {
                $table->dropIndex('idx_user_template');
                $table->dropIndex('idx_user_created');
                $table->dropIndex('idx_finalized');
            });
        }

        if (Schema::hasTable('work_experiences')) {
            Schema::table('work_experiences', function (Blueprint $table) {
                $table->dropIndex('idx_cv_dates');
            });
        }

        if (Schema::hasTable('educations')) {
            Schema::table('educations', function (Blueprint $table) {
                $table->dropIndex('idx_cv_dates');
            });
        }

        if (Schema::hasTable('skills')) {
            Schema::table('skills', function (Blueprint $table) {
                $table->dropIndex('idx_cv_name');
            });
        }

        if (Schema::hasTable('interests')) {
            Schema::table('interests', function (Blueprint $table) {
                $table->dropIndex('idx_cv_id');
            });
        }

        if (Schema::hasTable('contacts')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->dropIndex('idx_email_created');
            });
        }

        if (Schema::hasTable('user_activities')) {
            Schema::table('user_activities', function (Blueprint $table) {
                $table->dropIndex('idx_user_type');
                $table->dropIndex('idx_created');
            });
        }
    }
};
