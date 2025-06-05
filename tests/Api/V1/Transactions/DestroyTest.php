<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\Category;
use App\Models\Merchant;
use App\Models\Transaction;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Tests\Helpers\assertEndpointRequiresAuthentication;

beforeEach(function (): void {
    $this->user = User::factory()->withPersonalTeam()->create();
    $team = $this->user->currentTeam->id;

    $this->account = Account::factory()->create(['team_id' => $team]);
    $this->category = Category::factory()->create(['team_id' => $team]);
    $this->merchant = Merchant::factory()->create(['team_id' => $team]);
});

assertEndpointRequiresAuthentication('api.v1.transactions.destroy', 'deleteJson', ['transaction' => 1]);

test('user with "delete" token can delete a transaction', function (): void {
    /** @var Transaction $transaction */
    $transaction = Transaction::factory()->create([
        'team_id' => $this->user->currentTeam->id,
    ]);

    Sanctum::actingAs($this->user, ['delete']);

    $this->actingAs($this->user)
        ->deleteJson(route('api.v1.transactions.destroy', $transaction->id))
        ->assertStatus(204);

    $this->assertDatabaseMissing('transactions', [
        'id' => $transaction->id,
    ]);
});

test('denies deletion to user without "delete" token', function (): void {
    /** @var Transaction $transaction */
    $transaction = Transaction::factory()->create([
        'team_id' => $this->user->currentTeam->id,
    ]);

    Sanctum::actingAs($this->user, ['read', 'create', 'update']);

    $this->actingAs($this->user)
        ->deleteJson(route('api.v1.transactions.destroy', $transaction->id))
        ->assertStatus(403);

    $this->assertDatabaseHas('transactions', [
        'id' => $transaction->id,
    ]);
});

test('user cannot delete a transaction that does not belong to them', function (): void {
    $anotherUser = User::factory()->withPersonalTeam()->create();
    $anotherTeam = $anotherUser->currentTeam;

    $anotherTransaction = Transaction::factory()->create([
        'team_id' => $anotherTeam->id,
    ]);

    Sanctum::actingAs($this->user, ['delete']);

    $this->actingAs($this->user)
        ->deleteJson(route('api.v1.transactions.destroy', $anotherTransaction->id))
        ->assertStatus(404);

    $this->assertDatabaseHas('transactions', [
        'id' => $anotherTransaction->id,
    ]);
});
