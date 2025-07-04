<?php
// app/Models/Cv.php (Update this file)

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Add this line
use Illuminate\Database\Eloquent\Relations\HasMany; // Add this line
use Illuminate\Database\Eloquent\Relations\HasOne; // Add this line

class Cv extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cvs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'cv_title',
        'emri',
        'mbiemri',
        'email',
        'telefoni',
        'address',
        'summary',
        'date_of_birth',
        'place_of_birth',
        'gender',
        'nationality',
        'zip_code',
        'marital_status',
        'driving_license',
        'selected_template',
        'is_finalized',
        'views',
        'downloads',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'views' => 'integer',
        'downloads' => 'integer',
        'is_public' => 'boolean',
        'is_finalized' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the CV.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the education entries for the CV.
     */
    public function education(): HasMany
    {
        return $this->hasMany(Education::class);
    }

    /**
     * Get the work experience entries for the CV.
     */
    public function workExperiences(): HasMany
    {
        return $this->hasMany(WorkExperience::class);
    }

    /**
     * Get the skills for the CV.
     */
    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class);
    }

    /**
     * Get the interests for the CV.
     */
    public function interests(): HasMany
    {
        return $this->hasMany(Interest::class);
    }

    /**
     * Increment the view count.
     */
    public function incrementViews()
    {
        $this->increment('views');
    }

    /**
     * Increment the download count.
     */
    public function incrementDownloads()
    {
        $this->increment('downloads');
    }
}