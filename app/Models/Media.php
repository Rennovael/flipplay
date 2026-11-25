<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'genre',
        'synopsis',
        'release_year',
        'cover_url',
    ];

    protected function casts(): array
    {
        return [
            'release_year' => 'integer',
        ];
    }

    /**
     * Users who saved this media in their personal collections.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
