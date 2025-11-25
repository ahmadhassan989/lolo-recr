<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use App\Models\Project;
use App\Models\UserProjectLimit;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * @return HasMany<Project>
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'created_by');
    }

    /**
     * @return HasMany<Application>
     */
    public function updatedApplications(): HasMany
    {
        return $this->hasMany(Application::class, 'updated_by');
    }

    public function projectsAssigned(): BelongsToMany
    {
        return $this->belongsToMany(Project::class)
            ->using(ProjectUser::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function projectLimit(): HasOne
    {
        return $this->hasOne(UserProjectLimit::class);
    }

    public function restrictsToAssignedProjects(): bool
    {
        return ! ($this->hasRole('super_admin') || $this->can('projects.manage'));
    }

    public function accessibleProjectIds(): ?Collection
    {
        if (! $this->restrictsToAssignedProjects()) {
            return null;
        }

        $teamAssignments = $this->projectsAssigned()->pluck('projects.id');

        $leadAssignments = Project::query()
            ->where('team_lead_id', $this->id)
            ->pluck('id');

        return $teamAssignments
            ->merge($leadAssignments)
            ->unique()
            ->values();
    }

    public function canCreateMoreProjects(): bool
    {
        if ($this->can('projects.manage') || $this->hasRole('super_admin')) {
            return true;
        }

        if (! $this->can('projects.create')) {
            return false;
        }

        $limit = $this->projectLimit;

        if (! $limit || $limit->max_projects === 0) {
            return false;
        }

        return $this->projects()->count() < $limit->max_projects;
    }
}
