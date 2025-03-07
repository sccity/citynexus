<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuickVoteResponse extends Model
{
    protected $fillable = [
        'quick_vote_id',
        'voter_name',
        'response',
    ];

    public function vote(): BelongsTo
    {
        return $this->belongsTo(QuickVote::class, 'quick_vote_id');
    }
} 