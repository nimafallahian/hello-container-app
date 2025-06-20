<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_display_order_creation_form(): void
    {
        $response = $this->get(route('orders.create'));

        $response->assertStatus(200);
        $response->assertSee('Create Order');
        $response->assertSee('Contract Number');
        $response->assertSee('BL Number');
    }

    public function test_can_create_order_with_valid_data(): void
    {
        $user = User::factory()->create();

        $orderData = [
            'contract_number' => 'TEST-CONTRACT-001',
            'bl_number' => 'TEST-BL-001',
            'freight_payer_self' => '1',
            'bl_release_date' => '2024-01-15 10:30:00',
            'bl_release_user_id' => $user->id,
        ];

        $response = $this->post(route('orders.store'), $orderData);

        $response->assertRedirect(route('orders.create'));
        $response->assertSessionHas('success', 'Order created successfully!');

        $this->assertDatabaseHas('orders', [
            'contract_number' => 'TEST-CONTRACT-001',
            'bl_number' => 'TEST-BL-001',
            'freight_payer_self' => true,
            'bl_release_user_id' => $user->id,
        ]);
    }

    public function test_can_create_order_with_minimal_required_data(): void
    {
        $orderData = [
            'contract_number' => 'TEST-CONTRACT-002',
            'bl_number' => 'TEST-BL-002',
            'freight_payer_self' => '0',
        ];

        $response = $this->post(route('orders.store'), $orderData);

        $response->assertRedirect(route('orders.create'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'contract_number' => 'TEST-CONTRACT-002',
            'bl_number' => 'TEST-BL-002',
            'freight_payer_self' => false,
            'bl_release_date' => null,
            'bl_release_user_id' => null,
        ]);
    }

    public function test_validation_fails_with_missing_required_fields(): void
    {
        $response = $this->post(route('orders.store'), []);

        $response->assertSessionHasErrors(['contract_number', 'bl_number', 'freight_payer_self']);
    }

    public function test_validation_fails_with_invalid_user_id(): void
    {
        $orderData = [
            'contract_number' => 'TEST-CONTRACT-003',
            'bl_number' => 'TEST-BL-003',
            'freight_payer_self' => '1',
            'bl_release_user_id' => 99999,
        ];

        $response = $this->post(route('orders.store'), $orderData);

        $response->assertSessionHasErrors(['bl_release_user_id']);
    }

    public function test_validation_fails_with_invalid_date_format(): void
    {
        $orderData = [
            'contract_number' => 'TEST-CONTRACT-004',
            'bl_number' => 'TEST-BL-004',
            'freight_payer_self' => '1',
            'bl_release_date' => 'invalid-date',
        ];

        $response = $this->post(route('orders.store'), $orderData);

        $response->assertSessionHasErrors(['bl_release_date']);
    }
}
