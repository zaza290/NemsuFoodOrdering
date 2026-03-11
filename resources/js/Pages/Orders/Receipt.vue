<template>
  <div class="receipt-container font-mono text-slate-900 bg-white p-4 max-w-[320px] mx-auto">
    <!-- Header -->
    <header class="text-center space-y-2 pb-4 border-b border-dashed border-slate-400">
      <h1 class="text-2xl font-black tracking-tighter">NEMSU Chow Zone</h1>
      <p class="text-xs font-bold">North Eastern Mindanao State University</p>
      <p class="text-xs">{{ order.store.name }}</p>
      <p class="text-xs">{{ new Date(order.created_at).toLocaleString() }}</p>
    </header>

    <!-- Order Details -->
    <section class="py-4 border-b border-dashed border-slate-400">
      <div class="flex justify-between text-xs">
        <span class="font-bold">Order #</span>
        <span>{{ order.order_number }}</span>
      </div>
      <div class="flex justify-between text-xs">
        <span class="font-bold">Customer:</span>
        <span>{{ order.customer_name || order.user.name }}</span>
      </div>
      <div class="flex justify-between text-xs">
        <span class="font-bold">Type:</span>
        <span class="uppercase">{{ order.order_type.replace('_', ' ') }}</span>
      </div>
    </section>

    <!-- Items -->
    <section class="py-4 border-b border-dashed border-slate-400">
      <div class="grid grid-cols-12 text-xs font-bold mb-2">
        <div class="col-span-1">QTY</div>
        <div class="col-span-7">ITEM</div>
        <div class="col-span-4 text-right">AMOUNT</div>
      </div>
      <div v-for="item in order.order_items" :key="item.id" class="grid grid-cols-12 text-xs mb-1">
        <div class="col-span-1">{{ item.quantity }}x</div>
        <div class="col-span-7 truncate">{{ item.menu_item.name }}</div>
        <div class="col-span-4 text-right">₱{{ parseFloat(item.subtotal).toFixed(2) }}</div>
      </div>
    </section>

    <!-- Totals -->
    <section class="py-4 space-y-2">
      <div class="flex justify-between items-center text-xs">
        <span class="font-bold uppercase">Subtotal</span>
        <span class="font-bold">₱{{ parseFloat(order.total_amount).toFixed(2) }}</span>
      </div>
      <div class="flex justify-between items-center text-xs">
        <span class="font-bold uppercase">Tax</span>
        <span class="font-bold">₱0.00</span>
      </div>
      <div class="flex justify-between items-center text-lg mt-2 pt-2 border-t border-slate-300">
        <span class="font-black">TOTAL</span>
        <span class="font-black">₱{{ parseFloat(order.total_amount).toFixed(2) }}</span>
      </div>
    </section>

    <!-- Footer -->
    <footer class="text-center pt-4 border-t border-dashed border-slate-400 space-y-2 text-xs">
      <p class="font-bold">Thank you for your order!</p>
      <p>This is an official receipt.</p>
    </footer>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'

defineProps({
  order: {
    type: Object,
    required: true,
  },
})

// Automatically trigger print dialog when component is loaded
onMounted(() => {
  window.print()
})
</script>

<style>
@media print {
  body, html {
    margin: 0;
    padding: 0;
    background: #fff;
  }
  .receipt-container {
    max-width: 100%;
    margin: 0;
    padding: 0;
    box-shadow: none;
    border: none;
  }
  /* Hide other elements on the page when printing */
  body > *:not(.receipt-container) {
    display: none;
  }
}
</style>
