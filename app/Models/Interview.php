<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interview extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'candidate_id',
        'project_id',
        'interview_date',
        'interviewer',
        'type',
        'result',
        'notes',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'interview_date' => 'datetime',
    ];

    /**
     * @return BelongsTo<Candidate, Interview>
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    /**
     * @return BelongsTo<Project, Interview>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
