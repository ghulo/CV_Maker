<?php
// database/migrations/2025_05_27_123507_create_cvs_table.php (Replace everything in this file with this code)

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cvs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('cv_title');
            $table->string('emri');
            $table->string('mbiemri')->nullable();
            $table->string('email');
            $table->string('telefoni')->nullable();
            $table->string('address')->nullable();
            $table->text('summary');
            $table->date('date_of_birth')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('nationality')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('driving_license')->nullable();
            $table->string('selected_template', 50)->default('classic');
            $table->boolean('is_finalized')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cvs');
    }
};