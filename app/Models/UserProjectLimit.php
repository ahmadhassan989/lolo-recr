<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProjectLimit extends Model
{
    protected $fillable = [
        'user_id',
        'max_projects',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
