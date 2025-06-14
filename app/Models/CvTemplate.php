<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CvTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'style_config',
        'color_schemes',
        'thumbnail',
        'is_premium',
        'is_active'
    ];

    protected $casts = [
        'style_config' => 'array',
        'color_schemes' => 'array',
        'is_premium' => 'boolean',
        'is_active' => 'boolean'
    ];

    /**
     * Get the CVs using this template.
     */
    public function cvs()
    {
        return $this->hasMany(CV::class, 'template_id');
    }

    /**
     * Get the template's preview URL
     */
    public function getPreviewUrlAttribute()
    {
        return $this->thumbnail ? asset('storage/' . $this->thumbnail) : null;
    }
}
