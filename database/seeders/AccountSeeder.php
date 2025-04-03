<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AccountSeeder extends Seeder
{
    private array $accounts = [
        [
            'name' => 'Santander',
            'type' => 'bank',
            'currency' => 'EUR',
        ],
        [
            'name' => 'BBVA',
            'type' => 'bank',
            'currency' => 'EUR',
        ],
        [
            'name' => 'Caixabank',
            'type' => 'bank',
            'currency' => 'EUR',
        ],
        [
            'name' => 'Abanca',
            'type' => 'bank',
            'currency' => 'EUR',
        ],
        [
            'name' => 'ING',
            'type' => 'bank',
            'currency' => 'GBP',
        ],
        [
            'name' => 'Sabadell',
            'type' => 'bank',
            'currency' => 'EUR',
        ],
        [
            'name' => 'Bankia',
            'type' => 'bank',
            'currency' => 'EUR',
        ],
        [
            'name' => 'Kutxabank',
            'type' => 'bank',
            'currency' => 'EUR',
        ],
        [
            'name' => 'Revolut',
            'type' => 'bank',
            'currency' => 'GBP',
        ],
        [
            'name' => 'Ibercaja',
            'type' => 'bank',
            'currency' => 'EUR',
        ],
        [
            'name' => 'Unicaja',
            'type' => 'bank',
            'currency' => 'EUR',
        ],
        [
            'name' => 'Savings',
            'type' => 'cash',
            'currency' => 'USD',
        ],
        [
            'name' => 'Wallet',
            'type' => 'cash',
            'currency' => 'USD',
        ],
        [
            'name' => 'eToro',
            'type' => 'investments',
            'currency' => 'EUR',
        ],
        [
            'name' => 'Scalable',
            'type' => 'investments',
            'currency' => 'EUR',
        ],
        [
            'name' => 'Trade Republic',
            'type' => 'investments',
            'currency' => 'EUR',
        ],
    ];

    public function run(): void
    {
        $randomAccounts = collect($this->accounts)->shuffle()->take(4);

        foreach ($randomAccounts as $account) {
            $sampleImagePath = database_path('seeders/sample-assets/accounts/' . Str::slug($account['name']) . '.png');
            $logoPath = Storage::disk('public')->putFile('accounts', new File($sampleImagePath));

            Account::factory()->create([
                'name' => $account['name'],
                'type' => $account['type'],
                'currency' => $account['currency'],
                'logo_path' => $logoPath,
            ]);
        }
    }
}
