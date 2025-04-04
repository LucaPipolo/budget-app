<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Merchant;
use Illuminate\Database\Seeder;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Str;

class MerchantSeeder extends Seeder
{
    private array $merchantNames = [
        'Ametller Origen', 'Carrefour', 'Uber Eats', 'Vodafone', 'Primor',
        'Adeslas', 'Apple', 'El Corte Inglés', 'Freenow', 'TMB', 'H&M',
        'Zara', 'La Sirena', 'Bonpreu', 'Lidl', 'Netflix', 'New York Times',
        'PlayStation Store', 'OVS', 'Primark', 'Allianz', 'Sandwichez',
        'Starbucks', 'Vision Direct',
    ];

    public function run(): void
    {
        foreach ($this->merchantNames as $merchantName) {
            $sampleImagePath = database_path('seeders/sample-assets/merchants/' . Str::slug($merchantName) . '.png');
            $logoPath = Storage::disk('public')->putFile('merchants', new File($sampleImagePath));

            Merchant::factory()->create([
                'name' => $merchantName,
                'logo_path' => $logoPath,
            ]);
        }
    }
}
