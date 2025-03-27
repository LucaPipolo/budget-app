<?php

declare(strict_types=1);

namespace Tests\Helpers;

function assertEndpointRequiresAuthentication(string $routeName, string $method = 'postJson', array $routeParameters = []): void
{
    test('requires authentication', function () use ($routeName, $method, $routeParameters): void {
        auth()->logout();

        $response = $this->{$method}(route($routeName, $routeParameters));

        $response
            ->assertStatus(401)
            ->assertJson([
                'errors' => [
                    [
                        'status' => '401',
                        'title' => 'Not Authenticated',
                        'detail' => 'You are not authenticated.',
                    ],
                ],
            ]);
    });
}
