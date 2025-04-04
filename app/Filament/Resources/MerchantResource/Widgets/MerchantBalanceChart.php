<?php

declare(strict_types=1);

namespace App\Filament\Resources\MerchantResource\Widgets;

use App\Models\Merchant;
use Filament\Widgets\ChartWidget;

class MerchantBalanceChart extends ChartWidget
{
    protected static ?string $heading = 'Saldo rivenditori';

    protected function getData(): array
    {
        $user = auth()->user();

        $query = Merchant::query()
            ->where('team_id', $user->currentTeam->id) // Filtro per il team dell'utente corrente
            ->select('name', 'balance')
            ->orderBy('balance', 'desc');

        if ($this->filter === 'top5') {
            $query->limit(5);
        } elseif ($this->filter === 'top10') {
            $query->limit(10);
        }

        $merchants = $query->get();

        return [
            'datasets' => [
                [
                    'label' => 'Saldo rivenditori',
                    'data' => $merchants->pluck('balance')->toArray(),
                    'backgroundColor' => [
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)',
                    ],
                ],
            ],
            'labels' => $merchants->pluck('name')->toArray(),
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            'all' => 'Tutti',
            'top5' => 'Top 5',
            'top10' => 'Top 10',
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
