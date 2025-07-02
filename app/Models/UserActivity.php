<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'description',
        'icon',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the activity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Activity types constants
     */
    const TYPE_CV_CREATED = 'cv_created';
    const TYPE_CV_UPDATED = 'cv_updated';
    const TYPE_CV_DELETED = 'cv_deleted';
    const TYPE_CV_DOWNLOADED = 'cv_downloaded';
    const TYPE_CV_PRINTED = 'cv_printed';
    const TYPE_CV_PREVIEWED = 'cv_previewed';
    const TYPE_PROFILE_UPDATED = 'profile_updated';
    const TYPE_PASSWORD_UPDATED = 'password_updated';
    const TYPE_AVATAR_UPDATED = 'avatar_updated';
    const TYPE_ACCOUNT_CREATED = 'account_created';
    const TYPE_LOGIN = 'login';
    const TYPE_LOGOUT = 'logout';
    const TYPE_EXPORT_DATA = 'export_data';
    const TYPE_BULK_DOWNLOAD = 'bulk_download';

    /**
     * Helper method to log activity
     */
    public static function log($userId, $type, $title, $description, $metadata = null, $icon = null)
    {
        $iconMap = [
            self::TYPE_CV_CREATED => 'fas fa-file-plus',
            self::TYPE_CV_UPDATED => 'fas fa-edit',
            self::TYPE_CV_DELETED => 'fas fa-trash',
            self::TYPE_CV_DOWNLOADED => 'fas fa-download',
            self::TYPE_CV_PRINTED => 'fas fa-print',
            self::TYPE_CV_PREVIEWED => 'fas fa-eye',
            self::TYPE_PROFILE_UPDATED => 'fas fa-user-edit',
            self::TYPE_PASSWORD_UPDATED => 'fas fa-key',
            self::TYPE_AVATAR_UPDATED => 'fas fa-image',
            self::TYPE_ACCOUNT_CREATED => 'fas fa-user-plus',
            self::TYPE_LOGIN => 'fas fa-sign-in-alt',
            self::TYPE_LOGOUT => 'fas fa-sign-out-alt',
            self::TYPE_EXPORT_DATA => 'fas fa-file-export',
            self::TYPE_BULK_DOWNLOAD => 'fas fa-archive',
        ];

        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'description' => $description,
            'icon' => $icon ?? $iconMap[$type] ?? 'fas fa-info-circle',
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get activities for a user with optional filtering
     */
    public static function getForUser($userId, $limit = 10, $type = null)
    {
        $query = self::where('user_id', $userId)
            ->orderBy('created_at', 'desc');

        if ($type) {
            $query->where('type', $type);
        }

        return $query->limit($limit)->get();
    }

    /**
     * Get activity statistics for a user
     */
    public static function getStatsForUser($userId)
    {
        return [
            'total_activities' => self::where('user_id', $userId)->count(),
            'cvs_created' => self::where('user_id', $userId)->where('type', self::TYPE_CV_CREATED)->count(),
            'cvs_downloaded' => self::where('user_id', $userId)->where('type', self::TYPE_CV_DOWNLOADED)->count(),
            'profile_updates' => self::where('user_id', $userId)->where('type', self::TYPE_PROFILE_UPDATED)->count(),
            'last_activity' => self::where('user_id', $userId)->latest()->first()?->created_at,
        ];
    }
}
