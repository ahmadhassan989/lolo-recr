<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    use HasFactory;

    /**
     * @var string
     */

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'department',
        'project_id',
        'location',
        'description',
        'requirements',
        'skills',
        'employment_type',
        'salary_range',
        'deadline',
        'status',
        'created_by',
    ];
    protected $table = "tbljobs";

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'deadline' => 'date',
    ];

    /**
     * @return BelongsTo<Project, Job>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return BelongsTo<User, Job>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return HasMany<Application>
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
