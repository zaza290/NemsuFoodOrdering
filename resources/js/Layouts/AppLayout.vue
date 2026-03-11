<template>
  <div class="app-root min-h-screen bg-slate-50 dark:bg-slate-900 transition-colors duration-300">
    <!-- Top Navbar -->
    <nav class="sticky top-0 z-50 transition-all duration-300 glass-card dark:bg-slate-800/80 dark:border-slate-700/50 backdrop-blur-md">
      <div class="container mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between h-20">
          <Link :href="route('stores.index')" class="flex items-center gap-3 group">
            <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:rotate-12 transition-transform duration-300">
              <span class="text-2xl">🍽️</span>
            </div>
            <div class="hidden sm:block">
              <div class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-none">NEMSU Chow Zone</div>
              <div class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-widest mt-1">Premium Delivery</div>
            </div>
          </Link>

          <div class="hidden md:flex items-center gap-8">
            <Link :href="route('stores.index')" class="text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" :class="{ 'text-blue-600 dark:text-blue-400': $page.url.startsWith('/stores') }">
              🏪 Stores
            </Link>
            <Link :href="route('orders.index')" class="text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" :class="{ 'text-blue-600 dark:text-blue-400': $page.url.startsWith('/orders') }">
              📦 My Orders
            </Link>
          </div>

          <div class="flex items-center gap-4">
            <button @click="toggleDark" class="w-10 h-10 rounded-xl flex items-center justify-center text-xl transition-all hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-yellow-400">
              {{ isDark ? '☀️' : '🌙' }}
            </button>

            <div v-if="user" class="flex items-center gap-4">
              <!-- Quick Logout Button -->
              <Link
                :href="route('logout')"
                method="post"
                as="button"
                class="hidden sm:flex items-center gap-2 px-4 py-2 text-xs font-black uppercase tracking-widest text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-2xl transition-all active:scale-95"
                title="Logout"
              >
                <span>🚪</span> Logout
              </Link>

              <div class="relative">
                <button @click="dropdownOpen = !dropdownOpen" class="flex items-center gap-3 p-1.5 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-all active:scale-95 group">
                  <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold shadow-md group-hover:shadow-blue-200 transition-shadow">
                    {{ userInitial }}
                  </div>
                  <div class="hidden sm:block text-left mr-1">
                    <div class="text-sm font-bold text-slate-900 dark:text-white">{{ userFirstName }}</div>
                    <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-tighter">{{ userRole || 'Member' }}</div>
                  </div>
                  <span class="text-slate-400 text-xs transition-transform duration-300" :class="{ 'rotate-180': dropdownOpen }">▼</span>
                </button>

                <transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 translate-y-4 scale-95" enter-to-class="opacity-100 translate-y-0 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="opacity-100 translate-y-0 scale-100" leave-to-class="opacity-0 translate-y-4 scale-95">
                  <div v-if="dropdownOpen" class="absolute right-0 mt-3 w-64 glass-card dark:bg-slate-800 dark:border-slate-700 premium-shadow rounded-3xl overflow-hidden py-2 border-slate-200/50">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                      <div class="text-sm font-extrabold text-slate-900 dark:text-white">{{ userName }}</div>
                      <div class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-0.5 truncate">{{ userEmail }}</div>
                    </div>
                    <div class="p-2 space-y-1">
                      <Link v-if="isAdmin" :href="route('admin.dashboard')" class="flex items-center gap-3 px-3 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-blue-500/10 hover:text-blue-600 dark:hover:text-blue-400 rounded-xl transition-colors">
                        <span class="text-lg">📊</span> Admin Dashboard
                      </Link>
                      <Link :href="route('logout')" method="post" as="button" class="flex items-center w-full gap-3 px-3 py-2.5 text-sm font-bold text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-xl transition-colors">
                        <span class="text-lg">🚪</span> Log out
                      </Link>
                    </div>
                  </div>
                </transition>
              </div>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Overlay to close dropdown -->
    <div v-if="dropdownOpen" class="fixed inset-0 z-40 bg-slate-900/5 backdrop-blur-[2px]" @click="dropdownOpen = false"></div>

    <!-- Flash Messages -->
    <div class="fixed top-24 left-1/2 -translate-x-1/2 z-[60] w-full max-w-md px-4 space-y-3 pointer-events-none">
      <transition-group enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 -translate-y-8 scale-90" enter-to-class="opacity-100 translate-y-0 scale-100" leave-active-class="transition ease-in duration-200" leave-from-class="opacity-100 translate-y-0 scale-100" leave-to-class="opacity-0 -translate-y-8 scale-90">
        <div v-if="$page.props.flash?.success" key="success" class="pointer-events-auto flex items-center gap-3 px-5 py-4 glass-card border-emerald-100 text-emerald-800 rounded-3xl premium-shadow">
          <span class="text-xl">✅</span>
          <p class="text-sm font-extrabold">{{ $page.props.flash.success }}</p>
        </div>
        <div v-if="$page.props.flash?.error" key="error" class="pointer-events-auto flex items-center gap-3 px-5 py-4 glass-card border-rose-100 text-rose-800 rounded-3xl premium-shadow">
          <span class="text-xl">❌</span>
          <p class="text-sm font-extrabold">{{ $page.props.flash.error }}</p>
        </div>
      </transition-group>
    </div>

    <!-- Main Content -->
    <main class="main-content container mx-auto px-4 sm:px-6 py-8">
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
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

* { box-sizing: border-box; }

.app-root { min-height: 100vh; background: #f8fafc; font-family: 'Plus Jakarta Sans', sans-serif; }

.navbar {
  background: linear-gradient(90deg, #1e3a5f 0%, #1e40af 100%);
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: 0 2px 16px rgba(30, 58, 95, 0.2);
}

.nav-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
  height: 60px;
  display: flex;
  align-items: center;
  gap: 2rem;
}

.nav-brand {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  text-decoration: none;
  color: #fff;
  flex-shrink: 0;
}
.nav-brand-icon {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: url('https://image2url.com/r2/default/images/1772014557556-c8413775-376d-46f3-ac39-40ae95668b58.jpg') center/cover;
  box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}
.brand-name { font-weight: 800; font-size: 1rem; color: #fff; line-height: 1.1; }
.brand-tag { font-size: 0.65rem; color: rgba(255,255,255,0.8); letter-spacing: 0.05em; text-transform: uppercase; }

.nav-links { display: flex; gap: 0.15rem; flex: 1; }
.nav-link {
  color: rgba(255,255,255,0.9);
  text-decoration: none;
  font-size: 0.9rem;
  font-weight: 600;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  transition: background 0.2s, color 0.2s;
}
.nav-link:hover { background: rgba(255,255,255,0.1); color: #fff; }
.nav-link.active { background: rgba(255,255,255,0.2); color: #fff; }

.nav-user { position: relative; margin-left: auto; }
.user-pill {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: rgba(255,255,255,0.1);
  border: 1px solid rgba(255,255,255,0.2);
  border-radius: 999px;
  padding: 0.35rem 0.75rem 0.35rem 0.35rem;
  cursor: pointer;
  color: white;
  font-size: 0.875rem;
  font-weight: 600;
  transition: background 0.2s;
}
.user-pill:hover { background: rgba(255,255,255,0.2); }
.user-avatar {
  width: 30px; height: 30px;
  background: #f59e0b;
  color: #1e3a5f;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 0.8rem;
}
.user-name { max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.chevron { font-size: 0.7rem; opacity: 0.7; }

.user-dropdown {
  position: absolute;
  right: 0;
  top: calc(100% + 8px);
  background: white;
  border-radius: 14px;
  box-shadow: 0 8px 40px rgba(0,0,0,0.15);
  min-width: 220px;
  overflow: hidden;
  z-index: 200;
}
.dropdown-info { padding: 1rem 1.1rem 0.75rem; }
.dropdown-name { font-weight: 700; font-size: 0.9rem; color: #111827; }
.dropdown-email { font-size: 0.75rem; color: #9ca3af; }
.dropdown-role {
  display: inline-block;
  margin-top: 0.35rem;
  font-size: 0.65rem;
  font-weight: 700;
  letter-spacing: 1px;
  background: #d1fae5;
  color: #065f46;
  border-radius: 999px;
  padding: 0.15rem 0.6rem;
}
.dropdown-divider { border: none; border-top: 1px solid #f3f4f6; margin: 0; }
.dropdown-item {
  display: block;
  padding: 0.7rem 1.1rem;
  font-size: 0.875rem;
  color: #374151;
  text-decoration: none;
  font-family: 'DM Sans', sans-serif;
  cursor: pointer;
  background: none;
  border: none;
  width: 100%;
  text-align: left;
  transition: background 0.15s;
}
.dropdown-item:hover { background: #f9fafb; }
.logout-item { color: #ef4444; }
.logout-item:hover { background: #fef2f2; }

.overlay {
  position: fixed;
  inset: 0;
  z-index: 99;
}

.flash {
  position: fixed;
  top: 72px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 500;
  padding: 0.75rem 2rem;
  border-radius: 12px;
  font-size: 0.9rem;
  font-weight: 600;
  box-shadow: 0 4px 20px rgba(0,0,0,0.15);
  white-space: nowrap;
}
.flash-success { background: #d1fae5; color: #065f46; }
.flash-error { background: #fee2e2; color: #991b1b; }

.slide-down-enter-active, .slide-down-leave-active { transition: all 0.3s; }
.slide-down-enter-from { opacity: 0; transform: translateX(-50%) translateY(-10px); }
.slide-down-leave-to { opacity: 0; transform: translateX(-50%) translateY(-10px); }

.main-content { min-height: calc(100vh - 60px); }

@media (max-width: 640px) {
  .nav-links { display: none; }
  .user-name { display: none; }
}
</style>
