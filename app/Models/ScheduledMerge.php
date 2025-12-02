<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledMerge extends Model
{
    protected $fillable = [
        'user_id',
        'github_pr_url',
        'owner',
        'repo',
        'pull_number',
        'merge_method',
        'scheduled_at',
        'status',
        'error_message',
        'merged_at',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'merged_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function parseGitHubUrl(string $url): ?array
    {
        $pattern = '#^https?://github\.com/([^/]+)/([^/]+)/pull/(\d+)#';

        if (preg_match($pattern, $url, $matches)) {
            return [
                'owner' => $matches[1],
                'repo' => $matches[2],
                'pull_number' => (int) $matches[3],
            ];
        }

        return null;
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}
