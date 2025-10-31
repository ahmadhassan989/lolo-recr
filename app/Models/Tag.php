<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * @return BelongsToMany<Candidate>
     */
    public function candidates(): BelongsToMany
    {
        return $this->belongsToMany(Candidate::class);
    }
}
