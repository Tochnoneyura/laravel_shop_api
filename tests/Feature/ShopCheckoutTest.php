<?php

namespace Tests\Feature;

use App\Models\Order_item;
use App\Models\User;
use Database\Seeders\DemoCatalogSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ShopCheckoutTest extends TestCase
{
    use DatabaseTransactions;

    private const SCREWDRIVER = 'd0000000-0000-4000-8000-000000000002';
    private const TAPE_MEASURE = 'd0000000-0000-4000-8000-000000000003';

    public function test_search_basket_and_checkout_are_scoped_to_the_current_user(): void
    {
        $this->seed(DemoCatalogSeeder::class);
        $buyer = $this->authenticateCustomer();

        $this->getJson('/api/products?search=SCREW')
            ->assertOk()
            ->assertJsonPath('data.0.guid', self::SCREWDRIVER);
        $this->getJson('/api/brands?search=nord')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Nord Tools');

        $this->addToBasket(self::SCREWDRIVER, 1);
        $this->addToBasket(self::SCREWDRIVER, 1.5);
        $this->getJson('/api/basket')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.amount', 1.5);

        $order = $this->postJson('/api/orders/submit', $this->orderPayload([self::SCREWDRIVER]))
            ->assertOk()
            ->assertJsonPath('order.total', '735.75')
            ->assertJsonPath('order.created_by', $buyer->id)
            ->assertJsonPath('order.order_item.0.amount', '1.500')
            ->assertJsonPath('order.order_item.0.price', '490.50')
            ->json('order.id');

        DB::table('nomenclatures')->where('guid', self::SCREWDRIVER)->update(['price' => '600.00']);
        $this->getJson('/api/orders')
            ->assertOk()
            ->assertJsonPath('data.0.id', $order)
            ->assertJsonPath('data.0.order_item.0.price', '490.50');

        $this->authenticateCustomer();
        $this->getJson('/api/basket')->assertOk()->assertJsonPath('total', 0);
        $this->getJson('/api/orders')->assertOk()->assertJsonPath('total', 0);
        $this->postJson('/api/orders/submit', $this->orderPayload([self::SCREWDRIVER]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('products');
    }

    public function test_checkout_rolls_back_order_and_positions_if_a_position_fails(): void
    {
        $this->seed(DemoCatalogSeeder::class);
        $buyer = $this->authenticateCustomer();
        $this->addToBasket(self::SCREWDRIVER, 2);
        $this->addToBasket(self::TAPE_MEASURE, 1);
        $initialItemCount = DB::table('order_items')->count();

        $attempts = 0;
        Order_item::creating(function () use (&$attempts) {
            if (++$attempts === 2) {
                throw new RuntimeException('Simulated order item failure.');
            }
        });

        $this->postJson('/api/orders/submit', $this->orderPayload([self::SCREWDRIVER, self::TAPE_MEASURE]))
            ->assertStatus(500);

        $this->assertSame(2, $attempts);
        $this->assertDatabaseMissing('orders', ['created_by' => $buyer->id]);
        $this->assertDatabaseCount('order_items', $initialItemCount);
    }

    private function authenticateCustomer(): User
    {
        $user = User::create([
            'email' => Str::uuid() . '@example.test',
            'password' => bcrypt('test-password'),
            'name' => 'Test',
            'last_name' => 'Customer',
            'role' => 'customer',
        ]);

        auth()->forgetGuards();
        app('tymon.jwt')->unsetToken();
        $this->withToken(JWTAuth::fromUser($user));

        return $user;
    }

    private function addToBasket(string $guid, float $amount): void
    {
        $this->postJson('/api/basket/add', [
            'basket' => [['nomenclature_guid' => $guid, 'amount' => $amount]],
        ])->assertOk();
    }

    private function orderPayload(array $products): array
    {
        return [
            'products' => $products,
            'delivery_method' => 'Самовывоз',
            'delivery_address' => 'Pickup point',
            'delivery_company' => 'Nord Tools',
            'contact_name' => 'Test Customer',
            'contact_phone' => '+70000000000',
            'is_delivery_today' => false,
        ];
    }
}
