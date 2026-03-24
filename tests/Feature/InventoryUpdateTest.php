<?php
namespace Tests\Feature;

use App\Models\MenuItem;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_inventory_stock_and_availability(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $store = Store::create([
            'name' => 'Test Store',
            'slug' => 'test-store',
            'is_open' => true,
        ]);
        $menu = MenuItem::create([
            'store_id' => $store->id,
            'name' => 'Test Item',
            'price' => 50,
            'current_stock' => 10,
            'daily_target_stock' => 50,
            'is_available' => true,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.inventory.update', ['menu' => $menu->id]), [
                'current_stock' => 25,
                'daily_target_stock' => 100,
                'is_available' => false,
            ])
            ->assertStatus(302);

        $menu->refresh();
        $this->assertSame(25, (int) $menu->current_stock);
        $this->assertSame(100, (int) $menu->daily_target_stock);
        $this->assertFalse((bool) $menu->is_available);
    }
}
