<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitMedia extends Model
{
    use HasFactory;

    public const TYPE_PHOTO = 'photo';

    public const TYPE_FLOOR_PLAN = 'floor_plan';

    // define the two allowed media types.
    protected $fillable = [
        'path',
        'type',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    } // means every uploaded media record belongs to one unit
}
