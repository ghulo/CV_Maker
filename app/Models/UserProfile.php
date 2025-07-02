<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'avatar',
        'headline',
        'bio',
        'social_links',
        'phone',
        'address',
        'location',
        'website',
        'portfolio_links',
        'skills',
        'interests',
        'languages',
        'is_public',
    ];

    protected $casts = [
        'social_links' => 'array',
        'portfolio_links' => 'array',
        'skills' => 'array',
        'interests' => 'array',
        'languages' => 'array',
        'is_public' => 'boolean',
    ];

    protected $appends = ['avatar_url'];

    /**
     * Get the user that owns the profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the avatar URL
     */
    public function getAvatarUrlAttribute()
    {
        if (!$this->avatar) {
            return null;
        }

        // If it's already a full URL, return as is
        if (str_starts_with($this->avatar, 'http')) {
            return $this->avatar;
        }

        // Return the storage URL
        return Storage::url($this->avatar);
    }

    /**
     * Get formatted social links
     */
    public function getSocialLinksFormattedAttribute()
    {
        $links = $this->social_links ?? [];
        $formatted = [];
        
        foreach ($links as $platform => $url) {
            $formatted[] = [
                'platform' => $platform,
                'url' => $url,
                'icon' => $this->getSocialIcon($platform),
            ];
        }

        return $formatted;
    }

    /**
     * Get icon class for social platform
     */
    protected function getSocialIcon($platform)
    {
        return match (strtolower($platform)) {
            'linkedin' => 'fab fa-linkedin',
            'github' => 'fab fa-github',
            'twitter' => 'fab fa-twitter',
            'facebook' => 'fab fa-facebook',
            'instagram' => 'fab fa-instagram',
            default => 'fas fa-link',
        };
    }
}
