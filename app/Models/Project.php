<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'location',
        'description',
    ];

    /**
     * Auto-generate slug when creating a Project if not provided.
     */
    protected static function booted(): void
    {
        static::creating(function (Project $project) {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->name);
            }
        });
        static::updating(function (Project $project) {
            if ($project->isDirty('name')) {
                $project->slug = Str::slug($project->name);
            }
        });
    }
}
