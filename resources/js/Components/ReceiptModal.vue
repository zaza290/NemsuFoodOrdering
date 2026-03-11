<template>
  <div v-if="show" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto no-print">
    <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md overflow-hidden animate-fade-in-up">
      <div class="p-8 border-b border-slate-100 flex items-center justify-between">
        <h3 class="text-xl font-black text-slate-900 tracking-tight">Receipt Generated</h3>
        <button @click="$emit('close')" class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 hover:text-rose-500 transition-colors">✕</button>
      </div>

      <div class="p-8">
        <!-- Receipt Preview -->
        <div id="receipt-content" class="receipt-paper bg-white p-8 shadow-inner border border-slate-100 mx-auto">
          <div class="text-center space-y-2 mb-6">
            <h1 class="text-lg font-black tracking-tighter uppercase">NEMSU Chow Zone</h1>
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ storeName }}</p>
            <div class="text-[9px] font-mono text-slate-400">
              {{ formatDateTime(new Date()) }}<br/>
              Order #{{ orderNumber }}
            </div>
          </div>

          <div class="border-t border-dashed border-slate-300 my-4"></div>

          <div class="space-y-3">
            <div v-for="item in items" :key="item.id" class="flex justify-between text-[11px] font-mono">
              <div class="flex-1 pr-4">
                <div>{{ item.name }}</div>
                <div class="text-[9px] text-slate-400">{{ item.quantity }} x ₱{{ formatPrice(item.price) }}</div>
              </div>
              <div class="font-bold">₱{{ formatPrice(item.price * item.quantity) }}</div>
            </div>
          </div>

          <div class="border-t border-dashed border-slate-300 my-4"></div>

          <div class="space-y-2">
            <div class="flex justify-between text-[10px] font-mono">
              <span>Subtotal</span>
              <span>₱{{ formatPrice(total) }}</span>
            </div>
            <div class="flex justify-between text-xs font-black font-mono">
              <span>TOTAL</span>
              <span>₱{{ formatPrice(total) }}</span>
            </div>
          </div>

          <div class="border-t border-dashed border-slate-300 my-4"></div>

          <div class="text-center space-y-2 mt-4">
            <p class="text-[10px] font-black uppercase tracking-widest">Customer: {{ customerName || 'Walk-in' }}</p>
            <p class="text-[9px] font-mono text-slate-400">Payment: {{ paymentMethod.toUpperCase() }}</p>
            <div class="pt-4">
              <p class="text-[9px] font-bold text-slate-400 italic">Thank you for dining with us!</p>
            </div>
          </div>
        </div>

        <div class="mt-8 flex gap-4">
          <button
            @click="printReceipt"
            class="flex-1 py-4 bg-blue-600 text-white text-xs font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-blue-500/30 hover:bg-blue-700 transition-all active:scale-95 flex items-center justify-center gap-2"
          >
            <span>🖨️</span> Print Receipt
          </button>
          <button
            @click="$emit('close')"
            class="flex-1 py-4 bg-slate-50 text-slate-400 text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-slate-100 transition-all active:scale-95"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  show: Boolean,
  storeName: String,
  customerName: String,
  orderNumber: String,
  items: Array,
  total: Number,
  paymentMethod: String
})

const emit = defineEmits(['close'])

const formatPrice = (val) => {
  if (!val) return '0.00'
  return parseFloat(val).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

const formatDateTime = (date) => {
  return new Intl.DateTimeFormat('en-PH', {
    dateStyle: 'medium',
    timeStyle: 'short'
  }).format(date)
}

const printReceipt = () => {
  window.print()
}
</script>

<style scoped>
.receipt-paper {
  width: 100%;
  max-width: 300px;
  font-family: 'Courier New', Courier, monospace;
}

@media print {
  .no-print {
    display: none !important;
  }

  body * {
    visibility: hidden;
  }

  #receipt-content, #receipt-content * {
    visibility: visible;
  }

  #receipt-content {
    position: absolute;
    left: 0;
    top: 0;
    width: 80mm; /* Standard thermal printer width */
    margin: 0;
    padding: 10px;
    border: none;
    box-shadow: none;
  }
}

.animate-fade-in-up {
  animation: fade-in-up 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes fade-in-up {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  @apply bg-slate-200 rounded-full;
}
</style>
