<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        return $this->avatar ? asset('storage/' . $this->avatar) : null;
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
