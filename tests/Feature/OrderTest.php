<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderTest extends TestCase
{
    public function test_guest_cannot_create_order(): void
    {
        $response = $this->postJson('/api/orders', []);
        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_create_order(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 10]);

        $payload = [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2]
            ],
            'comment' => 'тестовый заказ'
        ];

        $response = $this->actingAs($user, 'api')->postJson('/api/orders', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('orders', ['user_id' => $user->id]);
        $this->assertDatabaseHas('order_items', ['product_id' => $product->id, 'quantity' => 2]);
    }

    public function test_order_requires_items_array(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->postJson('/api/orders', [
            'items' => []
        ]);

        $response->assertStatus(422);
    }

    public function test_user_only_sees_own_orders(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Order::factory()->create(['user_id' => $user1->id]);
        Order::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user1, 'api')->getJson('/api/orders');
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }


}
