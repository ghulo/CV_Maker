<?php
    // database/migrations/YOUR_TIMESTAMP_create_work_experiences_table.php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            if (!Schema::hasTable('work_experiences')) {
                Schema::create('work_experiences', function (Blueprint $table) {
                    $table->increments('id');
                    $table->unsignedBigInteger('cv_id'); // Matches cvs.id type
                    $table->string('job_title');
                    $table->string('company');
                    $table->string('city_country')->nullable();
                    $table->date('start_date')->nullable();
                    $table->date('end_date')->nullable();
                    $table->boolean('is_current_job')->default(false);
                    $table->text('job_description')->nullable();

                    $table->foreign('cv_id')->references('id')->on('cvs')->onDelete('cascade');
                });
            }
        }

        public function down(): void
        {
            Schema::dropIfExists('work_experiences');
        }
    };