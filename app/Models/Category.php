<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
