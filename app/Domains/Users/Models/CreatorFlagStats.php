<?php

namespace App\Domains\Users\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreatorFlagStats extends Model
{
    use HasFactory;

    protected $table = 'creator_flag_stats';

    protected $fillable = [
        'user_id',
        'total_flags',
        'flags_last_30_days',
        'last_flagged_at',
        'escalation_level',
        'escalation_triggered_at',
    ];

    protected $casts = [
        'last_flagged_at' => 'datetime',
        'escalation_triggered_at' => 'datetime',
    ];

    /**
     * Get the user these stats belong to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if creator should be warned (3+ flags in 30 days)
     */
    public function shouldWarn(): bool
    {
        return $this->flags_last_30_days >= 3 && $this->escalation_level < 1;
    }

    /**
     * Check if creator should be restricted (5+ flags in 30 days)
     */
    public function shouldRestrict(): bool
    {
        return $this->flags_last_30_days >= 5 && $this->escalation_level < 2;
    }

    /**
     * Check if creator should be banned (10+ total flags)
     */
    public function shouldBan(): bool
    {
        return $this->total_flags >= 10 && $this->escalation_level < 3;
    }

    /**
     * Get escalation status label
     */
    public function getEscalationLabel(): string
    {
        return match ($this->escalation_level) {
            0 => 'None',
            1 => 'Warned',
            2 => 'Restricted',
            3 => 'Banned',
            default => 'Unknown',
        };
    }

    /**
     * Get escalation status badge color
     */
    public function getEscalationBadgeColor(): string
    {
        return match ($this->escalation_level) {
            0 => 'gray',
            1 => 'yellow',
            2 => 'orange',
            3 => 'red',
            default => 'gray',
        };
    }
}
