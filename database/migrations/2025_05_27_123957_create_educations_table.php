<?php
    // database/migrations/YOUR_TIMESTAMP_create_educations_table.php

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
            Schema::create('educations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cv_id')->constrained()->onDelete('cascade');
                $table->string('institution');
                $table->string('degree');
                $table->string('field_of_study')->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->boolean('is_current')->default(false);
                $table->text('description')->nullable();
                $table->string('location')->nullable();
                $table->timestamps();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('educations');
        }
    };