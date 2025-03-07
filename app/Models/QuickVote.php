<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuickVote extends Model
{
    protected $fillable = [
        'question',
        'access_code',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function responses(): HasMany
    {
        return $this->hasMany(QuickVoteResponse::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getResultsAttribute(): array
    {
        $responses = $this->responses()->get();
        return [
            'total' => $responses->count(),
            'yea' => $responses->where('response', 'yea')->count(),
            'nay' => $responses->where('response', 'nay')->count(),
            'abstain' => $responses->where('response', 'abstain')->count(),
        ];
    }
} 