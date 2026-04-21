<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Tags\HasTags;

class Form extends Model
{
    use HasFactory;
    use HasTags;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'status',
        'current_schema',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'current_schema' => 'array',
            'settings' => 'array',
        ];
    }

    public function versions(): HasMany
    {
        return $this->hasMany(FormVersion::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }

    public function scopePublished($query): void
    {
        $query->where('status', 'published');
    }

    public function latestPublishedVersion(): ?FormVersion
    {
        return $this->versions()
            ->whereNotNull('published_at')
            ->orderByDesc('version_number')
            ->first();
    }
}
