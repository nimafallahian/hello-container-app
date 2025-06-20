<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnprocessedOrdersTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_unprocessed_orders_page(): void
    {
        $response = $this->get(route('orders.index'));

        $response->assertStatus(200);
        $response->assertSee('Unprocessed Orders');
        $response->assertSee('No unprocessed orders found');
    }

    public function test_displays_unprocessed_orders_correctly(): void
    {
        $user = User::factory()->create();

        $unprocessedOrder1 = Order::factory()->create([
            'bl_release_date' => null,
            'freight_payer_self' => false,
            'contract_number' => 'UNPRO-001',
            'bl_number' => 'BL-UNPRO-001',
        ]);

        $unprocessedOrder2 = Order::factory()->create([
            'bl_release_date' => null,
            'freight_payer_self' => false,
            'contract_number' => 'UNPRO-002',
            'bl_number' => 'BL-UNPRO-002',
        ]);

        $processedOrder = Order::factory()->create([
            'bl_release_date' => now(),
            'freight_payer_self' => false,
            'contract_number' => 'PRO-001',
            'bl_number' => 'BL-PRO-001',
        ]);

        $selfPayerOrder = Order::factory()->create([
            'bl_release_date' => null,
            'freight_payer_self' => true,
            'contract_number' => 'SELF-001',
            'bl_number' => 'BL-SELF-001',
        ]);

        $response = $this->get(route('orders.index'));

        $response->assertStatus(200);

        $response->assertSee('UNPRO-001');
        $response->assertSee('BL-UNPRO-001');
        $response->assertSee('UNPRO-002');
        $response->assertSee('BL-UNPRO-002');

        $response->assertDontSee('<td>PRO-001</td>', false);
        $response->assertDontSee('<td>SELF-001</td>', false);

        $response->assertSee('Total unprocessed orders: 2');
    }

    public function test_displays_empty_state_when_no_unprocessed_orders(): void
    {
        Order::factory()->create([
            'bl_release_date' => now(),
            'freight_payer_self' => false,
        ]);

        Order::factory()->create([
            'bl_release_date' => null,
            'freight_payer_self' => true,
        ]);

        $response = $this->get(route('orders.index'));

        $response->assertStatus(200);
        $response->assertSee('No unprocessed orders found');
        $response->assertSee('Orders with payment pending (freight_payer_self = false) and no BL release date will appear here');
    }

    public function test_orders_are_displayed_in_descending_order_by_creation_date(): void
    {
        $olderOrder = Order::factory()->create([
            'bl_release_date' => null,
            'freight_payer_self' => false,
            'contract_number' => 'OLD-001',
            'created_at' => now()->subDays(5),
        ]);

        $newerOrder = Order::factory()->create([
            'bl_release_date' => null,
            'freight_payer_self' => false,
            'contract_number' => 'NEW-001',
            'created_at' => now()->subDays(1),
        ]);

        $response = $this->get(route('orders.index'));

        $content = $response->getContent();
        $positionNew = strpos($content, 'NEW-001');
        $positionOld = strpos($content, 'OLD-001');

        $this->assertLessThan($positionOld, $positionNew);
    }

    public function test_shows_days_pending_calculation(): void
    {
        Order::factory()->create([
            'bl_release_date' => null,
            'freight_payer_self' => false,
            'created_at' => now()->subDays(3),
        ]);

        $response = $this->get(route('orders.index'));

        $response->assertStatus(200);
        $response->assertSee('3 days');
    }
}
