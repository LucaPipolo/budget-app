<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class MerchantBalance extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'merchant_balances';

    protected $primaryKey = 'merchant_id';

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
        DB::statement('REFRESH MATERIALIZED VIEW CONCURRENTLY merchant_balances');
    }

    /**
     * Relationship with the Merchant model.
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}
