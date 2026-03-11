<template>
  <div class="forbidden-root">
    <div class="card">
      <div class="icon">🚧</div>
      <h1 class="title">403 · Access Denied</h1>
      <p class="desc">This section is for Admin only. Please login using an admin account.</p>

      <div class="actions">
        <Link :href="route('login')" class="btn primary">Login</Link>
        <Link :href="route('logout')" method="post" as="button" class="btn">Logout</Link>
        <Link :href="route('stores.index')" class="btn">Back to Stores</Link>
      </div>
    </div>
  </div>
  </template>

<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap');

* { box-sizing: border-box; }
.forbidden-root {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #0f172a;
  font-family: 'DM Sans', sans-serif;
}
.card {
  background: white;
  border-radius: 16px;
  width: 100%;
  max-width: 520px;
  padding: 2rem;
  text-align: center;
  box-shadow: 0 10px 40px rgba(0,0,0,0.3);
}
.icon { font-size: 3rem; margin-bottom: 0.5rem; }
.title { font-family: 'Syne', sans-serif; font-size: 1.6rem; font-weight: 800; color: #0f172a; }
.desc { color: #64748b; margin-top: 0.25rem; margin-bottom: 1rem; }
.actions { display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap; }
.btn {
  padding: 0.6rem 1rem;
  border-radius: 10px;
  border: 1px solid #e5e7eb;
  text-decoration: none;
  color: #374151;
  font-weight: 700;
  font-size: 0.85rem;
}
.btn.primary {
  background: #10b981;
  color: white;
  border: none;
}
</style>
