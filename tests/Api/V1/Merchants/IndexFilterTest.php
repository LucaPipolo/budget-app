<?php

declare(strict_types=1);

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Sanctum\Sanctum;

test('merchants can be filtered by name', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Merchant> $merchants */
    $merchants = Merchant::factory(3)->create([
        'team_id' => $user->currentTeam->id,
    ]);
    $filteredMerchant = $merchants->random();

    Sanctum::actingAs($user, ['read']);

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.merchants.index', ['filter[name]' => $filteredMerchant->name]))
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['id' => $filteredMerchant->id]);

    $otherMerchants = $merchants->except($filteredMerchant->id)->pluck('id');
    foreach ($otherMerchants as $merchant) {
        $response->assertJsonMissing(['id' => $merchant]);
    }
});

test('merchants can be filtered by balance', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Merchant> $merchants */
    $merchants = Merchant::factory(3)->create([
        'team_id' => $user->currentTeam->id,
    ]);
    $filteredMerchant = $merchants->random();

    Sanctum::actingAs($user, ['read']);

    $response = $this->actingAs($user)
        ->getJson(route('api.v1.merchants.index', ['filter[balance]' => $filteredMerchant->balance]))
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['id' => $filteredMerchant->id]);

    $otherMerchants = $merchants->except($filteredMerchant->id)->pluck('id');
    foreach ($otherMerchants as $merchant) {
        $response->assertJsonMissing(['id' => $merchant]);
    }
});

test('merchants can be filtered by balance using operators', function (): void {
    $user = User::factory()->withPersonalTeam()->create();

    /** @var Collection<int, Merchant> $merchants */
    $merchants = Merchant::factory(10)->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Sanctum::actingAs($user, ['read']);

    $referenceBalance = $merchants->sortBy('balance')->values()[2]->balance;

    // Greater than
    $expectedGt = $merchants->where('balance', '>', $referenceBalance)->values()->toArray();
    $response = $this->actingAs($user)
        ->getJson(route('api.v1.merchants.index', ['filter[balance]' => '>' . $referenceBalance]))
        ->assertStatus(200);
    $this->assertCount(count($expectedGt), $response->json('data'));
    $this->assertEquals(
        collect($expectedGt)->pluck('id')->sort()->values()->toArray(),
        collect($response->json('data'))->pluck('id')->sort()->values()->toArray()
    );

    // Less than
    $expectedLt = $merchants->where('balance', '<', $referenceBalance)->values()->toArray();
    $response = $this->actingAs($user)
        ->getJson(route('api.v1.merchants.index', ['filter[balance]' => '<' . $referenceBalance]))
        ->assertStatus(200);
    $this->assertCount(count($expectedLt), $response->json('data'));
    $this->assertEquals(
        collect($expectedLt)->pluck('id')->sort()->values()->toArray(),
        collect($response->json('data'))->pluck('id')->sort()->values()->toArray()
    );

    // Greater than or equal
    $expectedGte = $merchants->where('balance', '>=', $referenceBalance)->values()->toArray();
    $response = $this->actingAs($user)
        ->getJson(route('api.v1.merchants.index', ['filter[balance]' => '>=' . $referenceBalance]))
        ->assertStatus(200);
    $this->assertCount(count($expectedGte), $response->json('data'));
    $this->assertEquals(
        collect($expectedGte)->pluck('id')->sort()->values()->toArray(),
        collect($response->json('data'))->pluck('id')->sort()->values()->toArray()
    );

    // Less than or equal
    $expectedLte = $merchants->where('balance', '<=', $referenceBalance)->values()->toArray();
    $response = $this->actingAs($user)
        ->getJson(route('api.v1.merchants.index', ['filter[balance]' => '<=' . $referenceBalance]))
        ->assertStatus(200);
    $this->assertCount(count($expectedLte), $response->json('data'));
    $this->assertEquals(
        collect($expectedLte)->pluck('id')->sort()->values()->toArray(),
        collect($response->json('data'))->pluck('id')->sort()->values()->toArray()
    );
});
