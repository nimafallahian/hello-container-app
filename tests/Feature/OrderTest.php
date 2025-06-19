<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_order_with_all_fields(): void
    {
        $user = User::factory()->create();
        
        $orderData = [
            'bl_release_date' => now(),
            'bl_release_user_id' => $user->id,
            'freight_payer_self' => true,
            'contract_number' => 'CTR-2024-001',
            'bl_number' => 'BL-123456',
        ];
        
        $order = Order::create($orderData);
        
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'bl_release_user_id' => $user->id,
            'freight_payer_self' => true,
            'contract_number' => 'CTR-2024-001',
            'bl_number' => 'BL-123456',
        ]);
    }
    
    public function test_can_create_order_with_nullable_fields(): void
    {
        $orderData = [
            'bl_release_date' => null,
            'bl_release_user_id' => null,
            'freight_payer_self' => false,
            'contract_number' => 'CTR-2024-002',
            'bl_number' => 'BL-654321',
        ];
        
        $order = Order::create($orderData);
        
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'bl_release_date' => null,
            'bl_release_user_id' => null,
            'freight_payer_self' => false,
            'contract_number' => 'CTR-2024-002',
            'bl_number' => 'BL-654321',
        ]);
    }
    
    public function test_can_create_order_using_factory(): void
    {
        $user = User::factory()->create();
        
        $order = Order::factory()->create([
            'bl_release_user_id' => $user->id,
        ]);
        
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
        ]);
        
        $this->assertNotNull($order->contract_number);
        $this->assertNotNull($order->bl_number);
    }
}
