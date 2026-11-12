<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiAccessTest extends TestCase
{
    use DatabaseTransactions;

    public function test_registration_validates_input_and_assigns_customer_role(): void
    {
        $this->postJson('/api/auth/register', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password', 'name', 'last_name']);

        $payload = [
            'email' => Str::uuid() . '@example.test',
            'password' => 'test-password',
            'name' => 'Test',
            'last_name' => 'Customer',
            'role' => 'admin',
        ];

        $this->postJson('/api/auth/register', $payload)->assertOk();

        $user = User::where('email', $payload['email'])->firstOrFail();
        $this->assertSame('customer', $user->role);
        $this->assertNull($user->second_name);
        $this->assertTrue(Hash::check($payload['password'], $user->password));

        $this->postJson('/api/auth/register', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_admin_routes_require_a_valid_jwt(): void
    {
        $this->getJson('/api/users')->assertStatus(401);
        $this->postJson('/api/tables/import/brands')->assertStatus(401);

        $this->withToken('invalid-token');
        $this->getJson('/api/users')->assertStatus(401);
        $this->postJson('/api/tables/import/brands')->assertStatus(401);
    }

    /** @dataProvider authenticatedRoles */
    public function test_admin_routes_check_the_authenticated_users_role(string $role, int $status): void
    {
        $user = $this->createUser($role);

        $this->withToken(JWTAuth::fromUser($user));
        $this->getJson('/api/users')->assertStatus($status);
        $this->postJson('/api/tables/import/brands')->assertStatus($status);
    }

    public function test_customer_can_soft_delete_only_their_own_account(): void
    {
        $customer = $this->createUser('customer');
        $other = $this->createUser('customer');
        $this->withToken(JWTAuth::fromUser($customer));

        $this->postJson('/api/users/delete/' . $other->id)->assertStatus(403);
        $this->postJson('/api/users/delete/' . $customer->id)
            ->assertOk()
            ->assertJsonPath('message', 'deleted');

        $this->assertSoftDeleted($customer);
        $this->assertDatabaseHas('users', ['id' => $customer->id, 'active' => 'N']);
        $this->assertDatabaseHas('users', ['id' => $other->id, 'deleted_at' => null]);
    }

    public function test_last_admin_cannot_be_deleted_or_demoted(): void
    {
        User::where('role', 'admin')->update(['role' => 'customer']);
        $admin = $this->createUser('admin');
        $secondAdmin = $this->createUser('admin');
        $this->withToken(JWTAuth::fromUser($admin));

        $this->postJson('/api/users/delete/' . $secondAdmin->id)->assertOk();
        $this->assertSoftDeleted($secondAdmin);

        $this->postJson('/api/users/delete/' . $admin->id)->assertStatus(409);
        $this->postJson('/api/users/update/' . $admin->id, ['role' => 'customer'])->assertStatus(409);
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'role' => 'admin', 'deleted_at' => null]);
    }

    public function authenticatedRoles(): array
    {
        return [
            'customer' => ['customer', 403],
            'admin' => ['admin', 200],
        ];
    }

    private function createUser(string $role): User
    {
        return User::create([
            'email' => Str::uuid() . '@example.test',
            'password' => Hash::make('test-password'),
            'name' => 'Test',
            'last_name' => 'User',
            'role' => $role,
        ]);
    }
}
