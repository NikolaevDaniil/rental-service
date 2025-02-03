<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Rental;
use Carbon\Carbon;
use Laravel\Sanctum\Sanctum;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_and_login(): void
    {
        $registrationData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/register', $registrationData);

        $response->assertStatus(201)
                 ->assertJsonStructure(['token']);

        $token = $response->json('token');
        $this->assertNotEmpty($token);

        $loginData = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/login', $loginData);
        $response->assertStatus(200)
                 ->assertJsonStructure(['token']);
    }

    /**
     * Тестирование эндпоинта покупки.
     */
    public function test_purchase_endpoint(): void
    {
        $user = User::factory()->create(['balance' => 1000]);
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 200,
        ]);

        Sanctum::actingAs($user, ['*']);

        $purchaseData = ['product_id' => $product->id];
        $response = $this->postJson('/api/purchase', $purchaseData);

        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'purchase_id']);

        $user->refresh();
        $this->assertEquals(800, $user->balance);
    }

    /**
     * Тестирование эндпоинта аренды товара.
     */
    public function test_rental_endpoint(): void
    {
        $user = User::factory()->create(['balance' => 1000]);
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 300,
        ]);

        Sanctum::actingAs($user, ['*']);

        $rentalData = [
            'product_id' => $product->id,
            'duration' => 8
        ];

        $response = $this->postJson('/api/rental', $rentalData);
        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'rental_id']);

        $user->refresh();
        $this->assertEquals(700, $user->balance);
    }

    /**
     * Тестирование эндпоинта продления аренды.
     */
    public function test_rental_extend_endpoint(): void
    {
        $user = User::factory()->create(['balance' => 1000]);
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 300
        ]);

        Sanctum::actingAs($user, ['*']);

        $now = Carbon::now();
        $initialRental = Rental::create([
            'user_id' => $user->id,
            'product_id' => $product->id, 'rented_at' => $now,
            'expires_at' => $now->copy()->addHours(4),
            'unique_code' => null,
        ]);

        $extendData = [
            'rental_id' => $initialRental->id,
            'duration' => 4,
        ];

        $response = $this->postJson('/api/rental/extend', $extendData);
        $response->assertStatus(200)
                 ->assertJsonStructure(['message', 'rental_id']);

        $initialRental->refresh();
        $this->assertEquals(8, $initialRental->rented_at->diffInHours($initialRental->expires_at));
    }

    /**
     * Тестирование эндпоинтов получения статуса покупки и аренды.
     */
    public function test_status_endpoints(): void
    {
        $user = User::factory()->create(['balance' => 1000]);
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 200,
        ]);

        Sanctum::actingAs($user, ['*']);

        $purchase = Purchase::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'unique_code' => null,
        ]);

        $response = $this->getJson("/api/purchase/status/{$purchase->id}");
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'purchase_id', 'product_id', 'unique_code', 'created_at',
                 ]);

        $now = Carbon::now();
        $rental = Rental::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rented_at' => $now,
            'expires_at' => $now->copy()->addHours(4),
            'unique_code' => null,
        ]);

        $response = $this->getJson("/api/rental/status/{$rental->id}");
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'rental_id', 'product_id', 'unique_code', 'rented_at', 'expires_at',
            ]);
    }

    /**
     * Тестирование эндпоинта истории операций пользователя.
     */
    public function test_history_endpoint(): void
    {
        $user = User::factory()->create(['balance' => 1000]);
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 200,
        ]);

        Sanctum::actingAs($user, ['*']);

        Purchase::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'unique_code' => 'code1',
        ]);

        Rental::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rented_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addHours(4),
            'unique_code' => 'code2',
        ]);

        $response = $this->getJson('/api/history');
        $response->assertStatus(200)
                 ->assertJsonStructure([
                    'purchases' => [
                        '*' => [
                            'id', 'user_id', 'product_id', 'unique_code', 'created_at',
                            'updated_at',
                        ],
                    ],
                    'rentals' => [
                        '*' => [
                            'id', 'user_id', 'product_id', 'rented_at', 'expires_at',
                            'unique_code', 'created_at', 'updated_at',
                        ],
                    ],
                 ]);
    }
}
