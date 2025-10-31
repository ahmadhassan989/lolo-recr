<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobOffer extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'candidate_id',
        'project_id',
        'position_title',
        'salary',
        'currency',
        'start_date',
        'contract_duration',
        'status',
        'notes',
        'created_by',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'salary' => 'decimal:2',
    ];

    /**
     * @return BelongsTo<Candidate, JobOffer>
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    /**
     * @return BelongsTo<Project, JobOffer>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return BelongsTo<User, JobOffer>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
