<?php
// app/Models/Education.php (Update this file)

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Add this line

class Education extends Model
{
    use HasFactory;

    protected $table = 'educations'; // Define table name

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cv_id',
        'institution',
        'degree',
        'field_of_study',
        'start_date',
        'end_date',
        'is_current',
        'description',
        'location',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    // Disable timestamps if your table doesn't have `created_at` and `updated_at`
    public $timestamps = false; // Add this line

    /**
     * Get the CV that owns the education entry.
     */
    public function cv()
    {
        return $this->belongsTo(Cv::class);
    }
}