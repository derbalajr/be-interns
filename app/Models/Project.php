<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'location',
        'description',
    ];

    /**
     * Auto-generate a unique slug when creating or updating a Project.
     */
    protected static function booted(): void
    {
        static::creating(function (Project $project) {
            if (empty($project->slug)) {
                $project->slug = static::generateUniqueSlug($project->name);
            }
        });

        static::updating(function (Project $project) {
            if ($project->isDirty('name')) {
                $project->slug = static::generateUniqueSlug($project->name, $project->id);
            }
        });
    }

    /**
     * Generate a guaranteed-unique slug for the given name.
     */
    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        // Fallback to 'project' if Str::slug returns empty (e.g., Arabic text)
        $baseSlug = Str::slug($name) ?: 'project';
        $slug = $baseSlug;
        $count = 1;

        while (
            static::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $count++;
            $slug = "{$baseSlug}-{$count}";
        }

        return $slug;
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }
}
