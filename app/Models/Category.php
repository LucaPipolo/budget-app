<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;
    use HasUuids;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * The category/team relationship.
     *
     * @return BelongsTo The relationship.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * The category/transactions relationship.
     *
     * @return HasMany The relationship.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
