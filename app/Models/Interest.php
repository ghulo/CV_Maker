<?php
// app/Models/Interest.php (Update this file)

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Add this line

class Interest extends Model
{
    use HasFactory;

    protected $table = 'interests'; // Define table name

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cv_id',
        'description',
    ];

    // Disable timestamps if your table doesn't have `created_at` and `updated_at`
    public $timestamps = false; // Add this line

    /**
     * Get the Cv that owns the interest.
     */
    public function cv(): BelongsTo // Add this method
    {
        return $this->belongsTo(Cv::class);
    }
}