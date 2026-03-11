<template>
  <div class="admin-root min-h-screen flex overflow-hidden transition-colors duration-300 bg-slate-50 text-slate-900 dark:bg-slate-900 dark:text-white">
    <!-- Sidebar - Modern Floating Style -->
    <aside :class="['sidebar sticky top-0 h-screen transition-all duration-500 z-50 p-6', { 'w-[100px]': collapsed, 'w-[320px]': !collapsed }]">
      <div class="h-full glass-card rounded-[2.5rem] shadow-3d border-slate-200/50 dark:border-slate-700/50 flex flex-col overflow-hidden relative bg-white/80 dark:bg-slate-800/80 backdrop-blur-md">
        <!-- Sidebar Header -->
        <div class="p-8 flex items-center gap-4 relative">
          <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/20 group-hover:rotate-12 transition-transform duration-300 shrink-0">
            <span class="text-2xl">🍽️</span>
          </div>
          <div v-if="!collapsed" class="flex-1">
            <div class="text-lg font-black tracking-tighter leading-none text-slate-900 dark:text-white">Chow Zone</div>
            <div class="text-[10px] font-black text-blue-600 uppercase tracking-widest mt-1">Admin Portal</div>
          </div>
          <button @click="collapsed = !collapsed" class="absolute -right-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full shadow-md flex items-center justify-center transition-all active:scale-90 z-10 bg-white border-slate-100 text-slate-400 dark:bg-slate-700 dark:border-slate-600 dark:text-slate-300">
            {{ collapsed ? '›' : '‹' }}
          </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 space-y-2 overflow-y-auto custom-scrollbar pt-4">
          <Link v-for="item in navItems" :key="item.route" :href="route(item.route)"
            :class="['group flex items-center gap-4 px-4 py-4 rounded-2xl transition-all duration-300 active:scale-95',
              $page.url === item.url || $page.url.startsWith(item.url + '/')
                ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20'
                : 'text-slate-500 hover:bg-slate-50 hover:text-blue-600 dark:text-slate-400 dark:hover:bg-slate-700/50 dark:hover:text-blue-400']">
            <span class="text-xl shrink-0">{{ item.icon }}</span>
            <span v-if="!collapsed" class="font-black text-sm uppercase tracking-widest truncate">{{ item.label }}</span>
            <span v-if="!collapsed && item.badge" class="ml-auto bg-rose-500 text-white text-[10px] font-black px-2 py-1 rounded-full shadow-sm">{{ item.badge }}</span>
          </Link>

          <div class="pt-8 pb-4">
            <div v-if="!collapsed" class="px-4 mb-4 text-[10px] font-black text-slate-400 uppercase tracking-widest dark:text-slate-500">External Links</div>
            <Link :href="route('stores.index')" class="group flex items-center gap-4 px-4 py-4 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-blue-600 dark:text-slate-400 dark:hover:bg-slate-700/50 dark:hover:text-blue-400 transition-all duration-300 active:scale-95">
              <span class="text-xl shrink-0">🌐</span>
              <span v-if="!collapsed" class="font-black text-sm uppercase tracking-widest">Live Site</span>
            </Link>
          </div>
        </nav>

        <!-- Sidebar Footer -->
        <div class="p-6 border-t bg-slate-50/50 dark:bg-slate-900/50 border-slate-100 dark:border-slate-700">
          <div v-if="!collapsed" class="flex items-center gap-4 mb-6 px-2">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold shadow-md">
              {{ adminInitial }}
            </div>
            <div class="flex-1 min-w-0">
              <div class="text-sm font-black truncate tracking-tight text-slate-900 dark:text-white">{{ adminName }}</div>
              <div class="text-[10px] font-black text-slate-400 uppercase tracking-tighter dark:text-slate-500">System Administrator</div>
            </div>
          </div>
          <Link :href="route('logout')" method="post" as="button" class="w-full py-4 text-xs font-black uppercase tracking-widest rounded-2xl transition-all active:scale-95 shadow-sm bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white dark:bg-rose-500/10 dark:text-rose-400 dark:hover:bg-rose-500 dark:hover:text-white">
            {{ collapsed ? '🚪' : '🚪 Logout Portal' }}
          </Link>
        </div>
      </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden relative">
      <header class="h-24 px-8 flex items-center justify-between z-40 bg-slate-50/80 dark:bg-slate-900/80 backdrop-blur-md sticky top-0 border-b border-transparent dark:border-slate-800">
        <div class="flex items-center gap-4">
          <div class="w-1.5 h-8 bg-blue-600 rounded-full"></div>
          <h1 class="text-3xl font-black tracking-tighter text-slate-900 dark:text-white">{{ title }}</h1>
        </div>
        <div class="flex items-center gap-6">
          <button @click="toggleDark" class="w-10 h-10 rounded-xl flex items-center justify-center text-xl transition-all bg-white border border-slate-100 shadow-sm text-slate-600 hover:bg-blue-50 dark:bg-slate-800 dark:border-slate-700 dark:text-yellow-400 dark:hover:bg-slate-700">
            {{ isDark ? '☀️' : '🌙' }}
          </button>
          <div class="hidden sm:flex flex-col text-right">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-0.5 dark:text-slate-500">Today is</div>
            <div class="text-sm font-black tracking-tight text-slate-900 dark:text-white">📅 {{ currentDate }}</div>
          </div>
          <div class="w-10 h-10 rounded-xl shadow-sm flex items-center justify-center text-xl relative cursor-pointer hover:shadow-md transition-shadow bg-white border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
            🔔
            <span class="absolute -top-1 -right-1 w-4 h-4 bg-rose-500 border-2 border-white dark:border-slate-800 rounded-full"></span>
          </div>
        </div>
      </header>

      <!-- Flash Messages -->
      <transition-group
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 -translate-y-8 scale-90"
        enter-to-class="opacity-100 translate-y-0 scale-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-y-0 scale-100"
        leave-to-class="opacity-0 -translate-y-8 scale-90"
        class="fixed top-28 left-1/2 -translate-x-1/2 z-[60] w-full max-w-md px-4 space-y-3 pointer-events-none"
      >
        <div v-if="$page.props.flash?.success" key="success" class="pointer-events-auto flex items-center justify-between gap-3 px-6 py-4 glass-card border-emerald-100 text-emerald-800 rounded-3xl premium-shadow">
          <div class="flex items-center gap-3">
            <span class="text-xl">✅</span>
            <p class="text-sm font-black uppercase tracking-tight">{{ $page.props.flash.success }}</p>
          </div>
          <a v-if="$page.props.flash.order_id" :href="route('orders.receipt', $page.props.flash.order_id)" target="_blank" class="px-3 py-1.5 bg-blue-100 text-blue-700 text-[10px] font-black uppercase tracking-widest rounded-lg hover:bg-blue-200 transition-all">
            Print Receipt
          </a>
        </div>
      </transition-group>

      <!-- Content Area -->
      <div class="flex-1 overflow-y-auto custom-scrollbar p-8">
        <slot />
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'

const props = defineProps({
  title: { type: String, default: 'Dashboard' },
  pendingCount: { type: Number, default: 0 },
})

const collapsed = ref(false)
const isDark = ref(false)
const page = usePage()

const toggleDark = () => {
  isDark.value = !isDark.value
  localStorage.setItem('theme', isDark.value ? 'dark' : 'light')
  updateDarkMode()
}

const updateDarkMode = () => {
  if (isDark.value) {
    document.documentElement.classList.add('dark')
  } else {
    document.documentElement.classList.remove('dark')
  }
}

onMounted(() => {
  // Initialize theme from localStorage or system preference
  const savedTheme = localStorage.getItem('theme')
  if (savedTheme) {
    isDark.value = savedTheme === 'dark'
  } else {
    isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches
  }
  updateDarkMode()

  // Safety check for Laravel Echo
  if (window.Echo) {
    window.Echo.private('admin.orders')
      .listen('.order.updated', (e) => {
        // Refresh the page data for admin
        router.reload({ only: ['orders', 'recent_walk_in', 'stats'] })
        console.log('Admin: Order status updated', e.order)
      })
      .listen('.order.placed', (e) => {
        // Refresh data and maybe show a toast notification
        router.reload({ only: ['orders', 'recent_walk_in', 'stats'] })
        console.log('Admin: New order placed!', e.order)
        // Here you could trigger a toast notification library
      })
  }
})

onUnmounted(() => {
  if (window.Echo) {
    window.Echo.leave('admin.orders')
  }
})

const navItems = computed(() => [
  { label: 'Dashboard', route: 'admin.dashboard', icon: '📊', url: '/admin/dashboard' },
  { label: 'Orders', route: 'admin.orders.index', icon: '📦', url: '/admin/orders', badge: props.pendingCount },
  { label: 'POS System', route: 'admin.pos.index', icon: '🖥️', url: '/admin/pos' },
  { label: 'Inventory', route: 'admin.inventory.index', icon: '📋', url: '/admin/inventory' },
  { label: 'Stores', route: 'admin.stores.index', icon: '🏪', url: '/admin/stores' },
  { label: 'User Management', route: 'admin.users.index', icon: '👥', url: '/admin/users' },
])

const adminName = computed(() => page.props.auth?.user?.name || 'Admin')
const adminInitial = computed(() => adminName.value.charAt(0).toUpperCase())

const currentDate = computed(() => {
  return new Date().toLocaleDateString('en-US', {
    weekday: 'short',
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
})
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Syne:wght@700;800&display=swap');

.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  @apply bg-slate-200 dark:bg-slate-700 rounded-full;
}

.glass-card {
  @apply bg-white/80 dark:bg-slate-800/80 backdrop-blur-md border border-white/20 dark:border-slate-700/50;
}

.shadow-premium {
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.05);
}

.dark .shadow-premium {
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
}

.shadow-3d {
  box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.1), 0 1px 4px rgba(0, 0, 0, 0.05);
}

.dark .shadow-3d {
  box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5), 0 1px 4px rgba(0, 0, 0, 0.2);
}
</style>
