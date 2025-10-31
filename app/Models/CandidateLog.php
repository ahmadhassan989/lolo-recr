<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateLog extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'candidate_id',
        'action',
        'performed_by',
        'notes',
    ];

    /**
     * @return BelongsTo<Candidate, CandidateLog>
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    /**
     * @return BelongsTo<User, CandidateLog>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
