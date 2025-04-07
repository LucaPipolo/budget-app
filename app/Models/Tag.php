<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $color
 */
class Tag extends Model
{
    use HasFactory;
    use HasUuids;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * The tag/team relationship.
     *
     * @return BelongsTo The relationship.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * The tags/transactions relationship.
     *
     * @return BelongsToMany The relationship.
     */
    public function transactions(): BelongsToMany
    {
        return $this->belongsToMany(Transaction::class);
    }

    protected function color(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => mb_strtolower($value),
            set: fn (string $value) => mb_strtolower($value),
        );
    }
}
