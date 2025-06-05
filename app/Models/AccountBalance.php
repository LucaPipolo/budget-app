<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class AccountBalance extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'account_balances';

    protected $primaryKey = 'account_id';

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
        DB::statement('REFRESH MATERIALIZED VIEW CONCURRENTLY account_balances');
    }

    /**
     * Relationship with the Account model.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
