 <?php
    // database/migrations/YOUR_TIMESTAMP_create_feedback_table.php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
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
        }

        public function down(): void
        {
            Schema::dropIfExists('feedback');
        }
    };