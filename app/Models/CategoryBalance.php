<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class CategoryBalance extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'category_balances';

    protected $primaryKey = 'category_id';

    protected $keyType = 'string';

    protected $casts = [
        'total_income' => 'integer',
        'total_outcome' => 'integer',
        'balance' => 'integer',
    ];

    /**
     * Refresh the materialized view.
     */
    public static function refreshView(): void
    {
        DB::statement('REFRESH MATERIALIZED VIEW CONCURRENTLY category_balances');
    }

    /**
     * Relationship with the Category model.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
