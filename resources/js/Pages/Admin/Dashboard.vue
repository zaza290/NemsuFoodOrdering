<template>
  <AdminLayout title="System Dashboard">
    <div class="space-y-12 pb-20">
      <!-- Welcome Section -->
      <section class="relative overflow-hidden rounded-[3rem] bg-gradient-to-br from-slate-900 via-slate-800 to-blue-900 p-8 sm:p-12 shadow-2xl group">
        <div class="absolute inset-0 opacity-20 mix-blend-overlay pointer-events-none">
          <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=1200&q=80" alt="" class="w-full h-full object-cover" />
        </div>
        <div class="relative z-10">
          <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
            <div class="space-y-6">
              <span class="inline-flex px-4 py-1.5 text-[10px] font-black uppercase tracking-[0.2em] text-blue-100 bg-blue-500/30 backdrop-blur-md border border-blue-400/30 rounded-full">
                System Overview
              </span>
              <h2 class="text-4xl sm:text-6xl font-black text-white tracking-tighter leading-tight">
                Good Day, <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-200">Admin.</span>
              </h2>
              <p class="text-lg font-medium text-slate-300 max-w-xl">
                NEMSU Chow Zone is performing well. You have <span class="text-white font-bold">{{ stats.pending_orders }} pending orders</span> that need your attention.
              </p>
            </div>
            <div class="flex flex-wrap gap-4">
              <div class="p-6 glass-card border-white/10 rounded-[2.5rem] text-center min-w-[140px] flex-1 lg:flex-none">
                <div class="text-4xl font-black text-white mb-1">{{ stats.delivered_today }}</div>
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Delivered Today</div>
              </div>
              <div class="p-6 glass-card border-white/10 rounded-[2.5rem] text-center min-w-[140px] flex-1 lg:flex-none">
                <div class="text-4xl font-black text-emerald-400 mb-1">+{{ stats.income_growth_percent }}%</div>
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Growth MoM</div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Stats Grid -->
      <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <div v-for="stat in statCards" :key="stat.label"
          class="group p-8 bg-white dark:bg-slate-800 rounded-[2.5rem] shadow-premium hover:shadow-2xl hover:-translate-y-2 border border-slate-100 dark:border-slate-700/50 transition-all duration-500">
          <div class="flex items-start justify-between mb-8">
            <div :class="['w-16 h-16 rounded-2xl flex items-center justify-center text-3xl shadow-lg transition-transform duration-500 group-hover:rotate-12', stat.colorClass]">
              {{ stat.icon }}
            </div>
            <span :class="['px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-slate-50 dark:bg-slate-900', stat.textClass]">
              {{ stat.trend }}
            </span>
          </div>
          <div class="space-y-2">
            <div class="text-3xl font-black text-slate-900 dark:text-white tracking-tighter">{{ stat.value }}</div>
            <div class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.1em]">{{ stat.label }}</div>
          </div>
        </div>
      </section>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        <!-- Revenue Chart -->
        <div class="lg:col-span-8 space-y-8">
          <div class="flex items-center justify-between px-6">
            <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Revenue Analytics</h3>
            <div class="flex p-1 bg-slate-100 dark:bg-slate-800 rounded-2xl">
              <button class="px-6 py-2 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-sm">6 Months</button>
              <button class="px-6 py-2 text-slate-400 dark:text-slate-500 text-[10px] font-black uppercase tracking-widest rounded-xl hover:text-slate-600 dark:hover:text-slate-300 transition-all">Yearly</button>
            </div>
          </div>

          <div class="p-10 bg-white dark:bg-slate-800 rounded-[3rem] shadow-premium border border-slate-100 dark:border-slate-700/50 min-h-[450px] flex flex-col group/chart">
            <div class="flex-1 flex items-end justify-between gap-6 pt-12 border-b border-slate-100 dark:border-slate-700 pb-6">
              <div v-for="pt in revenue_trend" :key="pt.label" class="flex-1 flex flex-col items-center gap-6 group">
                <div class="relative w-full flex flex-col items-center">
                  <!-- Bar with tooltip -->
                  <div
                    class="w-full max-w-[45px] rounded-t-2xl bg-gradient-to-t from-blue-600 to-indigo-500 dark:from-blue-500 dark:to-indigo-400 transition-all duration-1000 group-hover:scale-x-110 relative shadow-lg shadow-blue-500/20"
                    :style="{ height: barHeight(pt.amount) }"
                  >
                    <div class="absolute -top-12 left-1/2 -translate-x-1/2 bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 text-[11px] font-black px-3 py-1.5 rounded-xl opacity-0 group-hover:opacity-100 transition-all scale-75 group-hover:scale-100 whitespace-nowrap z-10 shadow-2xl">
                      ₱{{ formatAmount(pt.amount) }}
                    </div>
                  </div>
                </div>
                <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ pt.label }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activity / Top Stores -->
        <div class="lg:col-span-4 space-y-8">
          <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight px-6">Top Performing Stores</h3>
          <div class="space-y-4">
            <div v-for="store in top_stores" :key="store.id"
              class="p-6 bg-white dark:bg-slate-800 rounded-[2.5rem] shadow-premium border border-slate-100 dark:border-slate-700/50 flex items-center gap-6 group hover:border-blue-500/30 dark:hover:border-blue-500/50 transition-all duration-500 hover:shadow-2xl hover:-translate-x-2">
              <div class="w-20 h-20 rounded-[1.5rem] overflow-hidden shadow-premium shrink-0 border-2 border-slate-50 dark:border-slate-700">
                <img
                  :src="storeImageSrc(store)"
                  class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                  @error="(e) => e.target.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(store.name)}&background=random&size=128`"
                />
              </div>
              <div class="flex-1 min-w-0">
                <div class="text-base font-black text-slate-900 dark:text-white truncate tracking-tight mb-2">{{ store.name }}</div>
                <div class="flex items-center gap-3">
                  <div class="flex items-center gap-1.5 px-2 py-1 bg-amber-50 dark:bg-amber-500/10 rounded-lg">
                    <span class="text-amber-500 text-xs">★</span>
                    <span class="text-[11px] font-black text-amber-700 dark:text-amber-400">{{ parseFloat(store.ratings_avg_rating || 0).toFixed(1) }}</span>
                  </div>
                  <span class="text-[11px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest">{{ store.orders_count }} Orders</span>
                </div>
              </div>
            </div>
          </div>

          <Link :href="route('admin.stores.index')" class="flex items-center justify-center w-full py-6 bg-slate-50 dark:bg-slate-900 text-slate-500 dark:text-slate-400 text-[10px] font-black uppercase tracking-[0.25em] rounded-[2rem] hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600 dark:hover:text-white transition-all active:scale-95 shadow-inner border border-slate-100 dark:border-slate-700/50">
            View All Partners
          </Link>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  stats: Object,
  revenue_trend: Array,
  recent_orders: Array,
  top_stores: Array,
})

const statCards = computed(() => [
  {
    label: 'Total Revenue',
    value: '₱' + formatRevenue(props.stats.total_revenue),
    icon: '💰',
    trend: 'High Yield',
    colorClass: 'bg-emerald-50 text-emerald-600 shadow-emerald-100',
    textClass: 'text-emerald-500'
  },
  {
    label: 'Total Orders',
    value: props.stats.total_orders,
    icon: '📦',
    trend: 'Steady',
    colorClass: 'bg-blue-50 text-blue-600 shadow-blue-100',
    textClass: 'text-blue-500'
  },
  {
    label: 'Active Users',
    value: props.stats.total_users,
    icon: '👥',
    trend: 'Growing',
    colorClass: 'bg-indigo-50 text-indigo-600 shadow-indigo-100',
    textClass: 'text-indigo-500'
  },
  {
    label: 'Campus Stores',
    value: props.stats.total_stores,
    icon: '🏪',
    trend: 'Verified',
    colorClass: 'bg-amber-50 text-amber-600 shadow-amber-100',
    textClass: 'text-amber-500'
  },
])

const formatRevenue = (val) => {
  if (!val) return '0'
  return new Intl.NumberFormat('en-PH').format(val)
}

const formatAmount = (val, short = false) => {
  if (short && val >= 1000) return (val / 1000).toFixed(1) + 'k'
  return new Intl.NumberFormat('en-PH').format(val)
}

const barHeight = (amount) => {
  const max = Math.max(...props.revenue_trend.map(r => r.amount), 1000)
  const percentage = (amount / max) * 100
  return `${Math.max(percentage, 5)}%`
}

function storeImageSrc(store) {
  const DEFAULT_STORE_IMAGE = 'https://images.unsplash.com/photo-1555392886-6f5ac0d4c58b?w=400&h=240&fit=crop&q=80'
  if (!store || !store.image) return DEFAULT_STORE_IMAGE
  if (String(store.image).startsWith('http')) return store.image
  return '/storage/' + String(store.image).replace(/^[\\/]+/, '')
}
</script>
