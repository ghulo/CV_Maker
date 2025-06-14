<?php
// app/Models/WorkExperience.php (Update this file)

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Add this line

class WorkExperience extends Model
{
    use HasFactory;

    protected $table = 'work_experiences'; // Define table name

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cv_id',
        'job_title',
        'company',
        'city_country',
        'start_date',
        'end_date',
        'is_current_job',
        'job_description',
    ];

    // Disable timestamps if your table doesn't have `created_at` and `updated_at`
    public $timestamps = false; // Add this line if your table doesn't have timestamps

    /**
     * Get the Cv that owns the work experience.
     */
    public function cv(): BelongsTo // Add this method
    {
        return $this->belongsTo(Cv::class);
    }
}