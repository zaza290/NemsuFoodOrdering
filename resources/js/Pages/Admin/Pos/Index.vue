<template>
  <AdminLayout title="POS Terminal">
    <div class="pos-container space-y-8 pb-10">

      <!-- Top Metrics Bar -->
      <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="glass-card p-6 rounded-[2rem] shadow-3d border-white/50 flex items-center gap-6 group hover:scale-[1.02] transition-transform duration-500">
          <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-2xl shadow-sm group-hover:rotate-12 transition-transform">💰</div>
          <div>
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Sales Today</div>
            <div class="text-2xl font-black text-slate-900 tracking-tighter">₱{{ formatPrice(stats.total_sales_today) }}</div>
          </div>
        </div>
        <div class="glass-card p-6 rounded-[2rem] shadow-3d border-white/50 flex items-center gap-6 group hover:scale-[1.02] transition-transform duration-500">
          <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-2xl shadow-sm group-hover:rotate-12 transition-transform">📦</div>
          <div>
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Active Orders</div>
            <div class="text-2xl font-black text-slate-900 tracking-tighter">{{ stats.current_orders_count }}</div>
          </div>
        </div>
        <div class="glass-card p-6 rounded-[2rem] shadow-3d border-white/50 flex items-center gap-6 group hover:scale-[1.02] transition-transform duration-500">
          <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center text-2xl shadow-sm group-hover:rotate-12 transition-transform">🏪</div>
          <div>
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Active Stores</div>
            <div class="text-2xl font-black text-slate-900 tracking-tighter">{{ stores.length }}</div>
          </div>
        </div>
      </section>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Main: Menu & Store Selection -->
        <div class="lg:col-span-8 space-y-8">
          <!-- Store Selector Card -->
          <div class="glass-card p-8 rounded-[2.5rem] shadow-3d border-white/50">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
              <div class="space-y-1">
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Walk-in Terminal</h2>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Select a store to begin ordering</p>
              </div>
              <div class="relative w-full md:w-72">
                <select
                  v-model="selectedStoreId"
                  class="w-full pl-6 pr-10 py-4 bg-slate-50 border-none rounded-2xl text-sm font-black text-slate-700 focus:ring-2 focus:ring-blue-500 transition-all appearance-none cursor-pointer"
                >
                  <option value="">— Select Store —</option>
                  <option v-for="s in stores" :key="s.id" :value="s.id">
                    {{ s.name }} {{ s.is_open ? '🟢' : '🔴' }}
                  </option>
                </select>
                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">▼</div>
              </div>
            </div>
          </div>

          <!-- Menu Grid -->
          <div v-if="selectedStore" class="space-y-6">
            <div class="flex items-center justify-between px-4">
              <h3 class="text-xl font-black text-slate-900 tracking-tight uppercase">{{ selectedStore.name }} Menu</h3>
              <span class="px-4 py-1.5 bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest rounded-full border border-blue-100 shadow-sm">
                {{ menuItems.length }} Items Available
              </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
              <div
                v-for="item in menuItems"
                :key="item.id"
                class="group relative bg-white rounded-[2.5rem] overflow-hidden shadow-3d border border-slate-100 hover:border-blue-500/20 transition-all duration-500 flex flex-col"
                :class="{ 'opacity-60 grayscale grayscale-[50%]': !item.is_available }"
              >
                <!-- Product Image -->
                <div class="h-48 overflow-hidden relative">
                  <img :src="foodImageSrc(item)" :alt="item.name" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" @error="(e) => e.target.src = 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=400&h=400&fit=crop&q=80'" />
                  <div class="absolute inset-0 bg-gradient-to-t from-slate-900/40 to-transparent"></div>
                  <div v-if="!item.is_available" class="absolute inset-0 flex items-center justify-center bg-slate-900/20 backdrop-blur-[2px]">
                    <span class="px-4 py-2 bg-rose-500 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg">Sold Out</span>
                  </div>
                </div>

                <!-- Product Info -->
                <div class="p-6 flex-1 flex flex-col justify-between">
                  <div class="mb-4">
                    <h4 class="text-lg font-black text-slate-900 leading-tight group-hover:text-blue-600 transition-colors truncate">{{ item.name }}</h4>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">₱{{ formatPrice(item.price) }}</p>
                  </div>

                  <button
                    @click="addToCart(item)"
                    :disabled="!item.is_available"
                    class="w-full py-3 bg-slate-50 text-slate-900 text-xs font-black uppercase tracking-widest rounded-xl hover:bg-blue-600 hover:text-white transition-all active:scale-95 flex items-center justify-center gap-2 group/btn"
                  >
                    <span class="text-lg group-hover/btn:rotate-90 transition-transform">+</span> Add to Cart
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div v-else class="flex flex-col items-center justify-center py-32 glass-card rounded-[3rem] border-dashed border-slate-200">
            <div class="text-6xl mb-6 grayscale opacity-50">🍱</div>
            <p class="text-xl font-black text-slate-800 tracking-tight">No Store Selected</p>
            <p class="text-sm font-bold text-slate-400 uppercase tracking-widest mt-1">Pick a store above to view its menu</p>
          </div>
        </div>

        <!-- Right: Cart & Recent Activity -->
        <div class="lg:col-span-4 space-y-8 sticky top-28">
          <!-- Premium Cart Card -->
          <div class="glass-card rounded-[2.5rem] shadow-3d border-white/50 overflow-hidden flex flex-col max-h-[calc(100vh-160px)]">
            <header class="p-8 bg-slate-900 text-white flex items-center justify-between">
              <h3 class="text-xl font-black tracking-tight">Current Sale</h3>
              <span class="px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-[10px] font-black uppercase tracking-widest">{{ cart.length }} Items</span>
            </header>

            <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
              <div v-if="cart.length === 0" class="flex flex-col items-center justify-center py-12 text-center opacity-40">
                <div class="text-4xl mb-4">🛒</div>
                <p class="text-sm font-black uppercase tracking-widest text-slate-500 leading-tight">Your cart is <br/>waiting for flavors</p>
              </div>

              <div v-else class="space-y-6">
                <div v-for="line in cart" :key="line.id" class="flex items-center gap-4 group">
                  <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-xl shrink-0 group-hover:scale-110 transition-transform">🍕</div>
                  <div class="flex-1 min-w-0">
                    <div class="text-sm font-black text-slate-900 truncate">{{ line.name }}</div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">₱{{ formatPrice(line.price) }} each</div>
                  </div>
                  <div class="flex items-center gap-3">
                    <div class="flex items-center bg-slate-50 rounded-lg p-1 border border-slate-100">
                      <button @click="changeQty(line, -1)" class="w-6 h-6 flex items-center justify-center text-slate-400 hover:text-rose-500 font-bold transition-colors">−</button>
                      <span class="w-6 text-center text-xs font-black text-slate-700">{{ line.quantity }}</span>
                      <button @click="changeQty(line, 1)" class="w-6 h-6 flex items-center justify-center text-slate-400 hover:text-blue-600 font-bold transition-colors">+</button>
                    </div>
                    <div class="text-sm font-black text-slate-900 min-w-[60px] text-right">₱{{ formatPrice(line.price * line.quantity) }}</div>
                  </div>
                </div>
              </div>
            </div>

            <footer v-if="cart.length > 0" class="p-8 border-t border-slate-100 bg-slate-50/50 space-y-6">
              <div class="flex justify-between items-center">
                <span class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Subtotal</span>
                <span class="text-2xl font-black text-blue-600 tracking-tighter">₱{{ formatPrice(total) }}</span>
              </div>

              <div class="space-y-4">
                <div class="space-y-2">
                  <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Customer Name</label>
                  <input
                    v-model="form.customer_name"
                    type="text"
                    placeholder="Enter customer name..."
                    class="w-full px-4 py-3 bg-white border-none rounded-xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 shadow-sm transition-all"
                  />
                </div>

                <div class="space-y-2">
                  <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Payment</label>
                  <select v-model="form.payment_method" class="w-full px-4 py-3 bg-white border-none rounded-xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 shadow-sm transition-all">
                    <option value="cod">💵 Cash on Hand</option>
                    <option value="gcash">📱 GCash Pay</option>
                    <option value="paymaya">💳 PayMaya</option>
                  </select>
                </div>

                <button
                  @click="completeSale"
                  :disabled="form.processing || !selectedStoreId"
                  class="w-full py-5 bg-blue-600 text-white text-sm font-black uppercase tracking-widest rounded-[1.5rem] shadow-lg shadow-blue-500/30 hover:bg-blue-700 transition-all active:scale-95 disabled:opacity-50 disabled:grayscale"
                >
                  {{ form.processing ? 'Completing...' : 'Pay & Checkout' }}
                </button>
                <button @click="cart = []" class="w-full text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-rose-500 transition-colors">Discard Cart</button>
              </div>
            </footer>
          </div>

          <!-- Recent Walk-in History (Compact) -->
          <div class="px-4">
            <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Recent Walk-ins</h4>
            <div class="space-y-3">
              <div v-for="order in recent_walk_in.slice(0, 3)" :key="order.id" class="p-4 glass-card rounded-2xl flex items-center justify-between border-white/50">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-sm">✅</div>
                  <div class="text-[10px] font-black text-slate-900 tracking-tight">{{ order.order_number }}</div>
                </div>
                <div class="text-[10px] font-black text-blue-600">₱{{ formatPrice(order.total_amount) }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- POS Receipt Modal -->
    <ReceiptModal
      v-if="lastOrder"
      :show="showReceipt"
      :store-name="lastOrder.storeName"
      :customer-name="lastOrder.customerName"
      :order-number="lastOrder.orderNumber"
      :items="lastOrder.items"
      :total="lastOrder.total"
      :payment-method="lastOrder.paymentMethod"
      @close="showReceipt = false"
    />
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ReceiptModal from '@/Components/ReceiptModal.vue'

const props = defineProps({
  stores: Array,
  recent_walk_in: Array,
  stats: Object,
  flash: Object
})

const selectedStoreId = ref('')
const cart = ref([])
const showReceipt = ref(false)
const lastOrder = ref(null)

const selectedStore = computed(() =>
  props.stores?.find(s => s.id === selectedStoreId.value) || null
)

const menuItems = computed(() => selectedStore.value?.menu_items || [])

function addToCart(item) {
  const existing = cart.value.find(i => i.id === item.id)
  if (existing) {
    if (existing.quantity < item.stock_count) existing.quantity++
  } else {
    if (item.stock_count > 0) {
      cart.value.push({ id: item.id, name: item.name, price: parseFloat(item.price), quantity: 1, stock_count: item.stock_count })
    }
  }
}

function changeQty(line, delta) {
  const newQty = line.quantity + delta
  if (newQty > line.stock_count) return
  if (newQty <= 0) {
    cart.value = cart.value.filter(i => i.id !== line.id)
  } else {
    line.quantity = newQty
  }
}

const total = computed(() =>
  cart.value.reduce((sum, i) => sum + i.price * i.quantity, 0)
)

const form = useForm({
  store_id: '',
  customer_name: '',
  payment_method: 'cod',
  items: [],
})

function completeSale() {
  if (!selectedStoreId.value || cart.value.length === 0) return
  form.store_id = selectedStoreId.value
  form.items = cart.value.map(i => ({ menu_item_id: i.id, quantity: i.quantity }))
  form.clearErrors()

  // Store data for the receipt before the success resets everything
  const receiptData = {
    storeName: selectedStore.value.name,
    customerName: form.customer_name || 'Walk-in',
    items: [...cart.value],
    total: total.value,
    paymentMethod: form.payment_method,
    orderNumber: 'POS-' + Math.random().toString(36).substr(2, 9).toUpperCase() // Fallback until we get it from server
  }

  form.post(route('admin.pos.store'), {
    preserveScroll: true,
    onSuccess: (page) => {
      // Get the actual order data from flash or recent walk-in
      const latestOrder = page.props.recent_walk_in?.[0]
      if (latestOrder) {
        receiptData.orderNumber = latestOrder.order_number
      }

      lastOrder.value = receiptData
      showReceipt.value = true
      cart.value = []
      form.reset('customer_name', 'items')
    },
  })
}

function formatPrice(val) {
  if (!val) return '0.00'
  return parseFloat(val).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

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
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  @apply bg-slate-200 rounded-full;
}
</style>
