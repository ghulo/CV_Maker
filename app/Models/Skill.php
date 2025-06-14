<?php
// app/Models/Skill.php (Update this file)

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Add this line

class Skill extends Model
{
    use HasFactory;

    protected $table = 'skills'; // Define table name

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cv_id',
        'name',
        'level',
        'category',
        'years_of_experience',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'level' => 'integer',
        'years_of_experience' => 'float',
    ];

    // Disable timestamps if your table doesn't have `created_at` and `updated_at`
    public $timestamps = false; // Add this line

    /**
     * Get the CV that owns the skill.
     */
    public function cv(): BelongsTo // Add this method
    {
        return $this->belongsTo(CV::class);
    }
}