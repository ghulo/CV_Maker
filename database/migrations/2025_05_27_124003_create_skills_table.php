<?php
    // database/migrations/YOUR_TIMESTAMP_create_skills_table.php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            if (!Schema::hasTable('skills')) {
                Schema::create('skills', function (Blueprint $table) {
                    $table->increments('id');
                    $table->unsignedBigInteger('cv_id'); // Matches cvs.id type
                    $table->string('skill_name');
                    $table->string('skill_level', 100)->nullable();
                    $table->string('category', 100)->nullable();

                    $table->foreign('cv_id')->references('id')->on('cvs')->onDelete('cascade');
                });
            }
        }

        public function down(): void
        {
            Schema::dropIfExists('skills');
        }
    };