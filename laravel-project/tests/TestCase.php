<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        return $app;
    }

    /**
     * Create and authenticate a user with admin role
     */
    protected function createAdminUser()
    {
        $user = \App\Models\User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@test.com',
        ]);

        $this->actingAs($user, 'sanctum');

        return $user;
    }

    /**
     * Create and authenticate a regular user
     */
    protected function createUser()
    {
        $user = \App\Models\User::factory()->create([
            'role' => 'user',
            'email' => 'user@test.com',
        ]);

        $this->actingAs($user, 'sanctum');

        return $user;
    }

    /**
     * Get authentication headers for API requests
     */
    protected function getAuthHeaders($user = null)
    {
        if (!$user) {
            $user = $this->createUser();
        }

        $token = $user->createToken('test-token')->plainTextToken;

        return [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ];
    }
}