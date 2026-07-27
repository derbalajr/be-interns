<?php

namespace App\Models;

use App\Enums\LeadStage;
use Database\Factories\LeadFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'name',
    'email',
    'phone',
    'source',
    'stage',
    'budget',
    'agent_id',
])]
class Lead extends Model
{
    /** @use HasFactory<LeadFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'stage' => LeadStage::class,
        ];
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function scopeStage(Builder $query, ?string $stage): Builder
    {
        return $query->when(
            $stage,
            fn (Builder $query) => $query->where('stage', $stage),
        );
    }

    public function scopeSource(Builder $query, ?string $source): Builder
    {
        return $query->when(
            $source,
            fn (Builder $query) => $query->where('source', $source),
        );
    }

    public function scopeBudgetRange(
        Builder $query,
        ?float $minBudget,
        ?float $maxBudget,
    ): Builder {
        return $query
            ->when(
                $minBudget !== null,
                fn (Builder $query) => $query->where('budget', '>=', $minBudget),
            )
            ->when(
                $maxBudget !== null,
                fn (Builder $query) => $query->where('budget', '<=', $maxBudget),
            );
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        return $query->when($search, function (Builder $query) use ($search) {
            $query->where(function (Builder $query) use ($search) {
                $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        });
    }

    public function canTransitionTo(LeadStage $stage): bool
    {
        return $this->stage->canTransitionTo($stage);
    }

    public function transitionTo(LeadStage $stage): void
    {
        $this->update([
            'stage' => $stage,
        ]);
    }
}
