<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $currency
 */
class Account extends Model
{
    use HasFactory;
    use HasUuids;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * The account/team relationship.
     *
     * @return BelongsTo The relationship.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    protected function currency(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => strtoupper($value),
            set: fn (string $value) => strtoupper($value),
        );
    }
}
