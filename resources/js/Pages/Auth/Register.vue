<template>
  <div class="auth-root min-h-screen relative flex items-center justify-center p-6 overflow-hidden">
    <!-- Premium Unsplash Background -->
    <div class="absolute inset-0 z-0">
      <img
        src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=1920&q=80"
        class="w-full h-full object-cover scale-105 animate-slow-zoom"
        alt="Background"
      />
      <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-[2px]"></div>
    </div>

    <div class="relative z-10 w-full max-w-[500px] animate-fade-in-up">
      <div class="glass-card p-10 sm:p-12 rounded-[3rem] shadow-premium border-white/20 dark:border-slate-700/30 max-h-[90vh] overflow-y-auto custom-scrollbar">
        <div class="text-center mb-10">
          <div class="inline-flex w-20 h-20 bg-blue-600 rounded-[2rem] items-center justify-center shadow-2xl shadow-blue-500/40 mb-6 -rotate-3 hover:rotate-6 transition-transform duration-500">
            <span class="text-4xl">👋</span>
          </div>
          <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tighter mb-2">Create Account</h2>
          <p class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-[0.15em]">Join the Chow Zone Community</p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
          <div class="grid grid-cols-2 gap-4 p-1.5 bg-slate-100 dark:bg-slate-900/50 rounded-2xl">
            <button
              type="button"
              @click="form.role = 'student'"
              :class="[
                'py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all',
                form.role === 'student'
                  ? 'bg-white dark:bg-slate-800 text-blue-600 shadow-sm'
                  : 'text-slate-400 hover:text-slate-600'
              ]"
            >
              Student
            </button>
            <button
              type="button"
              @click="form.role = 'faculty'"
              :class="[
                'py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all',
                form.role === 'faculty'
                  ? 'bg-white dark:bg-slate-800 text-blue-600 shadow-sm'
                  : 'text-slate-400 hover:text-slate-600'
              ]"
            >
              Faculty
            </button>
          </div>

          <div class="space-y-2">
            <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-4">Full Name</label>
            <input v-model="form.name" type="text" placeholder="Juan Dela Cruz" class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-none rounded-[2rem] text-sm font-bold text-slate-700 dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all shadow-inner" />
            <span v-if="form.errors.name" class="text-[10px] font-bold text-rose-500 ml-4">{{ form.errors.name }}</span>
          </div>

          <div class="space-y-2">
            <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-4">NEMSU Email</label>
            <input v-model="form.email" type="email" placeholder="name@nemsu.edu.ph" class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-none rounded-[2rem] text-sm font-bold text-slate-700 dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all shadow-inner" />
            <span v-if="form.errors.email" class="text-[10px] font-bold text-rose-500 ml-4">{{ form.errors.email }}</span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="space-y-2" v-if="form.role === 'student'">
              <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-4">Student ID</label>
              <input v-model="form.student_id" type="text" placeholder="2024-00001" class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-none rounded-[2rem] text-sm font-bold text-slate-700 dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all shadow-inner" />
              <span v-if="form.errors.student_id" class="text-[10px] font-bold text-rose-500 ml-4">{{ form.errors.student_id }}</span>
            </div>
            <div class="space-y-2">
              <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-4">Phone</label>
              <input v-model="form.phone" type="text" placeholder="09XXXXXXXXX" class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-none rounded-[2rem] text-sm font-bold text-slate-700 dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all shadow-inner" />
              <span v-if="form.errors.phone" class="text-[10px] font-bold text-rose-500 ml-4">{{ form.errors.phone }}</span>
            </div>
          </div>

          <div class="space-y-2">
            <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-4">Department / College</label>
            <input v-model="form.department" type="text" placeholder="e.g. BSIT, BSED" class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-none rounded-[2rem] text-sm font-bold text-slate-700 dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all shadow-inner" />
            <span v-if="form.errors.department" class="text-[10px] font-bold text-rose-500 ml-4">{{ form.errors.department }}</span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="space-y-2">
              <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-4">Password</label>
              <input v-model="form.password" :type="showPass ? 'text' : 'password'" placeholder="Min. 8 char" class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-none rounded-[2rem] text-sm font-bold text-slate-700 dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all shadow-inner" />
              <span v-if="form.errors.password" class="text-[10px] font-bold text-rose-500 ml-4">{{ form.errors.password }}</span>
            </div>
            <div class="space-y-2">
              <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-4">Confirm</label>
              <input v-model="form.password_confirmation" :type="showPass ? 'text' : 'password'" placeholder="Re-enter" class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-none rounded-[2rem] text-sm font-bold text-slate-700 dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all shadow-inner" />
            </div>
          </div>

          <button
            type="submit"
            :disabled="form.processing"
            class="w-full py-5 bg-blue-600 text-white text-sm font-black uppercase tracking-widest rounded-[2rem] shadow-2xl shadow-blue-500/40 hover:bg-blue-700 hover:scale-[1.02] transition-all active:scale-95 disabled:opacity-50 disabled:grayscale"
          >
            {{ form.processing ? 'Registering...' : 'Create Account' }}
          </button>
        </form>

        <p class="text-center mt-10 text-sm font-bold text-slate-500 dark:text-slate-400">
          Already a member? <Link :href="route('login')" class="text-blue-600 dark:text-blue-400 font-black hover:underline">Log In</Link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'

const showPass = ref(false)

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: 'student',
  student_id: '',
  department: '',
  phone: '',
})

const submit = () => {
  form.post(route('register'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
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

.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  @apply bg-slate-200 dark:bg-slate-700 rounded-full;
}
</style>
