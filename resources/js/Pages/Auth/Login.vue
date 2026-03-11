<template>
  <div class="auth-root min-h-screen relative flex items-center justify-center p-6 overflow-hidden">
    <!-- Premium Unsplash Background -->
    <div class="absolute inset-0 z-0">
      <img
        src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=1920&q=80"
        class="w-full h-full object-cover scale-105 animate-slow-zoom"
        alt="Background"
      />
      <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-[2px]"></div>
    </div>

    <div class="relative z-10 w-full max-w-[440px] animate-fade-in-up">
      <div class="glass-card p-10 sm:p-12 rounded-[3rem] shadow-premium border-white/20 dark:border-slate-700/30">
        <div class="text-center mb-10">
          <div class="inline-flex w-20 h-20 bg-blue-600 rounded-[2rem] items-center justify-center shadow-2xl shadow-blue-500/40 mb-6 rotate-3 hover:rotate-12 transition-transform duration-500">
            <span class="text-4xl">🍽️</span>
          </div>
          <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tighter mb-2">Welcome Back</h2>
          <p class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-[0.15em]">NEMSU Chow Zone Admin</p>
        </div>

        <div v-if="status" class="status-msg mb-8 text-center bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 py-3 rounded-2xl font-bold text-sm">
          {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
          <div class="space-y-2">
            <label for="email" class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-4">Email Address</label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              placeholder="name@nemsu.edu.ph"
              class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-none rounded-[2rem] text-sm font-bold text-slate-700 dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all shadow-inner"
              :class="{ 'ring-2 ring-rose-500/50': form.errors.email }"
            />
            <span v-if="form.errors.email" class="text-[10px] font-bold text-rose-500 ml-4">{{ form.errors.email }}</span>
          </div>

          <div class="space-y-2">
            <label for="password" class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-4">Password</label>
            <div class="relative">
              <input
                id="password"
                v-model="form.password"
                :type="showPass ? 'text' : 'password'"
                placeholder="••••••••"
                class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-none rounded-[2rem] text-sm font-bold text-slate-700 dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all shadow-inner"
                :class="{ 'ring-2 ring-rose-500/50': form.errors.password }"
              />
              <button type="button" @click="showPass = !showPass" class="absolute right-6 top-1/2 -translate-y-1/2 text-slate-400 hover:text-blue-500 transition-colors">
                <svg v-if="!showPass" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
              </button>
            </div>
            <span v-if="form.errors.password" class="text-[10px] font-bold text-rose-500 ml-4">{{ form.errors.password }}</span>
          </div>

          <div class="flex items-center justify-between px-2">
            <label class="flex items-center gap-3 cursor-pointer group">
              <input type="checkbox" v-model="form.remember" class="w-5 h-5 rounded-lg border-none bg-slate-100 dark:bg-slate-800 text-blue-600 focus:ring-blue-500/20 shadow-inner" />
              <span class="text-xs font-bold text-slate-500 dark:text-slate-400 group-hover:text-slate-700 dark:group-hover:text-slate-200 transition-colors">Remember me</span>
            </label>
            <Link v-if="canResetPassword" :href="route('password.request')" class="text-xs font-black text-blue-600 dark:text-blue-400 hover:underline tracking-tight">Forgot password?</Link>
          </div>

          <button
            type="submit"
            :disabled="form.processing"
            class="w-full py-5 bg-blue-600 text-white text-sm font-black uppercase tracking-widest rounded-[2rem] shadow-2xl shadow-blue-500/40 hover:bg-blue-700 hover:scale-[1.02] transition-all active:scale-95 disabled:opacity-50 disabled:grayscale disabled:cursor-not-allowed"
          >
            {{ form.processing ? 'Verifying...' : 'Sign In Portal' }}
          </button>
        </form>

        <p class="text-center mt-10 text-sm font-bold text-slate-500 dark:text-slate-400">
          New here? <Link :href="route('register')" class="text-blue-600 dark:text-blue-400 font-black hover:underline">Create Account</Link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'

defineProps({
  canResetPassword: Boolean,
  status: String,
})

const showPass = ref(false)

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  })
}
</script>

<style scoped>
.animate-slow-zoom {
  animation: slow-zoom 20s ease-in-out infinite alternate;
}

@keyframes slow-zoom {
  from { transform: scale(1); }
  to { transform: scale(1.1); }
}

.animate-fade-in-up {
  animation: fade-in-up 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes fade-in-up {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.glass-card {
  @apply bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl border border-white/20 dark:border-slate-700/50;
}

.shadow-premium {
  box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.25), 0 18px 36px -18px rgba(0, 0, 0, 0.3);
}
</style>
