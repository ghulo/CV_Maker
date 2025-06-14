<?php
// app/Models/Feedback.php (Update this file)

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedback'; // Define table name

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'contact_name',
        'contact_email',
        'feedback_subject',
        'feedback_message',
        'submitted_at', // Include if you're manually setting it, otherwise Laravel handles it
    ];

    // Disable timestamps if your table doesn't have `created_at` and `updated_at`
    public $timestamps = false; // Add this line

    // If 'submitted_at' is a Carbon instance (Laravel's default for timestamps), you might need to cast it
    // protected $casts = [
    //     'submitted_at' => 'datetime',
    // ];
}
