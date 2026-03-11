<template>
  <AppLayout title="All Stores">
    <div class="stores-page space-y-12">
      <!-- Hero Section - Enhanced with Glassmorphism -->
      <header class="relative overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-blue-700 via-blue-600 to-indigo-800 py-16 px-8 sm:px-12 shadow-3d">
        <div class="absolute inset-0 opacity-20 mix-blend-overlay">
          <img src="https://images.unsplash.com/photo-1555392886-6f5ac0d4c58b?w=1200&q=80" alt="" class="w-full h-full object-cover" />
        </div>
        <div class="relative z-10 max-w-2xl">
          <span class="inline-block px-4 py-1.5 mb-6 text-xs font-black uppercase tracking-widest text-blue-100 bg-blue-500/30 backdrop-blur-md border border-blue-400/30 rounded-full">
            📍 NEMSU Campus
          </span>
          <h1 class="text-4xl sm:text-6xl font-black text-white leading-tight mb-4 tracking-tighter">
            Discover the Best <br/>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-200 to-indigo-100">Campus Flavors.</span>
          </h1>
          <p class="text-lg font-medium text-blue-100/90 leading-relaxed mb-8">
            Order from {{ (stores || []).length }} premium campus stores with real-time stock tracking and secure payments.
          </p>
          <div class="flex flex-wrap gap-4">
            <div class="flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-md rounded-2xl border border-white/10">
              <span class="text-xl">🚀</span>
              <span class="text-sm font-bold text-white">Fast Delivery</span>
            </div>
            <div class="flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-md rounded-2xl border border-white/10">
              <span class="text-xl">💎</span>
              <span class="text-sm font-bold text-white">Premium Quality</span>
            </div>
          </div>
        </div>
      </header>

      <!-- Search Section - Sticky with Glass Effect -->
      <section class="sticky top-24 z-30 py-4">
        <div class="max-w-2xl mx-auto glass-card premium-shadow rounded-[2rem] p-2 flex items-center gap-2">
          <div class="flex-1 flex items-center px-4 gap-3">
            <svg class="w-5 h-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input
              v-model="search"
              type="text"
              placeholder="What are you craving today?"
              class="w-full bg-transparent border-none focus:ring-0 text-slate-800 font-bold placeholder:text-slate-400 py-3"
            />
          </div>
          <button v-if="search" @click="search = ''" class="p-2 hover:bg-slate-100 rounded-full transition-colors mr-2">
            <span class="text-xl">×</span>
          </button>
        </div>
      </section>

      <!-- Store Grid - Modern 3D Cards -->
      <section class="container mx-auto">
        <div v-if="filteredStores.length === 0" class="flex flex-col items-center justify-center py-20 glass-card rounded-[3rem] border-dashed border-slate-200">
          <span class="text-6xl mb-4">🔍</span>
          <p class="text-xl font-black text-slate-800">No stores found</p>
          <p class="text-slate-500 font-bold">Try searching for something else!</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <Link
            v-for="store in filteredStores"
            :key="store.id"
            :href="route('stores.show', store.slug)"
            class="group relative flex flex-col bg-white rounded-[2.5rem] overflow-hidden shadow-3d ring-1 ring-slate-200/50 hover:ring-blue-500/20 transition-all duration-500"
          >
            <!-- Card Image Container -->
            <div class="img-container h-56 relative">
              <img
                :src="storeImageSrc(store)"
                :alt="store.name"
                class="img-cover"
                @error="(e) => e.target.src = imageFallback"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 to-transparent opacity-60 group-hover:opacity-40 transition-opacity"></div>

              <!-- Floating Badge -->
              <div class="absolute top-4 left-4">
                <span :class="[
                  'px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest backdrop-blur-md border shadow-lg',
                  store.is_open
                    ? 'bg-emerald-500/80 text-white border-emerald-400/30'
                    : 'bg-rose-500/80 text-white border-rose-400/30'
                ]">
                  {{ store.is_open ? 'Open Now' : 'Closed' }}
                </span>
              </div>
            </div>

            <!-- Card Body -->
            <div class="p-8 flex-1 flex flex-col">
              <div class="flex justify-between items-start mb-4">
                <h2 class="text-2xl font-black text-slate-900 group-hover:text-blue-600 transition-colors tracking-tight leading-tight">
                  {{ store.name }}
                </h2>
                <div class="flex items-center gap-1.5 px-3 py-1 bg-amber-50 rounded-xl border border-amber-100 shadow-sm">
                  <span class="text-amber-500 text-sm">★</span>
                  <span class="text-sm font-black text-amber-700">{{ formatRating(store.ratings_avg_rating) }}</span>
                </div>
              </div>

              <!-- Tags / Featured Items -->
              <div class="flex flex-wrap gap-2 mb-8">
                <template v-for="item in (store.menu_items || []).slice(0, 3)" :key="item.id">
                  <span class="px-3 py-1 bg-slate-50 text-slate-600 text-[10px] font-black uppercase tracking-widest rounded-lg border border-slate-100">
                    {{ item.name }}
                  </span>
                </template>
                <span v-if="(store.menu_items || []).length > 3" class="px-3 py-1 bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest rounded-lg border border-blue-100">
                  +{{ (store.menu_items || []).length - 3 }} More
                </span>
              </div>

              <!-- Action Footer -->
              <div class="mt-auto flex items-center justify-between pt-6 border-t border-slate-50">
                <div class="flex -space-x-2">
                  <div v-for="i in 3" :key="i" class="w-8 h-8 rounded-full border-2 border-white bg-slate-200 overflow-hidden shadow-sm">
                    <img :src="`https://i.pravatar.cc/100?u=${store.id}${i}`" alt="" class="w-full h-full object-cover opacity-80" />
                  </div>
                </div>
                <div class="flex items-center gap-2 font-black text-blue-600 group-hover:gap-4 transition-all duration-300">
                  <span class="text-sm uppercase tracking-widest">Explore Menu</span>
                  <span class="text-xl">→</span>
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

/** Default store image — same as Admin when store has no image; fallback when image fails to load */
const DEFAULT_STORE_IMAGE = 'https://images.unsplash.com/photo-1555392886-6f5ac0d4c58b?w=400&h=240&fit=crop&q=80'
// Inline SVG placeholder so something always shows (works offline / when CDN fails)
const imageFallback = 'data:image/svg+xml,' + encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" width="400" height="240" viewBox="0 0 400 240"><rect fill="#e2e8f0" width="400" height="240"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#94a3b8" font-family="sans-serif" font-size="14">Store</text></svg>')

function storeImageSrc(store) {
  if (!store) return DEFAULT_STORE_IMAGE
  if (store.image) {
    if (String(store.image).startsWith('http')) return store.image
    return '/storage/' + String(store.image).replace(/^[\\/]+/, '')
  }
  return DEFAULT_STORE_IMAGE
}

function formatRating(val) {
  const n = Number(val)
  if (Number.isNaN(n)) return '—'
  return n.toFixed(1)
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

* { box-sizing: border-box; }

.stores-page { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; min-height: 100vh; }

/* Hero - single gradient, professional */
.hero {
  background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
  padding: 2rem 1.5rem 2.5rem;
  color: #fff;
}
.hero-inner { max-width: 1100px; margin: 0 auto; text-align: center; }
.hero-title { font-size: 1.75rem; font-weight: 800; margin-bottom: 0.35rem; letter-spacing: -0.02em; }
.hero-sub { font-size: 0.95rem; opacity: 0.95; margin-bottom: 0.75rem; }
.hero-meta { font-size: 0.8rem; opacity: 0.85; }
.meta-dot { margin: 0 0.5rem; }
.meta-item { font-variant-numeric: tabular-nums; }

/* Search */
.search-section { background: #fff; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; }
.search-wrap {
  max-width: 560px;
  margin: 0 auto;
  position: relative;
  display: flex;
  align-items: center;
}
.search-icon { position: absolute; left: 1rem; color: #94a3b8; flex-shrink: 0; }
.search-input {
  width: 100%;
  padding: 0.75rem 2.5rem 0.75rem 2.75rem;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  font-size: 0.95rem;
  font-family: inherit;
  background: #f8fafc;
  transition: border-color 0.2s, background 0.2s;
}
.search-input:focus { outline: none; border-color: #1e3a5f; background: #fff; }
.search-input::placeholder { color: #94a3b8; }
.search-clear {
  position: absolute;
  right: 0.75rem;
  width: 28px; height: 28px;
  border: none;
  background: #e2e8f0;
  color: #64748b;
  font-size: 1.25rem;
  line-height: 1;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}
.search-clear:hover { background: #cbd5e1; color: #475569; }

/* Store cards - unified white cards, real images */
.stores-section { padding: 1.5rem 1rem 2.5rem; }
.container { max-width: 1100px; margin: 0 auto; }
.empty-msg { text-align: center; color: #64748b; padding: 3rem 1rem; font-size: 0.95rem; }

.stores-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.25rem;
}

.store-card {
  background: #fff;
  border-radius: 16px;
  overflow: hidden;
  text-decoration: none;
  color: inherit;
  display: block;
  border: 1px solid #e2e8f0;
  transition: box-shadow 0.2s, transform 0.2s;
}
.store-card:hover {
  box-shadow: 0 12px 28px rgba(30, 58, 95, 0.12);
  transform: translateY(-2px);
}

.card-image-wrap {
  position: relative;
  aspect-ratio: 16 / 10;
  overflow: hidden;
  background: #e2e8f0;
}
.card-image { width: 100%; height: 100%; object-fit: cover; display: block; }
.card-status {
  position: absolute;
  top: 0.75rem;
  right: 0.75rem;
  font-size: 0.7rem;
  font-weight: 700;
  padding: 0.3rem 0.6rem;
  border-radius: 8px;
  text-transform: uppercase;
  letter-spacing: 0.03em;
}
.card-status.open { background: #059669; color: #fff; }
.card-status.closed { background: #64748b; color: #fff; }

.card-content { padding: 1rem 1.15rem 1.15rem; }
.card-title {
  font-size: 1rem;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 0.5rem;
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.card-rating { display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.6rem; }
.stars { display: flex; gap: 0.1rem; }
.star { font-size: 0.85rem; color: #e2e8f0; }
.star.filled { color: #f59e0b; }
.rating-num { font-size: 0.9rem; font-weight: 700; color: #334155; font-variant-numeric: tabular-nums; }

.card-tags { display: flex; flex-wrap: wrap; gap: 0.35rem; margin-bottom: 0.75rem; }
.tag {
  background: #f1f5f9;
  color: #475569;
  font-size: 0.7rem;
  padding: 0.25rem 0.5rem;
  border-radius: 6px;
  max-width: 120px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.tag.more { background: #e0f2fe; color: #0369a1; font-weight: 600; }

.card-cta {
  font-size: 0.85rem;
  font-weight: 700;
  color: #1e3a5f;
}
.store-card:hover .card-cta { text-decoration: underline; }

@media (max-width: 640px) {
  .hero-title { font-size: 1.5rem; }
  .stores-grid { grid-template-columns: 1fr; gap: 1rem; }
  .stores-section { padding: 1rem 0.5rem 2rem; }
}
</style>
