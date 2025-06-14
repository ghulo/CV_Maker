<?php
// database/migrations/2025_05_27_123507_create_cvs_table.php (Replace everything in this file with this code)

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
        // This check is important if you've already run 'php artisan migrate' before.
        // It prevents errors if the table already exists in your database.
        if (!Schema::hasTable('cvs')) {
            Schema::create('cvs', function (Blueprint $table) {
                // Primary key for the 'cvs' table
                $table->bigIncrements('id');
                // Foreign key to the 'users' table. Must be unsignedBigInteger to match 'users.id' type.
                $table->unsignedBigInteger('user_id');
                $table->string('cv_title')->default('My CV');
                $table->string('emri');
                $table->string('mbiemri');
                $table->string('email')->nullable();
                $table->string('telefoni')->nullable();
                $table->text('address')->nullable();
                $table->text('summary')->nullable();
                $table->date('date_of_birth')->nullable();
                $table->string('place_of_birth')->nullable();
                $table->string('gender', 50)->nullable();
                $table->string('nationality')->nullable();
                $table->string('zip_code', 20)->nullable();
                $table->string('marital_status', 50)->nullable();
                $table->string('driving_license')->nullable();
                $table->string('selected_template', 50)->default('classic');
                $table->timestamps(); // Adds `created_at` and `updated_at` columns

                // Defines the relationship: user_id in 'cvs' table links to id in 'users' table
                // If a user is deleted, all their CVs are automatically deleted.
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     * This method is used when you roll back a migration (undo it).
     */
    public function down(): void
    {
        Schema::dropIfExists('cvs');
    }
};
