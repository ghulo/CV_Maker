<?php
    // database/migrations/YOUR_TIMESTAMP_create_interests_table.php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            if (!Schema::hasTable('interests')) {
                Schema::create('interests', function (Blueprint $table) {
                    $table->increments('id');
                    $table->unsignedBigInteger('cv_id')->unique(); // Matches cvs.id type, and unique
                    $table->text('description')->nullable();

                    $table->foreign('cv_id')->references('id')->on('cvs')->onDelete('cascade');
                });
            }
        }

        public function down(): void
        {
            Schema::dropIfExists('interests');
        }
    };