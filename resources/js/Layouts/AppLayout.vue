<template>
  <div class="app-root min-h-screen bg-slate-50 dark:bg-slate-900 transition-colors duration-300 pb-20 md:pb-0">
    <!-- Desktop Top Navbar (Hidden on Mobile) -->
    <nav class="hidden md:block sticky top-0 z-50 transition-all duration-300 bg-white/80 dark:bg-slate-800/80 backdrop-blur-md border-b border-slate-100 dark:border-slate-700/50">
      <div class="container mx-auto px-6">
        <div class="flex items-center justify-between h-20">
          <Link :href="route('stores.index')" class="flex items-center gap-3 group">
            <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:rotate-12 transition-transform duration-300">
              <span class="text-2xl">🍽️</span>
            </div>
            <div>
              <div class="text-xl font-black text-slate-900 dark:text-white tracking-tighter leading-none">NEMSU Chow Zone</div>
              <div class="text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-[0.2em] mt-1">Premium Delivery</div>
            </div>
          </Link>

          <div class="flex items-center gap-8">
            <Link :href="route('stores.index')" class="text-xs font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 hover:text-blue-600 transition-colors" :class="{ 'text-blue-600 dark:text-blue-400': $page.url === '/stores' }">Stores</Link>
            <Link :href="route('orders.index')" class="text-xs font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 hover:text-blue-600 transition-colors" :class="{ 'text-blue-600 dark:text-blue-400': $page.url === '/orders' }">Orders</Link>

            <button @click="toggleDark" class="w-10 h-10 rounded-xl flex items-center justify-center text-xl transition-all hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-yellow-400">
              {{ isDark ? '☀️' : '🌙' }}
            </button>

            <div v-if="user" class="relative">
              <button @click="dropdownOpen = !dropdownOpen" class="flex items-center gap-3 p-1 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-all active:scale-95 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold shadow-md">
                  {{ userInitial }}
                </div>
                <span class="text-slate-400 text-xs transition-transform duration-300" :class="{ 'rotate-180': dropdownOpen }">▼</span>
              </button>

              <transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 translate-y-4 scale-95" enter-to-class="opacity-100 translate-y-0 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="opacity-100 translate-y-0 scale-100" leave-to-class="opacity-0 translate-y-4 scale-95">
                <div v-if="dropdownOpen" class="absolute right-0 mt-3 w-64 bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-700 overflow-hidden py-2 z-50">
                  <div class="px-5 py-4 border-b border-slate-50 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                    <div class="text-sm font-black text-slate-900 dark:text-white truncate">{{ userName }}</div>
                    <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">{{ userRole || 'Member' }}</div>
                  </div>
                  <div class="p-2 space-y-1">
                    <Link v-if="isAdmin" :href="route('admin.dashboard')" class="flex items-center gap-3 px-3 py-2.5 text-xs font-black uppercase tracking-widest text-slate-600 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-blue-500/10 hover:text-blue-600 rounded-xl transition-colors">
                      <span>📊</span> Admin Dashboard
                    </Link>
                    <Link :href="route('logout')" method="post" as="button" class="flex items-center w-full gap-3 px-3 py-2.5 text-xs font-black uppercase tracking-widest text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl transition-colors">
                      <span>🚪</span> Log out
                    </Link>
                  </div>
                </div>
              </transition>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Mobile Top Header (Sticky) -->
    <header class="md:hidden sticky top-0 z-50 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-b border-slate-100 dark:border-slate-800 px-6 h-16 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <span class="text-xl">🍽️</span>
        <span class="text-lg font-black tracking-tighter text-slate-900 dark:text-white">Chow Zone</span>
      </div>
      <button @click="toggleDark" class="w-10 h-10 rounded-xl flex items-center justify-center text-lg bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-yellow-400">
        {{ isDark ? '☀️' : '🌙' }}
      </button>
    </header>

    <!-- Mobile Bottom Navigation (Native App Feel) -->
    <nav class="md:hidden bottom-nav-blur h-20 px-4 flex items-center justify-around">
      <Link :href="route('stores.index')" class="flex flex-col items-center gap-1 min-w-[64px] transition-all active:scale-90" :class="route().current('stores.*') ? 'text-blue-600' : 'text-slate-400'">
        <span class="text-2xl">{{ route().current('stores.*') ? '🏠' : '🏚️' }}</span>
        <span class="text-[10px] font-black uppercase tracking-widest">Home</span>
      </Link>
      <Link :href="route('orders.index')" class="flex flex-col items-center gap-1 min-w-[64px] transition-all active:scale-90" :class="route().current('orders.*') ? 'text-blue-600' : 'text-slate-400'">
        <span class="text-2xl">📦</span>
        <span class="text-[10px] font-black uppercase tracking-widest">Orders</span>
      </Link>
      <Link :href="route('stores.index')" class="flex flex-col items-center gap-1 min-w-[64px] transition-all active:scale-90 text-slate-400">
        <span class="text-2xl">🛒</span>
        <span class="text-[10px] font-black uppercase tracking-widest">Cart</span>
      </Link>
      <button @click="dropdownOpen = !dropdownOpen" class="flex flex-col items-center gap-1 min-w-[64px] transition-all active:scale-90 text-slate-400">
        <span class="text-2xl">👤</span>
        <span class="text-[10px] font-black uppercase tracking-widest">Profile</span>
      </button>
    </nav>

    <!-- Overlay to close dropdown / Mobile Profile -->
    <div v-if="dropdownOpen" class="fixed inset-0 z-[110] bg-slate-900/60 backdrop-blur-sm md:bg-transparent md:backdrop-blur-0" @click="dropdownOpen = false">
      <!-- Mobile Profile Sheet -->
      <div class="md:hidden absolute bottom-0 left-0 right-0 bg-white dark:bg-slate-900 rounded-t-[3rem] p-8 animate-slide-up" @click.stop>
        <div class="w-12 h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-8"></div>
        <div class="flex items-center gap-4 mb-8">
          <div class="w-16 h-16 rounded-[1.5rem] bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white text-2xl font-bold shadow-xl">
            {{ userInitial }}
          </div>
          <div>
            <div class="text-xl font-black text-slate-900 dark:text-white tracking-tight">{{ userName }}</div>
            <div class="text-sm font-bold text-slate-400 uppercase tracking-widest">{{ userRole || 'Member' }}</div>
          </div>
        </div>
        <div class="space-y-4">
          <Link v-if="isAdmin" :href="route('admin.dashboard')" class="flex items-center gap-4 p-5 bg-blue-50 dark:bg-blue-500/10 text-blue-600 rounded-3xl font-black uppercase text-xs tracking-[0.2em]">
            <span>📊</span> Admin Dashboard
          </Link>
          <Link :href="route('logout')" method="post" as="button" class="w-full flex items-center gap-4 p-5 bg-rose-50 dark:bg-rose-500/10 text-rose-600 rounded-3xl font-black uppercase text-xs tracking-[0.2em]">
            <span>🚪</span> Logout Account
          </Link>
        </div>
        <button @click="dropdownOpen = false" class="w-full mt-6 py-4 text-slate-400 font-black uppercase text-[10px] tracking-widest">Close Menu</button>
      </div>
    </div>

    <!-- Flash Messages (Mobile Optimized) -->
    <div class="fixed top-20 left-1/2 -translate-x-1/2 z-[120] w-full max-w-md px-6 space-y-3 pointer-events-none">
      <transition-group enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 -translate-y-8 scale-90" enter-to-class="opacity-100 translate-y-0 scale-100" leave-active-class="transition ease-in duration-200" leave-from-class="opacity-100 translate-y-0 scale-100" leave-to-class="opacity-0 -translate-y-8 scale-90">
        <div v-if="$page.props.flash?.success" key="success" class="pointer-events-auto flex items-center gap-3 px-6 py-4 bg-emerald-500 text-white rounded-[2rem] shadow-2xl shadow-emerald-500/30">
          <span class="text-xl">✅</span>
          <p class="text-sm font-black tracking-tight">{{ $page.props.flash.success }}</p>
        </div>
        <div v-if="$page.props.flash?.error" key="error" class="pointer-events-auto flex items-center gap-3 px-6 py-4 bg-rose-500 text-white rounded-[2rem] shadow-2xl shadow-rose-500/30">
          <span class="text-xl">❌</span>
          <p class="text-sm font-black tracking-tight">{{ $page.props.flash.error }}</p>
        </div>
      </transition-group>
    </div>

    <!-- Main Content -->
    <main class="container mx-auto px-4 sm:px-6 py-6 md:py-10">
      <slot />
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'

defineProps({ title: String })

const dropdownOpen = ref(false)
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

const user = computed(() => page.props.auth?.user || null)

onMounted(() => {
  // Initialize theme
  const savedTheme = localStorage.getItem('theme')
  if (savedTheme) {
    isDark.value = savedTheme === 'dark'
  } else {
    isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches
  }
  updateDarkMode()

  if (user.value && window.Echo) {
    window.Echo.private(`orders.${user.value.id}`)
      .listen('.order.updated', (e) => {
        // Refresh the page data if we are on the orders page
        if (window.location.pathname.includes('/orders')) {
          router.reload({ only: ['orders'] })
        }

        // Show a custom toast/notification if needed
        console.log('Order updated:', e.order)
      })
  }
})

onUnmounted(() => {
  if (user.value && window.Echo) {
    window.Echo.leave(`orders.${user.value.id}`)
  }
})

const userInitial = computed(() => {
  const name = user.value?.name || '?'
  return name.charAt(0).toUpperCase()
})

const userFirstName = computed(() => {
  if (!user.value?.name) return 'Guest'
  return user.value.name.split(' ')[0]
})

const userName = computed(() => user.value?.name || 'Guest User')
const userEmail = computed(() => user.value?.email || '')
const userRole = computed(() => user.value?.role ? user.value.role.toUpperCase() : '')
const isAdmin = computed(() => user.value?.role === 'admin')
</script>

<style scoped>
@keyframes slide-up {
  from { transform: translateY(100%); }
  to { transform: translateY(0); }
}

@keyframes bounce-in {
  0% { transform: scale(0.9); opacity: 0; }
  50% { transform: scale(1.05); }
  100% { transform: scale(1); opacity: 1; }
}

.animate-slide-up {
  animation: slide-up 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.animate-bounce-in {
  animation: bounce-in 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}

/* Custom Hide Scrollbar */
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
