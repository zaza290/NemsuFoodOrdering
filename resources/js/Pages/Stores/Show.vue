<template>
  <AppLayout :title="store.name">
    <div class="store-page space-y-8 md:space-y-12 pb-32">
      <!-- Premium Store Header - Native App Optimized -->
      <div class="relative h-[40vh] md:h-[50vh] min-h-[300px] md:min-h-[400px] overflow-hidden md:rounded-[4rem] shadow-2xl group md:mx-4 md:mt-4">
        <div class="absolute inset-0">
          <img
            :src="storeImageSrc(store)"
            :alt="store.name"
            class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110"
            @error="(e) => e.target.src = 'https://images.unsplash.com/photo-1555392886-6f5ac0d4c58b?w=1200&q=80'"
          />
          <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 to-transparent"></div>
        </div>

        <div class="absolute inset-0 flex flex-col justify-end p-6 md:p-16">
          <div class="container mx-auto">
            <Link :href="route('stores.index')" class="hidden md:inline-flex items-center gap-3 px-6 py-3 mb-12 bg-white/10 backdrop-blur-xl border border-white/20 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-white/20 transition-all active:scale-95 group/back">
              <span class="group-hover/back:-translate-x-1 transition-transform">←</span> Back to Stores
            </Link>

            <div class="flex flex-col md:flex-row items-start md:items-center gap-6 md:gap-8">
              <div class="w-24 h-24 md:w-40 md:h-40 rounded-[2rem] md:rounded-[3rem] bg-white/10 backdrop-blur-2xl border border-white/30 p-1.5 md:p-2 shadow-2xl shrink-0 overflow-hidden">
                <img
                  :src="storeImageSrc(store)"
                  :alt="store.name"
                  class="w-full h-full object-cover rounded-[1.5rem] md:rounded-[2.5rem]"
                  @error="(e) => e.target.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(store.name)}&background=random&size=256`"
                />
              </div>

              <div class="flex-1 text-white space-y-2 md:space-y-4">
                <div class="flex items-center gap-3">
                  <span :class="[
                    'px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-[0.15em] backdrop-blur-md border shadow-2xl',
                    store.is_open
                      ? 'bg-emerald-500/80 text-white border-emerald-400/30'
                      : 'bg-rose-500/80 text-white border-rose-400/30'
                  ]">
                    {{ store.is_open ? '● Open' : '● Closed' }}
                  </span>
                </div>
                <h1 class="text-4xl md:text-7xl font-black tracking-tighter drop-shadow-2xl leading-none">{{ store.name }}</h1>
                <div class="flex items-center gap-4 md:gap-8">
                  <div class="flex items-center gap-2 md:gap-3 px-3 py-1.5 bg-white/10 backdrop-blur-md rounded-xl border border-white/10">
                    <span class="text-amber-400 text-sm md:text-lg">★</span>
                    <span class="text-lg md:text-xl font-black">{{ averageRating ? parseFloat(averageRating).toFixed(1) : '0.0' }}</span>
                    <span class="text-[10px] font-bold text-white/60">({{ totalRatings }} reviews)</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 md:gap-16">
          <!-- Menu Column -->
          <div class="lg:col-span-8 space-y-10 md:space-y-16">
            <div class="flex items-end justify-between px-2 md:px-6 border-b-2 border-slate-100 dark:border-slate-800 pb-6 md:pb-10">
              <div class="space-y-1">
                <span class="inline-block px-3 py-1 bg-blue-500/10 text-blue-600 dark:text-blue-400 text-[9px] font-black uppercase tracking-[0.2em] rounded-full">Menu</span>
                <h2 class="text-3xl md:text-5xl font-black text-slate-900 dark:text-white tracking-tighter">Explore Flavors</h2>
              </div>
            </div>

            <div v-if="menuItems.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-8">
              <div
                v-for="item in menuItems"
                :key="item.id"
                class="group relative flex flex-row items-center gap-4 md:gap-6 p-4 md:p-8 bg-white dark:bg-slate-800 rounded-[2rem] md:rounded-[2.5rem] shadow-sm hover:shadow-xl border border-slate-100 dark:border-slate-700/50 transition-all duration-500"
                :class="{ 'opacity-60 grayscale cursor-not-allowed': !isPurchasable(item) }"
              >
                <!-- Item Image - App Style -->
                <div class="w-24 h-24 md:w-32 md:h-32 shrink-0 rounded-2xl md:rounded-3xl overflow-hidden shadow-md relative">
                  <img
                    :src="foodImageSrc(item)"
                    :alt="item.name"
                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                    loading="lazy"
                  />
                  <div v-if="!isPurchasable(item)" class="absolute inset-0 bg-slate-900/60 flex items-center justify-center backdrop-blur-sm">
                    <span class="text-white text-[8px] font-black uppercase tracking-widest border border-white/20 px-2 py-1 rounded-full">{{ getStatusLabel(item) }}</span>
                  </div>
                </div>

                <!-- Item Info -->
                <div class="flex-1 min-w-0 flex flex-col justify-between h-full py-1">
                  <div>
                    <h3 class="text-base md:text-xl font-black text-slate-900 dark:text-white truncate tracking-tight mb-0.5">{{ item.name }}</h3>
                    <p v-if="item.description" class="text-[10px] md:text-xs font-medium text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
                      {{ item.description }}
                    </p>
                  </div>

                  <div class="flex items-center justify-between mt-3">
                    <div class="text-base md:text-lg font-black text-blue-600 dark:text-blue-400">₱{{ parseFloat(item.price).toFixed(2) }}</div>

                    <!-- Actions - Optimized for Touch -->
                    <div v-if="isPurchasable(item)" class="flex items-center gap-2 p-1 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-100 dark:border-slate-700 shrink-0">
                      <button @click="decreaseQty(item)" class="w-8 h-8 md:w-10 md:h-10 flex items-center justify-center rounded-lg md:rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-black shadow-sm active:scale-90 disabled:opacity-30" :disabled="getQty(item.id) === 0">
                        −
                      </button>
                      <span class="w-4 md:w-6 text-center text-xs md:text-sm font-black text-slate-900 dark:text-white">{{ getQty(item.id) }}</span>
                      <button @click="increaseQty(item)" class="w-8 h-8 md:w-10 md:h-10 flex items-center justify-center rounded-lg md:rounded-xl bg-blue-600 text-white font-black shadow-sm active:scale-90" :disabled="getQty(item.id) >= item.current_stock">
                        +
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Mobile Sticky View Cart (Visible only on mobile when items in cart) -->
            <div v-if="cart.length > 0" class="md:hidden fixed bottom-24 left-0 right-0 px-6 z-[90]">
              <button @click="scrollToCart" class="w-full py-5 bg-blue-600 text-white rounded-[2rem] shadow-2xl shadow-blue-500/40 flex items-center justify-between px-8 animate-bounce-in">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center text-sm font-black">{{ totalItems }}</div>
                  <span class="text-xs font-black uppercase tracking-widest">View Cart</span>
                </div>
                <span class="text-lg font-black">₱{{ total.toFixed(2) }}</span>
              </button>
            </div>

            <!-- Styled Empty State for Menu -->
            <div v-else class="py-32 flex flex-col items-center text-center space-y-8 bg-white dark:bg-slate-800 rounded-[4rem] border-4 border-dashed border-slate-100 dark:border-slate-700/50">
              <div class="w-32 h-32 bg-slate-50 dark:bg-slate-900 rounded-[3rem] flex items-center justify-center text-6xl opacity-20">🍽️</div>
              <div class="space-y-2">
                <h3 class="text-2xl font-black text-slate-900 dark:text-white">Kitchen is Resting</h3>
                <p class="text-slate-400 dark:text-slate-500 font-bold max-w-xs">We are currently updating our menu items. Check back very soon!</p>
              </div>
              <Link :href="route('stores.index')" class="px-8 py-4 bg-blue-600 text-white text-xs font-black uppercase tracking-widest rounded-[2rem] shadow-xl shadow-blue-500/20 hover:scale-105 transition-all">Explore Other Stores</Link>
            </div>

            <!-- Enhanced Reviews Section -->
            <div class="space-y-12 pt-16 border-t border-slate-100 dark:border-slate-800">
              <h2 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight">Community Reviews</h2>
              <div v-if="store.ratings.length === 0" class="flex flex-col items-center justify-center py-24 bg-white dark:bg-slate-800 rounded-[4rem] border-4 border-dashed border-slate-100 dark:border-slate-700/50">
                <span class="text-6xl mb-6 opacity-20">💬</span>
                <p class="text-2xl font-black text-slate-300 dark:text-slate-600 uppercase tracking-widest">No reviews yet</p>
                <p class="text-slate-400 dark:text-slate-500 font-bold mt-2">Be the first to share your experience!</p>
              </div>
              <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                <div v-for="review in store.ratings" :key="review.id" class="p-8 bg-white dark:bg-slate-800 rounded-[3rem] shadow-premium border border-slate-50 dark:border-slate-700/50 space-y-6">
                  <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center text-white text-lg font-black shadow-lg">
                      {{ review.user.name[0] }}
                    </div>
                    <div>
                      <div class="text-base font-black text-slate-900 dark:text-white">{{ review.user.name }}</div>
                      <div class="flex text-amber-400 text-xs mt-1">
                        <span v-for="i in 5" :key="i" :class="['star', { 'text-slate-200 dark:text-slate-700': i > review.rating }]">★</span>
                      </div>
                    </div>
                  </div>
                  <p v-if="review.comment" class="text-sm font-medium text-slate-600 dark:text-slate-400 italic leading-relaxed bg-slate-50 dark:bg-slate-900/50 p-4 rounded-2xl">
                    "{{ review.comment }}"
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Sticky Cart Column -->
          <div id="cart-column" class="lg:col-span-4">
            <div class="sticky top-32 space-y-8">
              <div class="bg-white dark:bg-slate-800 rounded-[4rem] p-10 shadow-3d border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group/cart-panel">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-600/5 dark:bg-blue-400/5 rounded-full -mr-16 -mt-16 transition-transform duration-1000 group-hover/cart-panel:scale-150"></div>

                <div class="flex items-center justify-between mb-12 relative z-10">
                  <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Your Cart</h2>
                  <div class="w-10 h-10 bg-blue-600 text-white text-xs font-black rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/20">{{ totalItems }}</div>
                </div>

                <div v-if="cart.length === 0" class="flex flex-col items-center justify-center py-20 text-center space-y-6 relative z-10">
                  <div class="w-24 h-24 bg-slate-50 dark:bg-slate-900 rounded-[2.5rem] flex items-center justify-center shadow-inner group/empty-cart transition-all duration-500 hover:scale-110">
                    <span class="text-4xl opacity-40 group-hover/empty-cart:opacity-100 transition-opacity">🛒</span>
                  </div>
                  <div class="space-y-1">
                    <p class="text-xl font-black text-slate-900 dark:text-white">Cart is empty</p>
                    <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">Start adding flavors!</p>
                  </div>
                </div>

                <!-- Cart Items -->
                <div v-else class="space-y-8 relative z-10">
                  <div class="space-y-6 max-h-[45vh] overflow-y-auto pr-4 custom-scrollbar">
                    <div v-for="item in cart" :key="item.id" class="flex justify-between items-center group/item border-b border-slate-50 dark:border-slate-700/50 pb-4">
                      <div class="min-w-0 space-y-1">
                        <div class="text-sm font-black text-slate-900 dark:text-white truncate group-hover/item:text-blue-600 transition-colors">{{ item.name }}</div>
                        <div class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">₱{{ item.price }} × {{ item.quantity }}</div>
                      </div>
                      <div class="text-sm font-black text-slate-900 dark:text-white">₱{{ (item.price * item.quantity).toFixed(2) }}</div>
                    </div>
                  </div>

                  <div class="pt-8 border-t border-slate-100 dark:border-slate-700 space-y-6">
                    <div class="flex justify-between items-center px-2">
                      <span class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">Total Amount</span>
                      <span class="text-3xl font-black text-blue-600 dark:text-blue-400">₱{{ total.toFixed(2) }}</span>
                    </div>

                    <!-- Payment & Delivery Form -->
                    <div class="space-y-6 pt-4">
                      <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.25em] ml-4">Payment Method</label>
                        <select v-model="form.payment_method" class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-[2rem] text-sm font-black text-slate-700 dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all shadow-inner">
                          <option value="cod">💵 Cash on Delivery</option>
                          <option value="gcash">📱 GCash</option>
                          <option value="paymaya">💳 PayMaya</option>
                        </select>
                      </div>

                      <div v-if="form.payment_method !== 'cod'" class="space-y-6 animate-in fade-in slide-in-from-top-4 duration-500">
                        <div class="space-y-3">
                          <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.25em] ml-4">Account Number</label>
                          <input v-model="form.account_number" type="text" placeholder="09XXXXXXXXX" class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-[2rem] text-sm font-black text-slate-700 dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all shadow-inner placeholder:text-slate-300 dark:placeholder:text-slate-700" />
                        </div>
                        <div class="space-y-3">
                          <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.25em] ml-4">Reference Number</label>
                          <input v-model="form.reference_number" type="text" placeholder="Optional" class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-[2rem] text-sm font-black text-slate-700 dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all shadow-inner placeholder:text-slate-300 dark:placeholder:text-slate-700" />
                        </div>
                      </div>

                      <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.25em] ml-4">Delivery Address</label>
                        <input v-model="form.delivery_address" type="text" placeholder="e.g. Room 101, Building A" class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-[2rem] text-sm font-black text-slate-700 dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all shadow-inner placeholder:text-slate-300 dark:placeholder:text-slate-700" />
                      </div>

                      <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.25em] ml-4">Contact Phone</label>
                        <input v-model="form.contact_phone" type="text" placeholder="09XXXXXXXXX" class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-[2rem] text-sm font-black text-slate-700 dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all shadow-inner placeholder:text-slate-300 dark:placeholder:text-slate-700" />
                      </div>

                      <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.25em] ml-4">Special Notes (Optional)</label>
                        <textarea v-model="form.notes" rows="2" placeholder="Any special instructions?" class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-[2rem] text-sm font-black text-slate-700 dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all shadow-inner resize-none placeholder:text-slate-300 dark:placeholder:text-slate-700"></textarea>
                      </div>

                      <div v-if="form.payment_method !== 'cod'" class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.25em] ml-4">Proof of Payment</label>
                        <input type="file" accept="image/*" @change="onProofSelect" class="w-full px-6 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-[2rem] text-[10px] font-black text-slate-400 file:mr-4 file:py-2 file:px-6 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-blue-600 file:text-white hover:file:bg-blue-700 transition-all shadow-inner cursor-pointer" />
                      </div>

                      <div class="pt-8 space-y-4">
                        <button
                          @click="placeOrder"
                          :disabled="form.processing || !form.delivery_address || !form.contact_phone"
                          class="w-full py-6 bg-blue-600 text-white text-xs font-black uppercase tracking-[0.2em] rounded-[2.5rem] shadow-2xl shadow-blue-500/40 hover:bg-blue-700 hover:scale-[1.02] transition-all active:scale-95 disabled:opacity-50 disabled:grayscale disabled:cursor-not-allowed"
                        >
                          {{ form.processing ? 'Processing...' : 'Complete Order' }}
                        </button>
                        <button @click="clearCart" class="w-full py-4 bg-slate-50 dark:bg-slate-900 text-slate-400 dark:text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] rounded-[2rem] hover:bg-rose-500 hover:text-white dark:hover:bg-rose-500 transition-all">
                          Clear Cart
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  store: {
    type: Object,
    required: true
  },
  averageRating: Number,
  totalRatings: Number,
})

// Normalize menu items from either snake_case or camelCase props
const menuItems = computed(() => {
  if (!props.store) return []
  const items = props.store.menu_items || props.store.menuItems || []
  console.log('Store items loaded:', items.length, items)
  return items
})

const cart = ref([])

const form = useForm({
  store_id: props.store.id,
  items: [],
  payment_method: 'cod',
  delivery_address: '',
  contact_phone: '',
  account_number: '',
  reference_number: '',
  proof_image: null,
  notes: '',
})

const getQty = (id) => {
  const item = cart.value.find(i => i.id === id)
  return item ? item.quantity : 0
}

const isPurchasable = (item) => {
  if (!item.is_available) return false
  if (item.current_stock <= 0) return false
  return true
}

const getStatusLabel = (item) => {
  if (!item.is_available) return 'Unavailable'
  if (item.current_stock <= 0) return 'Sold Out'
  return 'Unavailable'
}

const increaseQty = (menuItem) => {
  const existing = cart.value.find(i => i.id === menuItem.id)
  if (existing) {
    if (existing.quantity < menuItem.current_stock) {
      existing.quantity++
    }
  } else {
    if (menuItem.current_stock > 0) {
      cart.value.push({ id: menuItem.id, name: menuItem.name, price: parseFloat(menuItem.price), quantity: 1 })
    }
  }
}

const decreaseQty = (menuItem) => {
  const idx = cart.value.findIndex(i => i.id === menuItem.id)
  if (idx === -1) return
  if (cart.value[idx].quantity > 1) cart.value[idx].quantity--
  else cart.value.splice(idx, 1)
}

const clearCart = () => { cart.value = [] }

const total = computed(() => cart.value.reduce((sum, i) => sum + i.price * i.quantity, 0))
const totalItems = computed(() => cart.value.reduce((sum, i) => sum + i.quantity, 0))

const placeOrder = () => {
  form.items = cart.value.map(i => ({ menu_item_id: i.id, quantity: i.quantity }))
  form.post(route('orders.store'), { forceFormData: true })
}

const cartSection = ref(null)
const scrollToCart = () => {
  const el = document.getElementById('cart-column')
  if (el) el.scrollIntoView({ behavior: 'smooth' })
}

const onProofSelect = (e) => {
  const f = e.target.files?.[0]
  form.proof_image = f || null
}

/** Same store image as Stores listing & Admin Dashboard */
const DEFAULT_STORE_IMAGE = 'https://images.unsplash.com/photo-1555392886-6f5ac0d4c58b?w=400&h=240&fit=crop&q=80'

function storeImageSrc(store) {
  if (!store) return DEFAULT_STORE_IMAGE
  if (store.image) {
    if (String(store.image).startsWith('http') || String(store.image).startsWith('/images/')) return store.image
    return '/storage/' + String(store.image).replace(/^[\\/]+/, '')
  }
  return DEFAULT_STORE_IMAGE
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
  @apply bg-slate-200 dark:bg-slate-700 rounded-full;
}

.star {
  @apply drop-shadow-sm;
}
</style>
