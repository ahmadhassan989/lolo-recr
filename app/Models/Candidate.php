<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'cv_file',
        'linkedin_url',
        'skills',
        'experience_years',
        'notes',
        'gender',
        'birth_date',
        'nationality',
        'education_level',
        'expected_salary',
        'availability_date',
        'source',
        'rating',
        'status',
        'created_by',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'birth_date' => 'date',
        'availability_date' => 'date',
        'expected_salary' => 'decimal:2',
        'experience_years' => 'integer',
        'rating' => 'integer',
    ];

    /**
     * @return HasMany<Application>
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * @return HasMany<Interview>
     */
    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    /**
     * @return HasMany<Attachment>
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    /**
     * @return BelongsTo<User, Candidate>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsToMany<Tag>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * @return HasMany<CandidateLog>
     */
    public function logs(): HasMany
    {
        return $this->hasMany(CandidateLog::class)->latest();
    }

    /**
     * @return HasMany<JobOffer>
     */
    public function jobOffers(): HasMany
    {
        return $this->hasMany(JobOffer::class)->latest();
    }
}
