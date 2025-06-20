<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;
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
    
    public function test_cannot_create_order_without_required_fields(): void
    {
        $this->expectException(QueryException::class);
        
        Order::create([
            'freight_payer_self' => true,
        ]);
    }
    
    public function test_cannot_create_order_with_invalid_user_id(): void
    {
        $this->expectException(QueryException::class);
        
        Order::create([
            'contract_number' => 'CTR-2024-003',
            'bl_number' => 'BL-789012',
            'freight_payer_self' => true,
            'bl_release_user_id' => 99999,
        ]);
    }
    
    public function test_boolean_cast_works_correctly(): void
    {
        $order = Order::create([
            'contract_number' => 'CTR-2024-004',
            'bl_number' => 'BL-345678',
            'freight_payer_self' => 1,
        ]);
        
        $this->assertTrue($order->freight_payer_self);
        $this->assertIsBool($order->freight_payer_self);
        
        $order2 = Order::create([
            'contract_number' => 'CTR-2024-005',
            'bl_number' => 'BL-901234',
            'freight_payer_self' => 0,
        ]);
        
        $this->assertFalse($order2->freight_payer_self);
        $this->assertIsBool($order2->freight_payer_self);
    }
}
