<template>
  <AppLayout title="All Stores">
    <div class="stores-page space-y-10 md:space-y-16">
      <!-- Hero Section - Native App Style -->
      <header class="relative overflow-hidden rounded-[2.5rem] md:rounded-[3.5rem] bg-slate-900 py-12 md:py-20 px-6 md:px-16 shadow-2xl">
        <div class="absolute inset-0 opacity-40">
          <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=1200&q=80" alt="" class="w-full h-full object-cover" />
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent"></div>

        <div class="relative z-10 max-w-2xl">
          <div class="inline-flex items-center gap-2 px-3 py-1 mb-6 bg-blue-500/20 backdrop-blur-md border border-blue-400/20 rounded-full">
            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-300">NEMSU Campus</span>
          </div>
          <h1 class="text-4xl md:text-7xl font-black text-white leading-[0.9] mb-6 tracking-tighter">
            Craving <br/>
            <span class="text-blue-500">Something?</span>
          </h1>
          <p class="text-base md:text-xl font-medium text-slate-300 leading-relaxed mb-8 max-w-md">
            The fastest way to get your favorite campus meals delivered.
          </p>
        </div>
      </header>

      <!-- Search & Categories - Fixed on Scroll on Mobile? -->
      <section class="sticky top-20 md:top-24 z-30 py-2">
        <div class="glass-card premium-shadow rounded-3xl p-2 flex items-center gap-2 border-slate-200/50 dark:border-slate-700/50">
          <div class="flex-1 flex items-center px-4 gap-3">
            <span class="text-xl">🔍</span>
            <input
              v-model="search"
              type="text"
              placeholder="Search dishes or stores..."
              class="w-full bg-transparent border-none focus:ring-0 text-slate-900 dark:text-white font-bold placeholder:text-slate-400 py-3 text-sm md:text-base"
            />
          </div>
        </div>
      </section>

      <!-- Store Grid - App-like Cards -->
      <section>
        <div class="flex items-center justify-between mb-8 px-2">
          <h2 class="text-2xl md:text-4xl font-black text-slate-900 dark:text-white tracking-tighter">Featured Stores</h2>
          <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ filteredStores.length }} Results</span>
        </div>

        <div v-if="filteredStores.length === 0" class="flex flex-col items-center justify-center py-20 bg-white dark:bg-slate-800 rounded-[3rem] border-2 border-dashed border-slate-100 dark:border-slate-700">
          <span class="text-6xl mb-4">🏜️</span>
          <p class="text-lg font-black text-slate-900 dark:text-white">Nothing here yet</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 md:gap-8">
          <Link
            v-for="store in filteredStores"
            :key="store.id"
            :href="route('stores.show', store.slug)"
            class="group flex flex-col bg-white dark:bg-slate-800 rounded-[2.5rem] overflow-hidden shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 border border-slate-100 dark:border-slate-700/50"
          >
            <!-- Card Image -->
            <div class="relative h-48 md:h-56 overflow-hidden">
              <img
                :src="storeImageSrc(store)"
                :alt="store.name"
                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                @error="(e) => e.target.src = imageFallback"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent opacity-60"></div>

              <!-- Open/Closed Badge -->
              <div class="absolute top-4 left-4">
                <span :class="[
                  'px-3 py-1 rounded-xl text-[9px] font-black uppercase tracking-widest backdrop-blur-md border shadow-lg',
                  isStoreOpen(store)
                    ? 'bg-emerald-500 text-white border-emerald-400/30'
                    : 'bg-rose-500 text-white border-rose-400/30'
                ]">
                  {{ isStoreOpen(store) ? 'Open' : 'Closed' }}
                </span>
              </div>

              <!-- Rating Badge -->
              <div class="absolute bottom-4 right-4 bg-white/90 dark:bg-slate-800/90 backdrop-blur-md px-2 py-1 rounded-lg flex items-center gap-1 shadow-lg">
                <span class="text-amber-500 text-xs">★</span>
                <span class="text-[10px] font-black text-slate-900 dark:text-white">{{ formatRating(store.ratings_avg_rating) }}</span>
              </div>
            </div>

            <!-- Card Body -->
            <div class="p-6 md:p-8 flex-1 flex flex-col">
              <h3 class="text-xl md:text-2xl font-black text-slate-900 dark:text-white tracking-tight mb-2 group-hover:text-blue-600 transition-colors">
                {{ store.name }}
              </h3>

              <p class="text-xs md:text-sm font-medium text-slate-500 dark:text-slate-400 line-clamp-2 mb-6 leading-relaxed">
                {{ store.description || 'Quality food and quick service at NEMSU.' }}
              </p>

              <!-- Mini Menu Preview (Native App Style) -->
              <div class="flex gap-2 mb-6 overflow-x-auto no-scrollbar pb-1">
                <template v-for="item in (store.menu_items || []).slice(0, 2)" :key="item.id">
                  <span class="shrink-0 px-3 py-1.5 bg-slate-50 dark:bg-slate-900/50 text-slate-600 dark:text-slate-400 text-[9px] font-black uppercase tracking-widest rounded-xl border border-slate-100 dark:border-slate-700/50">
                    {{ item.name }}
                  </span>
                </template>
                <span v-if="(store.menu_items || []).length > 2" class="shrink-0 px-3 py-1.5 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 text-[9px] font-black uppercase tracking-widest rounded-xl">
                  +{{ (store.menu_items || []).length - 2 }}
                </span>
              </div>

              <div class="mt-auto flex items-center justify-between pt-6 border-t border-slate-50 dark:border-slate-700/50">
                <div class="flex flex-col">
                  <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Delivery</span>
                  <span class="text-xs font-bold text-slate-900 dark:text-white">15-25 min</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-500/20 group-hover:scale-110 transition-transform">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                </div>
              </div>
            </div>
          </Link>
        </div>
      </section>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({ stores: Array })

const search = ref('')

const filteredStores = computed(() => {
  const list = props.stores || []
  if (!search.value.trim()) return list
  const q = search.value.toLowerCase()
  return list.filter(s =>
    (s.name || '').toLowerCase().includes(q) ||
    (s.menu_items || []).some(m => (m.name || '').toLowerCase().includes(q))
  )
})

const DEFAULT_STORE_IMAGE = 'https://images.unsplash.com/photo-1555392886-6f5ac0d4c58b?w=400&h=240&fit=crop&q=80'
const imageFallback = 'data:image/svg+xml,' + encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" width="400" height="240" viewBox="0 0 400 240"><rect fill="#e2e8f0" width="400" height="240"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#94a3b8" font-family="sans-serif" font-size="14">Store</text></svg>')

function storeImageSrc(store) {
  if (!store) return DEFAULT_STORE_IMAGE
  if (store.image) {
    if (String(store.image).startsWith('http') || String(store.image).startsWith('/images/')) return store.image
    return '/storage/' + String(store.image).replace(/^[\\/]+/, '')
  }
  return DEFAULT_STORE_IMAGE
}

function isStoreOpen(store) {
  return store.is_open === 1 || store.is_open === true
}

function formatRating(val) {
  const n = Number(val)
  if (Number.isNaN(n)) return '—'
  return n.toFixed(1)
}
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

@keyframes slide-up {
  from { transform: translateY(100%); }
  to { transform: translateY(0); }
}

.animate-slide-up {
  animation: slide-up 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
</style>
