<template>
  <div class="welcome-root min-h-screen bg-slate-900 flex flex-col items-center justify-center p-6 relative overflow-hidden">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 z-0">
      <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-600/20 blur-[120px] rounded-full"></div>
      <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-indigo-600/20 blur-[120px] rounded-full"></div>
      <img src="https://images.unsplash.com/photo-1567521464024-feb5972ecf64?w=1600&q=80" alt="" class="absolute inset-0 w-full h-full object-cover opacity-10 mix-blend-overlay" />
    </div>

    <div class="relative z-10 w-full max-w-4xl text-center space-y-12">
      <header class="space-y-6">
        <div class="inline-flex p-4 glass-card border-white/10 rounded-[2.5rem] shadow-3d mb-4 active:scale-95 transition-transform duration-300">
          <div class="w-24 h-24 bg-blue-600 rounded-[2rem] flex items-center justify-center shadow-lg shadow-blue-500/20">
            <span class="text-5xl">🍽️</span>
          </div>
        </div>
        <h1 class="text-6xl sm:text-8xl font-black text-white tracking-tighter leading-none">
          NEMSU <br/>
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-300">Chow Zone.</span>
        </h1>
        <p class="text-xl sm:text-2xl font-medium text-slate-400 max-w-2xl mx-auto leading-relaxed">
          The ultimate campus food ordering experience. <br class="hidden sm:block"/>
          Premium flavors, delivered in real-time.
        </p>
      </header>

      <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
        <Link :href="route('login')" class="w-full sm:w-auto px-12 py-5 bg-white text-slate-900 text-lg font-black rounded-3xl shadow-3d hover:bg-blue-50 transition-all active:scale-95">
          Login Now
        </Link>
        <Link :href="route('register')" class="w-full sm:w-auto px-12 py-5 glass-card border-white/10 text-white text-lg font-black rounded-3xl shadow-3d hover:bg-white/5 transition-all active:scale-95">
          Join Community
        </Link>
      </div>

      <!-- Feature Pills -->
      <div class="flex flex-wrap justify-center gap-3 pt-8">
        <span v-for="name in storeNames" :key="name" class="px-5 py-2 glass-card border-white/5 text-slate-400 text-[10px] font-black uppercase tracking-widest rounded-full hover:border-blue-500/30 hover:text-blue-400 transition-all cursor-default">
          {{ name }}
        </span>
      </div>
    </div>

    <footer class="absolute bottom-8 text-slate-500 text-xs font-black uppercase tracking-[0.3em]">
      © {{ new Date().getFullYear() }} NEMSU Chow Zone · Premium Campus Dining
    </footer>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

const storeNames = [
  'Sisig Republic', "Tag'z Fruit", "Enjoy Kiddos' Hub", "Elma's Takoyaki",
  'Double G Street Foods', 'Il Rutto', 'Shawarma', 'Tuhog Corner',
  'Buko Juice', 'Fresh Shake', "Jac N' Jell", 'Sweet Corner'
]
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

.welcome-root {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
  font-family: 'Plus Jakarta Sans', sans-serif;
  padding: 2rem 1.5rem 4rem;
}

.welcome-bg {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, #1e3a5f 0%, #1e40af 60%, #2563eb 100%);
  background-image: url('https://images.unsplash.com/photo-1567521464024-feb5972ecf64?w=1600&q=80'), linear-gradient(135deg, rgba(30,58,95,0.92) 0%, rgba(37,99,235,0.9) 100%);
  background-size: cover;
  background-position: center;
  background-blend-mode: overlay;
}

.welcome-inner {
  position: relative;
  z-index: 1;
  text-align: center;
  max-width: 420px;
}

.brand { margin-bottom: 1.25rem; }
.brand-logo {
  width: 120px;
  height: 120px;
  border-radius: 24px;
  overflow: hidden;
  margin: 0 auto 1rem;
  box-shadow: 0 12px 32px rgba(0,0,0,0.25);
}
.brand-img { width: 100%; height: 100%; object-fit: cover; display: block; }
.brand-title {
  font-size: 1.85rem;
  font-weight: 800;
  color: #fff;
  margin: 0 0 0.35rem;
  letter-spacing: -0.03em;
  line-height: 1.2;
}
.brand-desc {
  color: rgba(255,255,255,0.9);
  font-size: 0.95rem;
  margin: 0;
  font-weight: 500;
}

.tagline {
  color: rgba(255,255,255,0.85);
  font-size: 0.9rem;
  margin-bottom: 1.75rem;
}
.tagline strong { color: #fff; }

.actions {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  margin-bottom: 2rem;
}

.btn {
  display: block;
  padding: 0.9rem 1.5rem;
  border-radius: 12px;
  font-weight: 700;
  font-size: 1rem;
  text-decoration: none;
  text-align: center;
  transition: transform 0.2s, box-shadow 0.2s;
  font-family: inherit;
}
.btn-primary {
  background: #fff;
  color: #1e3a5f;
  box-shadow: 0 4px 20px rgba(0,0,0,0.2);
}
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(0,0,0,0.25); }

.btn-secondary {
  background: rgba(255,255,255,0.15);
  color: #fff;
  border: 2px solid rgba(255,255,255,0.4);
  backdrop-filter: blur(8px);
}
.btn-secondary:hover { background: rgba(255,255,255,0.25); transform: translateY(-2px); }

.store-pills {
  display: flex;
  flex-wrap: wrap;
  gap: 0.4rem;
  justify-content: center;
}
.pill {
  background: rgba(255,255,255,0.12);
  color: rgba(255,255,255,0.95);
  padding: 0.3rem 0.65rem;
  border-radius: 8px;
  font-size: 0.7rem;
  border: 1px solid rgba(255,255,255,0.2);
  white-space: nowrap;
}

.footer {
  position: absolute;
  bottom: 1rem;
  left: 0;
  right: 0;
  text-align: center;
  color: rgba(255,255,255,0.5);
  font-size: 0.75rem;
  z-index: 1;
}

@media (max-width: 480px) {
  .brand-title { font-size: 1.6rem; }
  .brand-logo { width: 100px; height: 100px; }
}
</style>
