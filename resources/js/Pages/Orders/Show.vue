<template>
  <AppLayout title="Order Details">
    <div class="order-detail-page">
      <div class="page-header">
        <div class="header-inner">
          <Link :href="route('orders.index')" class="back-link">← Back to Orders</Link>
          <h1 class="page-title">Order Details</h1>
          <div class="order-num">{{ order.order_number }}</div>
        </div>
      </div>

      <div class="detail-container">
        <div class="detail-grid">
          <!-- Left: Order Info -->
          <div class="detail-left">
            <!-- Status Timeline -->
            <div class="detail-card">
              <h2 class="card-title">📊 Order Status</h2>
              <div class="status-timeline">
                <div v-for="step in statusSteps" :key="step.key" class="timeline-step"
                  :class="{ active: isStepActive(step.key), current: order.status === step.key }">
                  <div class="step-circle">
                    <span class="step-icon">{{ step.icon }}</span>
                  </div>
                  <div class="step-info">
                    <div class="step-label">{{ step.label }}</div>
                    <div class="step-desc">{{ step.desc }}</div>
                  </div>
                  <div class="step-connector" v-if="step.key !== 'delivered'"></div>
                </div>
              </div>
            </div>

            <!-- Ordered Items -->
            <div class="detail-card">
              <h2 class="card-title">🍴 Ordered Items</h2>
              <div class="items-table">
                <div class="items-header">
                  <span>Item</span>
                  <span>Qty</span>
                  <span>Price</span>
                  <span>Subtotal</span>
                </div>
                <div v-for="item in order.order_items" :key="item.id" class="items-row">
                  <span class="item-name-cell">{{ item.menu_item?.name }}</span>
                  <span class="item-qty-cell">×{{ item.quantity }}</span>
                  <span class="item-price-cell">₱{{ parseFloat(item.price).toFixed(2) }}</span>
                  <span class="item-sub-cell">₱{{ parseFloat(item.subtotal).toFixed(2) }}</span>
                </div>
                <div class="items-total-row">
                  <span colspan="3">TOTAL</span>
                  <span class="grand-total">₱{{ parseFloat(order.total_amount).toFixed(2) }}</span>
                </div>
              </div>
            </div>

            <!-- Notes -->
            <div v-if="order.notes" class="detail-card">
              <h2 class="card-title">📝 Special Notes</h2>
              <p class="notes-text">{{ order.notes }}</p>
            </div>

            <!-- Actions -->
            <div class="detail-card" v-if="order.status === 'ready'">
              <h2 class="card-title">📬 Confirm Delivery</h2>
              <p class="confirm-desc">Once you receive your order, please confirm delivery so the store knows.</p>
              <button @click="confirmDelivery" :disabled="confirming" class="confirm-btn">
                <span v-if="confirming" class="spinner"></span>
                {{ confirming ? 'Confirming...' : '✅ I Received My Order!' }}
              </button>
            </div>
          </div>

          <!-- Right: Summary -->
          <div class="detail-right">
            <!-- Store Info -->
            <div class="detail-card">
              <h2 class="card-title">🏪 Store</h2>
              <div class="store-summary">
                <div class="store-emoji">🍽️</div>
                <div>
                  <div class="store-name">{{ order.store?.name }}</div>
                </div>
              </div>
            </div>

            <!-- Payment Info -->
            <div class="detail-card">
              <h2 class="card-title">💳 Payment</h2>
              <div class="payment-row">
                <span class="pay-label">Method</span>
                <span class="pay-val">{{ paymentIcon(order.payment_method) }} {{ order.payment_method?.toUpperCase() }}</span>
              </div>
              <div class="payment-row">
                <span class="pay-label">Status</span>
                <span :class="['pay-status', order.payment_status]">
                  {{ order.payment_status === 'paid' ? '✅ Paid' : '⏳ Unpaid' }}
                </span>
              </div>
              <div class="payment-row total-row">
                <span class="pay-label">Total</span>
                <span class="pay-total">₱{{ parseFloat(order.total_amount).toFixed(2) }}</span>
              </div>
              <div class="payment-row">
                <Link :href="route('orders.receipt', order.id)" class="receipt-link">🧾 View Receipt</Link>
              </div>
            </div>

            <!-- Delivery Info -->
            <div class="detail-card">
              <h2 class="card-title">📍 Delivery</h2>
              <p class="delivery-address">{{ order.delivery_address }}</p>
              <div v-if="order.delivered_at" class="delivered-time">
                <span>Delivered at: {{ formatDate(order.delivered_at) }}</span>
              </div>
            </div>

            <!-- Order Info -->
            <div class="detail-card">
              <h2 class="card-title">ℹ️ Order Info</h2>
              <div class="info-row">
                <span class="info-label">Placed</span>
                <span class="info-val">{{ formatDate(order.created_at) }}</span>
              </div>
              <div class="info-row">
                <span class="info-label">Status</span>
                <span :class="['status-pill', order.status]">{{ order.status.toUpperCase() }}</span>
              </div>
            </div>

            <!-- Rating Section (if delivered) -->
            <div v-if="order.status === 'delivered'" class="detail-card">
              <h2 class="card-title">⭐ Your Rating</h2>
              <div v-if="order.ratings?.length > 0" class="existing-rating">
                <div class="rating-stars">
                  <span v-for="i in 5" :key="i" :class="['r-star', { filled: i <= order.ratings[0].rating }]">★</span>
                </div>
                <p v-if="order.ratings[0].comment" class="rating-comment">{{ order.ratings[0].comment }}</p>
              </div>
              <div v-else class="rating-form">
                <p class="rate-prompt">How was your order? Leave a review!</p>
                <div class="star-input">
                  <button v-for="i in 5" :key="i" @click="ratingForm.rating = i"
                    :class="['star-btn', { active: i <= ratingForm.rating }]">★</button>
                </div>
                <textarea v-model="ratingForm.comment" rows="3" placeholder="Write your review..." class="rate-textarea"></textarea>
                <button @click="submitRating" :disabled="ratingForm.rating === 0 || ratingForm.processing" class="rate-submit-btn">
                  Submit Review
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({ order: Object })

const confirming = ref(false)
const ratingForm = useForm({ rating: 0, comment: '' })

const statusSteps = [
  { key: 'pending', icon: '⏳', label: 'Order Placed', desc: 'Waiting for store confirmation' },
  { key: 'confirmed', icon: '✅', label: 'Confirmed', desc: 'Store accepted your order' },
  { key: 'preparing', icon: '👨‍🍳', label: 'Preparing', desc: 'Your food is being prepared' },
  { key: 'ready', icon: '🎉', label: 'Ready', desc: 'Order ready for delivery' },
  { key: 'delivered', icon: '📦', label: 'Delivered', desc: 'Order received successfully' },
]

const statusOrder = ['pending', 'confirmed', 'preparing', 'ready', 'delivered']

const isStepActive = (stepKey) => {
  const currentIdx = statusOrder.indexOf(props.order.status)
  const stepIdx = statusOrder.indexOf(stepKey)
  return stepIdx <= currentIdx
}

const confirmDelivery = () => {
  confirming.value = true
  router.patch(route('orders.confirm-delivery', props.order.id), {}, {
    onFinish: () => { confirming.value = false }
  })
}

const submitRating = () => {
  ratingForm.post(route('ratings.store', props.order.id))
}

const paymentIcon = (method) => {
  const icons = { cod: '💵', gcash: '📱', paymaya: '💳' }
  return icons[method] || ''
}

const formatDate = (date) => {
  if (!date) return '—'
  return new Date(date).toLocaleDateString('en-PH', {
    month: 'short', day: 'numeric', year: 'numeric',
    hour: '2-digit', minute: '2-digit'
  })
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap');

* { box-sizing: border-box; }

.order-detail-page { font-family: 'DM Sans', sans-serif; }

.page-header { background: linear-gradient(135deg, #064e3b, #065f46); padding: 2rem 2rem 2.5rem; }
.header-inner { max-width: 1100px; margin: 0 auto; }
.back-link { color: #a7f3d0; text-decoration: none; font-size: 0.85rem; display: block; margin-bottom: 0.5rem; }
.back-link:hover { color: white; }
.page-title { font-family: 'Syne', sans-serif; font-size: 1.8rem; font-weight: 800; color: white; margin-bottom: 0.25rem; }
.order-num { font-size: 0.85rem; color: #6ee7b7; font-weight: 600; }

.detail-container { max-width: 1100px; margin: 0 auto; padding: 2rem 1.5rem; }
.detail-grid { display: grid; grid-template-columns: 1fr 340px; gap: 1.5rem; align-items: start; }

.detail-card {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: 0 2px 10px rgba(0,0,0,0.06);
  border: 1px solid #f3f4f6;
  margin-bottom: 1.25rem;
}
.card-title {
  font-family: 'Syne', sans-serif;
  font-size: 1rem;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 1.25rem;
}

/* Timeline */
.status-timeline { display: flex; flex-direction: column; gap: 0; }
.timeline-step {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  position: relative;
  padding-bottom: 1.25rem;
}
.timeline-step:last-child { padding-bottom: 0; }
.step-circle {
  width: 36px; height: 36px;
  border-radius: 50%;
  background: #f3f4f6;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
  flex-shrink: 0;
  border: 2px solid #e5e7eb;
  transition: all 0.3s;
}
.timeline-step.active .step-circle { background: #d1fae5; border-color: #10b981; }
.timeline-step.current .step-circle { background: #10b981; border-color: #065f46; transform: scale(1.1); }
.step-info { flex: 1; }
.step-label { font-weight: 700; font-size: 0.875rem; color: #374151; }
.timeline-step.active .step-label { color: #065f46; }
.timeline-step.current .step-label { color: #10b981; }
.step-desc { font-size: 0.75rem; color: #9ca3af; margin-top: 0.1rem; }
.step-connector {
  position: absolute;
  left: 17px;
  top: 38px;
  width: 2px;
  height: calc(100% - 14px);
  background: #e5e7eb;
}
.timeline-step.active .step-connector { background: #10b981; }

/* Items Table */
.items-table { border: 1px solid #f3f4f6; border-radius: 10px; overflow: hidden; }
.items-header {
  display: grid;
  grid-template-columns: 1fr 60px 80px 90px;
  gap: 0.5rem;
  padding: 0.6rem 1rem;
  background: #f9fafb;
  font-size: 0.75rem;
  font-weight: 700;
  color: #9ca3af;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.items-row {
  display: grid;
  grid-template-columns: 1fr 60px 80px 90px;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  font-size: 0.875rem;
  color: #374151;
  border-top: 1px solid #f3f4f6;
}
.item-name-cell { font-weight: 600; }
.item-qty-cell, .item-price-cell, .item-sub-cell { text-align: center; }
.item-sub-cell { font-weight: 700; color: #065f46; text-align: right; }
.items-total-row {
  display: grid;
  grid-template-columns: 1fr 60px 80px 90px;
  padding: 0.85rem 1rem;
  background: #f0fdf4;
  font-weight: 700;
  font-size: 0.9rem;
  color: #064e3b;
  border-top: 2px solid #d1fae5;
}
.grand-total { text-align: right; font-size: 1.1rem; color: #10b981; }

.notes-text { color: #6b7280; font-size: 0.875rem; background: #f9fafb; padding: 0.75rem; border-radius: 8px; }

.confirm-desc { color: #6b7280; font-size: 0.875rem; margin-bottom: 1rem; }
.confirm-btn {
  width: 100%;
  background: linear-gradient(135deg, #10b981, #065f46);
  color: white;
  border: none;
  border-radius: 12px;
  padding: 0.85rem;
  font-size: 0.95rem;
  font-weight: 700;
  font-family: 'DM Sans', sans-serif;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  transition: all 0.2s;
}
.confirm-btn:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(16,185,129,0.4); }
.confirm-btn:disabled { opacity: 0.5; cursor: not-allowed; }

/* Right Column */
.store-summary { display: flex; align-items: center; gap: 0.85rem; }
.store-emoji { font-size: 2.5rem; }
.store-name { font-weight: 700; font-size: 0.95rem; color: #111827; }

.payment-row { display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; border-bottom: 1px solid #f3f4f6; font-size: 0.875rem; }
.payment-row:last-child { border-bottom: none; }
.pay-label { color: #9ca3af; }
.pay-val { font-weight: 600; color: #374151; }
.pay-status.paid { color: #10b981; font-weight: 700; }
.pay-status.unpaid { color: #f59e0b; font-weight: 700; }
.total-row { border-top: 2px solid #f3f4f6; margin-top: 0.25rem; padding-top: 0.75rem; }
.pay-total { font-size: 1.1rem; font-weight: 800; color: #10b981; }

.delivery-address { font-size: 0.875rem; color: #374151; font-weight: 600; }
.delivered-time { margin-top: 0.75rem; font-size: 0.78rem; color: #10b981; font-weight: 600; }

.info-row { display: flex; justify-content: space-between; align-items: center; padding: 0.4rem 0; font-size: 0.875rem; }
.info-label { color: #9ca3af; }
.info-val { color: #374151; font-weight: 600; font-size: 0.8rem; }
.status-pill {
  font-size: 0.7rem;
  font-weight: 700;
  padding: 0.25rem 0.6rem;
  border-radius: 999px;
}
.status-pill.pending { background: #fef9c3; color: #854d0e; }
.status-pill.confirmed { background: #dbeafe; color: #1e3a8a; }
.status-pill.preparing { background: #fed7aa; color: #7c2d12; }
.status-pill.ready { background: #d1fae5; color: #065f46; }
.status-pill.delivered { background: #dcfce7; color: #14532d; }

/* Rating */
.existing-rating { }
.rating-stars { display: flex; gap: 0.25rem; margin-bottom: 0.5rem; }
.r-star { font-size: 1.2rem; color: #d1d5db; }
.r-star.filled { color: #f59e0b; }
.rating-comment { font-size: 0.875rem; color: #6b7280; font-style: italic; }

.rate-prompt { font-size: 0.875rem; color: #6b7280; margin-bottom: 0.75rem; }
.star-input { display: flex; gap: 0.35rem; margin-bottom: 0.75rem; }
.star-btn {
  font-size: 1.8rem;
  background: none;
  border: none;
  cursor: pointer;
  color: #d1d5db;
  transition: all 0.15s;
}
.star-btn.active { color: #f59e0b; }
.rate-textarea {
  width: 100%;
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  padding: 0.65rem;
  font-family: 'DM Sans', sans-serif;
  font-size: 0.85rem;
  resize: none;
  margin-bottom: 0.75rem;
}
.rate-textarea:focus { outline: none; border-color: #10b981; }
.rate-submit-btn {
  width: 100%;
  background: linear-gradient(135deg, #10b981, #065f46);
  color: white;
  border: none;
  border-radius: 10px;
  padding: 0.75rem;
  font-weight: 700;
  font-family: 'DM Sans', sans-serif;
  cursor: pointer;
  transition: all 0.2s;
}
.rate-submit-btn:disabled { opacity: 0.5; cursor: not-allowed; }

.spinner { width: 15px; height: 15px; border: 2px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

@media (max-width: 860px) {
  .detail-grid { grid-template-columns: 1fr; }
}
</style>
