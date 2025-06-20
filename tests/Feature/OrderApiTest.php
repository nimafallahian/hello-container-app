<?php

namespace Tests\Feature;

use App\Events\OrderPaymentRequired;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_unprocessed_orders(): void
    {
        Order::factory()->create([
            'bl_release_date' => null,
            'freight_payer_self' => false,
            'contract_number' => 'API-UNPRO-001',
        ]);

        Order::factory()->create([
            'bl_release_date' => now(),
            'freight_payer_self' => false,
            'contract_number' => 'API-PRO-001',
        ]);

        Order::factory()->create([
            'bl_release_date' => null,
            'freight_payer_self' => true,
            'contract_number' => 'API-SELF-001',
        ]);

        $response = $this->getJson('/api/v1/orders');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.contract_number', 'API-UNPRO-001')
            ->assertJsonPath('meta.total', 1);
    }

    public function test_can_show_single_order(): void
    {
        $order = Order::factory()->create([
            'contract_number' => 'API-SHOW-001',
            'bl_number' => 'BL-SHOW-001',
        ]);

        $response = $this->getJson("/api/v1/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $order->id)
            ->assertJsonPath('data.contract_number', 'API-SHOW-001')
            ->assertJsonPath('data.bl_number', 'BL-SHOW-001');
    }

    public function test_can_create_order_via_api(): void
    {
        $user = User::factory()->create();

        $orderData = [
            'contract_number' => 'API-CREATE-001',
            'bl_number' => 'BL-CREATE-001',
            'freight_payer_self' => true,
            'bl_release_date' => '2024-01-15 10:00:00',
            'bl_release_user_id' => $user->id,
        ];

        $response = $this->postJson('/api/v1/orders', $orderData);

        $response->assertStatus(201)
            ->assertJsonPath('message', 'Order created successfully')
            ->assertJsonPath('data.contract_number', 'API-CREATE-001');

        $this->assertDatabaseHas('orders', [
            'contract_number' => 'API-CREATE-001',
            'bl_number' => 'BL-CREATE-001',
        ]);
    }

    public function test_can_update_order_via_api(): void
    {
        $order = Order::factory()->create([
            'freight_payer_self' => true,
            'contract_number' => 'API-UPDATE-001',
        ]);

        $updateData = [
            'freight_payer_self' => false,
            'contract_number' => 'API-UPDATE-002',
        ];

        $response = $this->putJson("/api/v1/orders/{$order->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Order updated successfully')
            ->assertJsonPath('data.contract_number', 'API-UPDATE-002')
            ->assertJsonPath('data.freight_payer_self', false);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'contract_number' => 'API-UPDATE-002',
            'freight_payer_self' => false,
        ]);
    }

    public function test_can_delete_order_via_api(): void
    {
        $order = Order::factory()->create();

        $response = $this->deleteJson("/api/v1/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Order deleted successfully');

        $this->assertDatabaseMissing('orders', [
            'id' => $order->id,
        ]);
    }

    public function test_api_validation_errors(): void
    {
        $response = $this->postJson('/api/v1/orders', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['contract_number', 'bl_number', 'freight_payer_self']);
    }

    public function test_returns_404_for_non_existent_order(): void
    {
        $response = $this->getJson('/api/v1/orders/99999');

        $response->assertStatus(404);
    }

    public function test_triggers_payment_event_when_freight_payer_self_changes_to_false(): void
    {
        Event::fake([OrderPaymentRequired::class]);

        $order = Order::factory()->create([
            'freight_payer_self' => true,
        ]);

        $response = $this->putJson("/api/v1/orders/{$order->id}", [
            'freight_payer_self' => false,
        ]);

        $response->assertStatus(200);

        Event::assertDispatched(OrderPaymentRequired::class, function ($event) use ($order) {
            return $event->order->id === $order->id;
        });
    }
}
