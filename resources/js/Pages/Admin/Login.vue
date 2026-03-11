<template>
  <div class="admin-login-root min-h-screen bg-slate-900 flex flex-col items-center justify-center p-6 relative overflow-hidden">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 z-0">
      <div class="absolute top-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-600/20 blur-[120px] rounded-full"></div>
      <div class="absolute bottom-[-10%] left-[-10%] w-[40%] h-[40%] bg-indigo-600/20 blur-[120px] rounded-full"></div>
    </div>

    <div class="relative z-10 w-full max-w-md">
      <div class="glass-card rounded-[2.5rem] p-10 shadow-3d border-white/10 text-center space-y-8">
        <header class="space-y-4">
          <div class="inline-flex p-4 bg-blue-600 rounded-[2rem] shadow-lg shadow-blue-500/20 mb-2">
            <span class="text-4xl">🔐</span>
          </div>
          <h1 class="text-3xl font-black text-white tracking-tighter">Admin Portal</h1>
          <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">NEMSU Chow Zone Control</p>
        </header>

        <form @submit.prevent="submit" class="space-y-6 text-left">
          <div class="space-y-2">
            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Email Address</label>
            <input 
              v-model="form.email" 
              type="email" 
              required 
              class="w-full px-6 py-4 bg-white/5 border-none rounded-2xl text-white font-bold focus:ring-2 focus:ring-blue-500 transition-all placeholder:text-slate-600"
              placeholder="admin@nemsu.edu.ph"
            />
            <div v-if="form.errors.email" class="text-xs font-bold text-rose-500 ml-2 mt-1">{{ form.errors.email }}</div>
          </div>

          <div class="space-y-2">
            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Password</label>
            <input 
              v-model="form.password" 
              type="password" 
              required 
              class="w-full px-6 py-4 bg-white/5 border-none rounded-2xl text-white font-bold focus:ring-2 focus:ring-blue-500 transition-all placeholder:text-slate-600"
              placeholder="••••••••"
            />
            <div v-if="form.errors.password" class="text-xs font-bold text-rose-500 ml-2 mt-1">{{ form.errors.password }}</div>
          </div>

          <div class="flex items-center justify-between px-2">
            <label class="flex items-center gap-2 cursor-pointer group">
              <input type="checkbox" v-model="form.remember" class="w-4 h-4 rounded bg-white/5 border-none text-blue-600 focus:ring-0" />
              <span class="text-xs font-bold text-slate-500 group-hover:text-slate-300 transition-colors">Remember me</span>
            </label>
            <Link :href="route('password.request')" class="text-xs font-black text-blue-400 hover:text-blue-300 transition-colors uppercase tracking-tighter">Forgot Password?</Link>
          </div>

          <button 
            type="submit" 
            :disabled="form.processing"
            class="w-full py-5 bg-blue-600 text-white text-sm font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-blue-500/30 hover:bg-blue-700 transition-all active:scale-95 disabled:opacity-50"
          >
            {{ form.processing ? 'Authenticating...' : 'Enter Dashboard' }}
          </button>
        </form>

        <footer class="pt-4 border-t border-white/5">
          <Link :href="route('home')" class="text-xs font-bold text-slate-500 hover:text-white transition-all uppercase tracking-widest flex items-center justify-center gap-2">
            <span>←</span> Return to Main Site
          </Link>
        </footer>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'

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
.admin-login-root {
  background: radial-gradient(circle at top left, #1e293b 0%, #0f172a 100%);
}
</style>
