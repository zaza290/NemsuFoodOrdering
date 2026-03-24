<template>
  <AdminLayout title="Inventory Dashboard">
    <div class="inventory-page pb-12">
      <div class="page-header mb-8">
        <h2 class="page-title text-slate-900 dark:text-white">📦 Inventory Overview</h2>
        <p class="page-sub text-slate-500 dark:text-slate-400">Availability of menu items across all stores</p>
      </div>

      <div class="stats-grid mb-12">
        <div class="stat-card green bg-white dark:bg-slate-800 border-slate-100 dark:border-slate-700 shadow-3d">
          <div class="stat-icon">📋</div>
          <div class="stat-info">
            <div class="stat-value text-slate-900 dark:text-white">{{ overall.total_items }}</div>
            <div class="stat-label text-slate-500 dark:text-slate-400">Total Items</div>
          </div>
        </div>
        <div class="stat-card teal bg-white dark:bg-slate-800 border-slate-100 dark:border-slate-700 shadow-3d">
          <div class="stat-icon">✅</div>
          <div class="stat-info">
            <div class="stat-value text-slate-900 dark:text-white">{{ overall.available_items }}</div>
            <div class="stat-label text-slate-500 dark:text-slate-400">Available</div>
          </div>
        </div>
        <div class="stat-card red bg-white dark:bg-slate-800 border-slate-100 dark:border-slate-700 shadow-3d">
          <div class="stat-icon">❌</div>
          <div class="stat-info">
            <div class="stat-value text-slate-900 dark:text-white">{{ overall.unavailable_items }}</div>
            <div class="stat-label text-slate-500 dark:text-slate-400">Unavailable</div>
          </div>
        </div>
      </div>

      <div class="panel bg-white dark:bg-slate-800 border-slate-100 dark:border-slate-700 mb-12 shadow-3d">
        <div class="panel-header border-slate-100 dark:border-slate-700">
          <h3 class="panel-title text-slate-900 dark:text-white">Store Inventory</h3>
        </div>
        <div class="table-wrap">
          <table class="inv-table">
            <thead class="bg-slate-50 dark:bg-slate-900/50">
              <tr>
                <th class="text-slate-500 dark:text-slate-400">Store</th>
                <th class="num text-slate-500 dark:text-slate-400">Items</th>
                <th class="num text-slate-500 dark:text-slate-400">Available</th>
                <th class="num text-slate-500 dark:text-slate-400">Unavailable</th>
                <th class="text-slate-500 dark:text-slate-400">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
              <tr v-for="s in stores" :key="s.id" class="text-slate-700 dark:text-slate-200">
                <td>
                  <div class="store-name font-bold">
                    <span class="dot" :class="s.is_open ? 'open' : 'closed'"></span>
                    {{ s.name }}
                  </div>
                </td>
                <td class="num font-medium">{{ s.total_items }}</td>
                <td class="num avail">{{ s.available_items }}</td>
                <td class="num unavail">{{ s.unavailable_items }}</td>
                <td>
                  <span :class="['status-badge', s.is_open ? 'open' : 'closed']">
                    {{ s.is_open ? 'Open' : 'Closed' }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Low Stock Tracker -->
      <div class="panel low-stock-tracker bg-white dark:bg-slate-800 border-slate-100 dark:border-slate-700 mb-12 shadow-3d">
        <div class="panel-header border-slate-100 dark:border-slate-700">
          <div class="flex flex-col">
            <h3 class="panel-title text-slate-900 dark:text-white">📉 Low Stock Tracker</h3>
            <p class="panel-sub text-slate-500 dark:text-slate-400">Items with less than 20% of target stock remaining</p>
          </div>
        </div>
        <div class="table-wrap">
          <table class="inv-table">
            <thead class="bg-slate-50 dark:bg-slate-900/50">
              <tr>
                <th class="text-slate-500 dark:text-slate-400">Item</th>
                <th class="text-slate-500 dark:text-slate-400">Store</th>
                <th class="text-slate-500 dark:text-slate-400">Current Stock</th>
                <th class="text-slate-500 dark:text-slate-400">Target Stock</th>
                <th class="text-slate-500 dark:text-slate-400">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
              <tr v-for="item in lowStockItems" :key="`low-${item.id}`" class="text-slate-700 dark:text-slate-200">
                <td><strong>{{ item.name }}</strong></td>
                <td>{{ item.store ? item.store.name : 'N/A' }}</td>
                <td class="font-black text-rose-500">{{ item.current_stock }}</td>
                <td class="font-medium text-slate-400">{{ item.daily_target_stock }}</td>
                <td>
                  <span class="status-badge danger">LOW STOCK</span>
                </td>
              </tr>
              <tr v-if="!lowStockItems || lowStockItems.length === 0">
                <td colspan="5" class="no-data text-slate-400 dark:text-slate-500">No low stock items tracked</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Editable Items -->
      <div class="panel bg-white dark:bg-slate-800 border-slate-100 dark:border-slate-700/50 shadow-premium rounded-[2.5rem] overflow-hidden">
        <div class="panel-header border-slate-100 dark:border-slate-700 p-8 flex flex-col sm:flex-row gap-6">
          <div class="flex-1">
            <h3 class="panel-title text-2xl font-black text-slate-900 dark:text-white">Daily Target & Current Stocks</h3>
            <p class="text-sm font-medium text-slate-400 mt-1 uppercase tracking-wider">Manage daily inventory targets and current levels</p>
          </div>
          <button
            @click="saveAll"
            :disabled="bulkForm.processing"
            class="bulk-save-btn px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white shadow-xl shadow-blue-500/20 disabled:opacity-50 disabled:grayscale transition-all active:scale-95"
          >
            {{ bulkForm.processing ? 'Saving...' : '💾 Save All Changes' }}
          </button>
        </div>
        <div class="filter-bar bg-slate-50/50 dark:bg-slate-900/50 border-y border-slate-100 dark:border-slate-700 p-6">
          <div class="search-wrap">
            <span class="search-icon">🔍</span>
            <input v-model="search" type="text" placeholder="Search item or store..." class="filter-input bg-white dark:bg-slate-800 text-slate-900 dark:text-white border-slate-200 dark:border-slate-700 focus:ring-4 focus:ring-blue-500/10 rounded-2xl" />
          </div>
          <select v-model="storeFilter" class="filter-input bg-white dark:bg-slate-800 text-slate-900 dark:text-white border-slate-200 dark:border-slate-700 focus:ring-4 focus:ring-blue-500/10 rounded-2xl">
            <option value="">All Stores</option>
            <option v-for="s in stores" :key="`filter-${s.id}`" :value="s.id">{{ s.name }}</option>
          </select>
          <select v-model="availabilityFilter" class="filter-input bg-white dark:bg-slate-800 text-slate-900 dark:text-white border-slate-200 dark:border-slate-700 focus:ring-4 focus:ring-blue-500/10 rounded-2xl">
            <option value="">All Availability</option>
            <option :value="true">Available</option>
            <option :value="false">Unavailable</option>
          </select>
        </div>
        <div class="table-wrap">
          <table class="inv-table w-full">
            <thead class="bg-slate-50 dark:bg-slate-900/50">
              <tr>
                <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Store</th>
                <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Item</th>
                <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 num">Current Stock</th>
                <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 num">Target Stock</th>
                <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Availability</th>
                <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 num">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50">
              <tr v-for="m in filteredItems" :key="m.id" class="text-slate-700 dark:text-slate-300 hover:bg-slate-50/50 dark:hover:bg-slate-700/20 transition-colors group">
                <td class="px-8 py-6">
                  <span class="text-sm font-bold text-slate-500 dark:text-slate-400">{{ m.store_name }}</span>
                </td>
                <td class="px-8 py-6">
                  <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-700 shrink-0 shadow-sm">
                      <img
                        :src="foodImageSrc(m)"
                        class="w-full h-full object-cover"
                        @error="(e) => e.target.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(m.name)}&background=random&size=128`"
                      />
                    </div>
                    <span class="text-base font-black text-slate-900 dark:text-white">{{ m.name }}</span>
                  </div>
                </td>
                <td class="px-8 py-6 num">
                  <input v-model.number="m.current_stock" type="number" min="0" class="cell-input max-w-[100px] ml-auto bg-white dark:bg-slate-900 text-slate-900 dark:text-white border-slate-200 dark:border-slate-700 focus:ring-4 focus:ring-blue-500/10 rounded-xl font-bold text-center" />
                </td>
                <td class="px-8 py-6 num">
                  <input v-model.number="m.daily_target_stock" type="number" min="1" class="cell-input max-w-[100px] ml-auto bg-white dark:bg-slate-900 text-slate-400 dark:text-slate-500 border-slate-200 dark:border-slate-700 focus:ring-4 focus:ring-blue-500/10 rounded-xl font-bold text-center" />
                </td>
                <td class="px-8 py-6">
                  <div class="availability-cell">
                    <select v-model="m.is_available" class="cell-input bg-white dark:bg-slate-900 text-slate-900 dark:text-white border-slate-200 dark:border-slate-700 focus:ring-4 focus:ring-blue-500/10 rounded-xl text-sm font-bold" :class="{ 'text-rose-500 border-rose-200 dark:border-rose-900/50': m.current_stock <= 0 }">
                      <option :value="true">Available</option>
                      <option :value="false">Unavailable</option>
                    </select>
                    <span v-if="m.current_stock <= 0" class="stock-warning text-[9px] font-black text-rose-500 tracking-tighter mt-1">Sold Out</span>
                  </div>
                </td>
                <td class="px-8 py-6 num">
                  <button @click="save(m)" class="save-cell-btn px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl shadow-lg shadow-emerald-500/20 transition-all active:scale-90 font-black text-[10px] uppercase tracking-widest">Save</button>
                </td>
              </tr>
              <tr v-if="filteredItems.length === 0">
                <td colspan="6" class="px-8 py-20 text-center">
                  <div class="flex flex-col items-center gap-4">
                    <span class="text-5xl opacity-20">🔎</span>
                    <p class="text-lg font-black text-slate-300 dark:text-slate-600 uppercase tracking-widest">No matching items found</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { router, useForm } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'

const props = defineProps({
  stores: Array,
  overall: Object,
  lowStockItems: Array,
})

const search = ref('')
const storeFilter = ref('')
const availabilityFilter = ref('')
const items = ref([])

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

const bulkForm = useForm({
  items: []
})

watch(() => props.stores, (newStores) => {
  const flat = (newStores || []).flatMap(s => (s.menu_items || []).map(m => ({
    id: m.id,
    name: m.name,
    current_stock: m.current_stock ?? 0,
    daily_target_stock: m.daily_target_stock ?? 50,
    is_available: m.is_available,
    store_id: s.id,
    store_name: s.name,
  })))
  items.value = flat
}, { immediate: true })

const filteredItems = computed(() => {
  let data = items.value
  if (storeFilter.value) {
    const sid = Number(storeFilter.value)
    data = data.filter(i => i.store_id === sid)
  }
  if (availabilityFilter.value !== '') {
    data = data.filter(i => i.is_available === availabilityFilter.value)
  }
  if (search.value) {
    const q = search.value.toLowerCase()
    data = data.filter(i => i.name.toLowerCase().includes(q) || i.store_name.toLowerCase().includes(q))
  }
  return data
})

/** Food images for menu items — by item name when no upload */
const FOOD_IMAGES = {
  default: 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=200&h=200&fit=crop&q=80',
  sisig: 'https://images.unsplash.com/photo-1559847844-5315695dadae?w=200&h=200&fit=crop&q=80',
  fruit: 'https://images.unsplash.com/photo-1619566636858-adf3ef46400b?w=200&h=200&fit=crop&q=80',
  mangga: 'https://images.unsplash.com/photo-1553279768-865429fa0078?w=200&h=200&fit=crop&q=80',
  pinya: 'https://images.unsplash.com/photo-1550258987-190a2d41a8ba?w=200&h=200&fit=crop&q=80',
  chicken: 'https://images.unsplash.com/photo-1567620832903-1fcdec272204?w=200&h=200&fit=crop&q=80',
  fried: 'https://images.unsplash.com/photo-1626645738196-c2a72c7d649a?w=200&h=200&fit=crop&q=80',
  takoyaki: 'https://images.unsplash.com/photo-1579871494447-9811cf80d66c?w=200&h=200&fit=crop&q=80',
  street: 'https://images.unsplash.com/photo-1555126634-323283e090fa?w=200&h=200&fit=crop&q=80',
  icecream: 'https://images.unsplash.com/photo-1560008581-98ca0a87a908?w=200&h=200&fit=crop&q=80',
  shawarma: 'https://images.unsplash.com/photo-1529006557810-274b9b2fc783?w=200&h=200&fit=crop&q=80',
  drinks: 'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=200&h=200&fit=crop&q=80',
  juice: 'https://images.unsplash.com/photo-1546173159-315724a31696?w=200&h=200&fit=crop&q=80',
  buko: 'https://images.unsplash.com/photo-1546173159-315724a31696?w=200&h=200&fit=crop&q=80',
  shake: 'https://images.unsplash.com/photo-1572490122747-3968ff75e2c1?w=200&h=200&fit=crop&q=80',
  snack: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=200&h=200&fit=crop&q=80',
  dessert: 'https://images.unsplash.com/photo-1488477181946-6428a0291777?w=200&h=200&fit=crop&q=80',
  cake: 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=200&h=200&fit=crop&q=80',
  burger: 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=200&h=200&fit=crop&q=80',
  pizza: 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=200&h=200&fit=crop&q=80',
  rice: 'https://images.unsplash.com/photo-1512058564366-18510be2db19?w=200&h=200&fit=crop&q=80',
  noodles: 'https://images.unsplash.com/photo-1569718212165-3a244849bc2e?w=200&h=200&fit=crop&q=80',
}

function foodImageSrc(item) {
  if (!item) return FOOD_IMAGES.default
  if (item.image) {
    if (String(item.image).startsWith('http')) return item.image
    return '/storage/' + String(item.image).replace(/^[\\/]+/, '')
  }
  const name = (item.name || '').toLowerCase()
  if (name.includes('sisig')) return FOOD_IMAGES.sisig
  if (name.includes('mangga') || name.includes('mango')) return FOOD_IMAGES.mangga
  if (name.includes('pinya') || name.includes('pineapple')) return FOOD_IMAGES.pinya
  if (name.includes('fruit') || name.includes('slice')) return FOOD_IMAGES.fruit
  if (name.includes('chicken') || name.includes('fried')) return FOOD_IMAGES.chicken
  if (name.includes('takoyaki') || name.includes('japanese')) return FOOD_IMAGES.takoyaki
  if (name.includes('street') || name.includes('fish')) return FOOD_IMAGES.street
  if (name.includes('ice') || name.includes('cream') || name.includes('rutto')) return FOOD_IMAGES.icecream
  if (name.includes('shawarma')) return FOOD_IMAGES.shawarma
  if (name.includes('juice') || name.includes('buko')) return FOOD_IMAGES.buko
  if (name.includes('shake') || name.includes('fresh')) return FOOD_IMAGES.shake
  if (name.includes('snack') || name.includes('tuhog') || name.includes('merienda')) return FOOD_IMAGES.snack
  if (name.includes('dessert') || name.includes('sweet') || name.includes('corner')) return FOOD_IMAGES.dessert
  if (name.includes('cake')) return FOOD_IMAGES.cake
  if (name.includes('burger')) return FOOD_IMAGES.burger
  if (name.includes('pizza')) return FOOD_IMAGES.pizza
  if (name.includes('rice')) return FOOD_IMAGES.rice
  if (name.includes('noodle') || name.includes('pancit')) return FOOD_IMAGES.noodles
  if (name.includes('drink') || name.includes('beverage')) return FOOD_IMAGES.drinks
  return FOOD_IMAGES.default
}

const save = (m) => {
  router.patch(`/admin/inventory/menu-items/${m.id}`, {
    menu_id: m.id,
    current_stock: m.current_stock ?? 0,
    daily_target_stock: m.daily_target_stock ?? 50,
    is_available: !!m.is_available,
  }, {
    preserveScroll: true,
    preserveState: true
  })
}

const saveAll = () => {
  bulkForm.setData('items', items.value.map(i => ({
    id: i.id,
    current_stock: i.current_stock,
    daily_target_stock: i.daily_target_stock,
    is_available: i.is_available
  })))

  bulkForm.post(route('admin.inventory.bulk-update'), {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      // Success handled by layout flash
    }
  })
}
</script>

<style scoped>
.inventory-page { font-family: 'Plus Jakarta Sans', sans-serif; }

.page-title { font-family: 'Syne', sans-serif; font-size: 1.4rem; font-weight: 800; }
.page-sub { font-size: 0.85rem; }

.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
}
.stat-card {
  border-radius: 2rem;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1.25rem;
  transition: all 0.3s;
}
.stat-card:hover {
  transform: translateY(-5px);
}
.stat-card .stat-icon { font-size: 2rem; }
.stat-card .stat-info .stat-value { font-weight: 900; font-size: 1.75rem; tracking: -0.05em; }
.stat-card .stat-label { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; tracking: 0.1em; }

.panel { border-radius: 2.5rem; overflow: hidden; }
.panel-header { padding: 2rem; display: flex; align-items: center; justify-content: space-between; }
.panel-title { font-family: 'Syne', sans-serif; font-size: 1.25rem; font-weight: 800; }
.table-wrap { overflow-x: auto; }

.inv-table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
.inv-table th {
  padding: 1.25rem 2rem;
  text-align: left;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.15em;
  font-size: 0.65rem;
}
.inv-table td { padding: 1.5rem 2rem; text-align: left; }
.inv-table th.num, .inv-table td.num { text-align: right; }

.status-badge { font-size: 0.65rem; font-weight: 900; border-radius: 999px; padding: 0.35rem 0.85rem; text-transform: uppercase; letter-spacing: 0.1em; }
.status-badge.open { @apply bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400; }
.status-badge.closed { @apply bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400; }

.status-badge.danger { @apply bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400; }
.status-badge.warning { @apply bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400; }
.status-badge.success { @apply bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400; }
.status-badge.neutral { @apply bg-slate-100 text-slate-700 dark:bg-slate-500/10 dark:text-slate-400; }

.text-red { @apply text-rose-500; }
.text-orange { @apply text-amber-500; }
.text-green { @apply text-emerald-500; }

.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { @apply bg-slate-200 dark:bg-slate-700 rounded-full; }

@media (max-width: 768px) {
  .stats-grid { grid-template-columns: 1fr; }
}
</style>
