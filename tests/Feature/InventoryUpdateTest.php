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
            'stock_count' => 10,
            'is_available' => true,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.inventory.update', ['menu' => $menu->id]), [
                'stock_count' => 25,
                'expiration_date' => now()->addDays(7)->toDateString(),
                'is_available' => false,
            ])
            ->assertStatus(302);

        $menu->refresh();
        $this->assertSame(25, (int) $menu->stock_count);
        $this->assertFalse((bool) $menu->is_available);
        $this->assertNotNull($menu->expiration_date);
    }
}
