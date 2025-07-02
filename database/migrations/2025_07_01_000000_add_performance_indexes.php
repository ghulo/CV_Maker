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
        // Add indexes to CVs table for performance optimization
        Schema::table('cvs', function (Blueprint $table) {
            // Index for user queries (most common)
            $table->index(['user_id', 'created_at'], 'idx_cvs_user_created');
            
            // Index for email searches
            $table->index('email', 'idx_cvs_email');
            
            // Index for template filtering
            $table->index('selected_template', 'idx_cvs_template');
            
            // Index for finalized status queries
            $table->index('is_finalized', 'idx_cvs_finalized');
            
            // Composite index for analytics queries
            $table->index(['user_id', 'views', 'downloads'], 'idx_cvs_analytics');
        });

        // Add indexes to work_experiences table
        Schema::table('work_experiences', function (Blueprint $table) {
            // Index for CV relationship queries
            $table->index(['cv_id', 'start_date'], 'idx_work_exp_cv_date');
            
            // Index for job title searches
            $table->index('job_title', 'idx_work_exp_title');
            
            // Index for company searches
            $table->index('company', 'idx_work_exp_company');
            
            // Index for current job queries
            $table->index('is_current_job', 'idx_work_exp_current');
        });

        // Add indexes to educations table
        Schema::table('educations', function (Blueprint $table) {
            // Index for CV relationship queries
            $table->index(['cv_id', 'start_date'], 'idx_education_cv_date');
            
            // Index for institution searches
            $table->index('institution', 'idx_education_institution');
            
            // Index for degree searches
            $table->index('degree', 'idx_education_degree');
            
            // Index for current studies
            $table->index('is_current', 'idx_education_current');
        });

        // Add indexes to skills table
        Schema::table('skills', function (Blueprint $table) {
            // Index for CV relationship queries
            $table->index(['cv_id', 'level'], 'idx_skills_cv_level');
            
            // Index for skill name searches
            $table->index('name', 'idx_skills_name');
            
            // Index for skill category filtering
            $table->index('category', 'idx_skills_category');
            
            // Composite index for skill analytics
            $table->index(['name', 'level', 'years_of_experience'], 'idx_skills_analytics');
        });

        // Add indexes to interests table
        Schema::table('interests', function (Blueprint $table) {
            // Index for CV relationship queries
            $table->index('cv_id', 'idx_interests_cv');
        });

        // Add indexes to user_profiles table
        Schema::table('user_profiles', function (Blueprint $table) {
            // Index for user relationship
            $table->index('user_id', 'idx_user_profiles_user');
            
            // Index for public profile queries
            $table->index('is_public', 'idx_user_profiles_public');
            
            // Composite index for location-based queries
            $table->index(['location', 'is_public'], 'idx_user_profiles_location');
        });

        // Add indexes to user_activities table
        Schema::table('user_activities', function (Blueprint $table) {
            // Index for activity type filtering
            $table->index('type', 'idx_user_activities_type');
            
            // Composite index for recent activities
            $table->index(['user_id', 'created_at', 'type'], 'idx_user_activities_recent');
        });

        // Add indexes to personal_access_tokens table for Sanctum performance
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            // Index for token lookups
            $table->index(['tokenable_type', 'tokenable_id'], 'idx_tokens_tokenable');
            
            // Index for token expiration cleanup
            $table->index('expires_at', 'idx_tokens_expires');
            
            // Index for last used tracking
            $table->index('last_used_at', 'idx_tokens_last_used');
        });

        // Add indexes to contacts table
        Schema::table('contacts', function (Blueprint $table) {
            // Index for email searches
            $table->index('email', 'idx_contacts_email');
            
            // Index for chronological queries
            $table->index('created_at', 'idx_contacts_created');
            
            // Index for subject filtering
            $table->index('subject', 'idx_contacts_subject');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes from CVs table
        Schema::table('cvs', function (Blueprint $table) {
            $table->dropIndex('idx_cvs_user_created');
            $table->dropIndex('idx_cvs_email');
            $table->dropIndex('idx_cvs_template');
            $table->dropIndex('idx_cvs_finalized');
            $table->dropIndex('idx_cvs_analytics');
        });

        // Remove indexes from work_experiences table
        Schema::table('work_experiences', function (Blueprint $table) {
            $table->dropIndex('idx_work_exp_cv_date');
            $table->dropIndex('idx_work_exp_title');
            $table->dropIndex('idx_work_exp_company');
            $table->dropIndex('idx_work_exp_current');
        });

        // Remove indexes from educations table
        Schema::table('educations', function (Blueprint $table) {
            $table->dropIndex('idx_education_cv_date');
            $table->dropIndex('idx_education_institution');
            $table->dropIndex('idx_education_degree');
            $table->dropIndex('idx_education_current');
        });

        // Remove indexes from skills table
        Schema::table('skills', function (Blueprint $table) {
            $table->dropIndex('idx_skills_cv_level');
            $table->dropIndex('idx_skills_name');
            $table->dropIndex('idx_skills_category');
            $table->dropIndex('idx_skills_analytics');
        });

        // Remove indexes from interests table
        Schema::table('interests', function (Blueprint $table) {
            $table->dropIndex('idx_interests_cv');
        });

        // Remove indexes from user_profiles table
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropIndex('idx_user_profiles_user');
            $table->dropIndex('idx_user_profiles_public');
            $table->dropIndex('idx_user_profiles_location');
        });

        // Remove indexes from user_activities table
        Schema::table('user_activities', function (Blueprint $table) {
            $table->dropIndex('idx_user_activities_type');
            $table->dropIndex('idx_user_activities_recent');
        });

        // Remove indexes from personal_access_tokens table
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->dropIndex('idx_tokens_tokenable');
            $table->dropIndex('idx_tokens_expires');
            $table->dropIndex('idx_tokens_last_used');
        });

        // Remove indexes from contacts table
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropIndex('idx_contacts_email');
            $table->dropIndex('idx_contacts_created');
            $table->dropIndex('idx_contacts_subject');
        });
    }
}; 