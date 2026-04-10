<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // POST /api/register
    // -------------------------------------------------------------------------

    public function test_register_creates_user_and_returns_token(): void
    {
        $response = $this->postJson('/api/register', [
            'name'                  => 'João Silva',
            'email'                 => 'joao@example.com',
            'password'              => 'Test@1234',
            'password_confirmation' => 'Test@1234',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'data'  => ['id', 'name', 'email', 'created_at'],
                     'token' => [],
                 ]);

        $this->assertDatabaseHas('users', ['email' => 'joao@example.com']);
    }

    public function test_register_hashes_password(): void
    {
        $this->postJson('/api/register', [
            'name'                  => 'Test',
            'email'                 => 'test@example.com',
            'password'              => 'Test@1234',
            'password_confirmation' => 'Test@1234',
        ]);

        $user = User::where('email', 'test@example.com')->first();

        $this->assertNotEquals('Test@1234', $user->password);
    }

    public function test_register_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->postJson('/api/register', [
            'name'     => 'Another',
            'email'    => 'existing@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    public function test_register_fails_with_missing_fields(): void
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_register_fails_with_short_password(): void
    {
        $response = $this->postJson('/api/register', [
            'name'     => 'Test',
            'email'    => 'test@example.com',
            'password' => '1234567', // 7 chars
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['password']);
    }

    public function test_register_fails_with_invalid_email(): void
    {
        $response = $this->postJson('/api/register', [
            'name'     => 'Test',
            'email'    => 'not-an-email',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    // -------------------------------------------------------------------------
    // POST /api/login
    // -------------------------------------------------------------------------

    public function test_login_returns_token_for_valid_credentials(): void
    {
        User::factory()->create([
            'email'    => 'user@example.com',
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => 'user@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data'  => ['id', 'name', 'email'],
                     'token' => [],
                 ]);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create(['email' => 'user@example.com']);

        $response = $this->postJson('/api/login', [
            'email'    => 'user@example.com',
            'password' => 'WRONG_PASSWORD',
        ]);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Credenciais inválidas']);
    }

    public function test_login_fails_with_nonexistent_email(): void
    {
        $response = $this->postJson('/api/login', [
            'email'    => 'ghost@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Credenciais inválidas']);
    }

    public function test_login_fails_with_missing_fields(): void
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_login_token_is_usable_on_protected_endpoints(): void
    {
        User::factory()->create([
            'email'    => 'user@example.com',
            'password' => 'password123',
        ]);

        $token = $this->postJson('/api/login', [
            'email'    => 'user@example.com',
            'password' => 'password123',
        ])->json('token');

        $response = $this->withHeader('Authorization', "Bearer {$token}")
                         ->postJson('/api/categories', ['name' => 'Guitars']);

        $response->assertStatus(201);
    }

    // -------------------------------------------------------------------------
    // POST /api/logout
    // -------------------------------------------------------------------------

    public function test_logout_revokes_token(): void
    {
        $user     = User::factory()->create();
        $newToken = $user->createToken('auth_token');

        $this->withToken($newToken->plainTextToken)
             ->postJson('/api/logout')
             ->assertStatus(200)
             ->assertJson(['message' => 'Logged out']);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $newToken->accessToken->id,
        ]);
    }

    public function test_logout_requires_authentication(): void
    {
        $this->postJson('/api/logout')
             ->assertStatus(401);
    }
}
