<template>
  <AppLayout title="My Orders">
    <div class="orders-page">
      <div class="page-header">
        <div class="header-inner">
          <h1 class="page-title">📦 My Orders</h1>
          <p class="page-sub">Track all your orders from NEMSU Chow Zone</p>
        </div>
      </div>

      <div class="orders-container">
        <!-- Filter Tabs -->
        <div class="filter-tabs">
          <button
            v-for="tab in tabs"
            :key="tab.value"
            @click="activeTab = tab.value"
            :class="['tab-btn', { active: activeTab === tab.value }]"
          >
            {{ tab.icon }} {{ tab.label }}
            <span v-if="countByStatus(tab.value) > 0" class="tab-count">
              {{ countByStatus(tab.value) }}
            </span>
          </button>
        </div>

        <!-- Empty State -->
        <div v-if="filteredOrders.length === 0" class="empty-state">
          <div class="empty-icon">📭</div>
          <h3>No orders yet</h3>
          <p>{{ activeTab === 'all' ? "You haven't ordered anything yet!" : `No ${activeTab} orders.` }}</p>
          <Link :href="route('stores.index')" class="browse-btn">Browse Stores →</Link>
        </div>

        <!-- Orders List -->
        <div v-else class="orders-list">
          <div
            v-for="order in filteredOrders"
            :key="order.id"
            class="order-card"
          >
            <!-- Order Header -->
            <div class="order-card-header">
              <div class="order-info">
                <div class="order-number">{{ order.order_number }}</div>
                <div class="order-store">📍 {{ order.store?.name }}</div>
              </div>
              <div class="order-status-group">
                <span :class="['status-badge', order.status]">
                  {{ statusIcon(order.status) }} {{ order.status.toUpperCase() }}
                </span>
                <span :class="['pay-badge', order.payment_status]">
                  {{ order.payment_status === 'paid' ? '✅ Paid' : '⏳ Unpaid' }}
                </span>
              </div>
            </div>

            <!-- Order Items -->
            <div class="order-items">
              <div v-for="item in order.order_items" :key="item.id" class="order-item">
                <span class="order-item-name">{{ item.menu_item?.name }}</span>
                <span class="order-item-qty">×{{ item.quantity }}</span>
                <span class="order-item-price">₱{{ parseFloat(item.subtotal).toFixed(2) }}</span>
              </div>
            </div>

            <!-- Order Footer -->
            <div class="order-card-footer">
              <div class="order-meta">
                <span class="payment-method">
                  {{ paymentIcon(order.payment_method) }} {{ order.payment_method.toUpperCase() }}
                </span>
                <span class="order-date">{{ formatDate(order.created_at) }}</span>
              </div>
              <div class="order-total">
                <span class="total-label">Total:</span>
                <span class="total-val">₱{{ parseFloat(order.total_amount).toFixed(2) }}</span>
              </div>
            </div>

            <!-- Actions -->
            <div class="order-actions">
              <Link :href="route('orders.show', order.id)" class="action-btn detail-btn">
                View Details
              </Link>

              <!-- Confirm Delivery Button -->
              <button
                v-if="order.status === 'ready' && order.status !== 'delivered'"
                @click="confirmDelivery(order.id)"
                class="action-btn confirm-btn"
              >
                ✅ Confirm Received
              </button>

              <!-- Rate Button -->
              <button
                v-if="order.status === 'delivered' && order.ratings?.length === 0"
                @click="openRating(order)"
                class="action-btn rate-btn"
              >
                ⭐ Rate Order
              </button>

              <span v-if="order.status === 'delivered' && order.ratings?.length > 0" class="rated-badge">
                ✅ Rated
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Rating Modal -->
    <div v-if="ratingModal" class="modal-overlay" @click.self="ratingModal = false">
      <div class="modal-card">
        <div class="modal-header">
          <h3>⭐ Rate Your Order</h3>
          <button @click="ratingModal = false" class="modal-close">✕</button>
        </div>
        <div class="modal-body">
          <p class="modal-store">{{ selectedOrder?.store?.name }}</p>

          <!-- Star Rating -->
          <div class="star-selector">
            <button
              v-for="i in 5"
              :key="i"
              @click="ratingForm.rating = i"
              @mouseover="hoverRating = i"
              @mouseleave="hoverRating = 0"
              :class="['star-btn', { active: i <= (hoverRating || ratingForm.rating) }]"
            >
              ★
            </button>
          </div>
          <p class="rating-label">{{ ratingLabels[hoverRating || ratingForm.rating] || 'Select a rating' }}</p>

          <!-- Comment -->
          <textarea
            v-model="ratingForm.comment"
            rows="3"
            placeholder="Write your review here... (optional)"
            class="rating-textarea"
          ></textarea>

          <button @click="submitRating" :disabled="ratingForm.rating === 0 || ratingForm.processing" class="submit-rating-btn">
            {{ ratingForm.processing ? 'Submitting...' : 'Submit Review ⭐' }}
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({ orders: Array })

const activeTab = ref('all')
const ratingModal = ref(false)
const selectedOrder = ref(null)
const hoverRating = ref(0)

const tabs = [
  { value: 'all', label: 'All', icon: '📋' },
  { value: 'pending', label: 'Pending', icon: '⏳' },
  { value: 'confirmed', label: 'Confirmed', icon: '✅' },
  { value: 'preparing', label: 'Preparing', icon: '👨‍🍳' },
  { value: 'ready', label: 'Ready', icon: '🎉' },
  { value: 'delivered', label: 'Delivered', icon: '📦' },
]

const filteredOrders = computed(() => {
  if (activeTab.value === 'all') return props.orders
  return props.orders.filter(o => o.status === activeTab.value)
})

const countByStatus = (status) => {
  if (status === 'all') return 0
  return props.orders.filter(o => o.status === status).length
}

const statusIcon = (status) => {
  const icons = { pending: '⏳', confirmed: '✅', preparing: '👨‍🍳', ready: '🎉', delivered: '📦', cancelled: '❌' }
  return icons[status] || ''
}

const paymentIcon = (method) => {
  const icons = { cod: '💵', gcash: '📱', paymaya: '💳' }
  return icons[method] || ''
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-PH', {
    month: 'short', day: 'numeric', year: 'numeric',
    hour: '2-digit', minute: '2-digit'
  })
}

const confirmDelivery = (orderId) => {
  router.patch(route('orders.confirm-delivery', orderId))
}

const ratingForm = useForm({ rating: 0, comment: '', menu_item_id: null })
const ratingLabels = { 1: '😞 Poor', 2: '😐 Fair', 3: '🙂 Good', 4: '😊 Very Good', 5: '🤩 Excellent!' }

const openRating = (order) => {
  selectedOrder.value = order
  ratingForm.rating = 0
  ratingForm.comment = ''
  ratingModal.value = true
}

const submitRating = () => {
  ratingForm.post(route('ratings.store', selectedOrder.value.id), {
    onSuccess: () => { ratingModal.value = false }
  })
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap');

* { box-sizing: border-box; }

.orders-page { font-family: 'DM Sans', sans-serif; }

.page-header {
  background: linear-gradient(135deg, #064e3b, #065f46);
  padding: 2rem 2rem 2.5rem;
}
.header-inner { max-width: 900px; margin: 0 auto; }
.page-title { font-family: 'Syne', sans-serif; font-size: 2rem; font-weight: 800; color: white; margin-bottom: 0.25rem; }
.page-sub { color: #a7f3d0; font-size: 0.9rem; }

.orders-container { max-width: 900px; margin: 0 auto; padding: 2rem 1.5rem; }

/* Filter Tabs */
.filter-tabs {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
  margin-bottom: 1.5rem;
}
.tab-btn {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.5rem 1rem;
  border-radius: 999px;
  border: 2px solid #e5e7eb;
  background: white;
  font-size: 0.8rem;
  font-weight: 600;
  cursor: pointer;
  color: #6b7280;
  font-family: 'DM Sans', sans-serif;
  transition: all 0.2s;
}
.tab-btn.active { background: #064e3b; border-color: #064e3b; color: white; }
.tab-btn:hover:not(.active) { border-color: #10b981; color: #065f46; }
.tab-count {
  background: #ef4444;
  color: white;
  font-size: 0.65rem;
  font-weight: 800;
  padding: 0.1rem 0.4rem;
  border-radius: 999px;
}

/* Empty State */
.empty-state { text-align: center; padding: 4rem 2rem; }
.empty-icon { font-size: 3.5rem; margin-bottom: 1rem; }
.empty-state h3 { font-family: 'Syne', sans-serif; font-size: 1.25rem; color: #374151; margin-bottom: 0.4rem; }
.empty-state p { color: #9ca3af; margin-bottom: 1.5rem; font-size: 0.9rem; }
.browse-btn {
  display: inline-block;
  background: linear-gradient(135deg, #10b981, #065f46);
  color: white;
  padding: 0.75rem 2rem;
  border-radius: 12px;
  text-decoration: none;
  font-weight: 700;
}

/* Order Cards */
.orders-list { display: flex; flex-direction: column; gap: 1rem; }
.order-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.06);
  border: 1px solid #f3f4f6;
  overflow: hidden;
  transition: box-shadow 0.2s;
}
.order-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,0.1); }

.order-card-header {
  padding: 1rem 1.25rem;
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  border-bottom: 1px solid #f3f4f6;
  gap: 1rem;
}
.order-number { font-family: 'DM Sans', monospace; font-size: 0.8rem; font-weight: 700; color: #374151; margin-bottom: 0.2rem; }
.order-store { font-size: 0.8rem; color: #9ca3af; }

.order-status-group { display: flex; flex-direction: column; gap: 0.35rem; align-items: flex-end; }
.status-badge {
  font-size: 0.7rem;
  font-weight: 700;
  padding: 0.3rem 0.75rem;
  border-radius: 999px;
  letter-spacing: 0.5px;
}
.status-badge.pending { background: #fef9c3; color: #854d0e; }
.status-badge.confirmed { background: #dbeafe; color: #1e3a8a; }
.status-badge.preparing { background: #fed7aa; color: #7c2d12; }
.status-badge.ready { background: #d1fae5; color: #065f46; }
.status-badge.delivered { background: #dcfce7; color: #14532d; }
.status-badge.cancelled { background: #fee2e2; color: #991b1b; }

.pay-badge {
  font-size: 0.7rem;
  font-weight: 700;
  padding: 0.2rem 0.6rem;
  border-radius: 999px;
}
.pay-badge.paid { background: #d1fae5; color: #065f46; }
.pay-badge.unpaid { background: #fef3c7; color: #92400e; }

.order-items {
  padding: 0.85rem 1.25rem;
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
  border-bottom: 1px solid #f3f4f6;
}
.order-item {
  display: flex;
  align-items: center;
  font-size: 0.85rem;
  gap: 0.5rem;
}
.order-item-name { flex: 1; color: #374151; font-weight: 500; }
.order-item-qty { color: #9ca3af; }
.order-item-price { font-weight: 700; color: #064e3b; min-width: 70px; text-align: right; }

.order-card-footer {
  padding: 0.75rem 1.25rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #f9fafb;
  border-bottom: 1px solid #f3f4f6;
}
.order-meta { display: flex; gap: 1rem; align-items: center; }
.payment-method { font-size: 0.78rem; color: #6b7280; font-weight: 600; }
.order-date { font-size: 0.75rem; color: #9ca3af; }
.order-total { display: flex; align-items: center; gap: 0.5rem; }
.total-label { font-size: 0.8rem; color: #6b7280; }
.total-val { font-weight: 800; color: #10b981; font-size: 1rem; }

.order-actions {
  padding: 0.85rem 1.25rem;
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
  align-items: center;
}
.action-btn {
  padding: 0.5rem 1rem;
  border-radius: 8px;
  font-size: 0.8rem;
  font-weight: 700;
  font-family: 'DM Sans', sans-serif;
  cursor: pointer;
  border: none;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  transition: all 0.15s;
}
.detail-btn { background: #f3f4f6; color: #374151; }
.detail-btn:hover { background: #e5e7eb; }
.confirm-btn { background: #10b981; color: white; }
.confirm-btn:hover { background: #059669; }
.rate-btn { background: #fef9c3; color: #854d0e; }
.rate-btn:hover { background: #fef08a; }
.rated-badge { font-size: 0.8rem; color: #10b981; font-weight: 700; }

/* Rating Modal */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 1rem;
}
.modal-card {
  background: white;
  border-radius: 20px;
  width: 100%;
  max-width: 400px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.3);
  overflow: hidden;
}
.modal-header {
  background: linear-gradient(135deg, #064e3b, #10b981);
  padding: 1.25rem 1.5rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.modal-header h3 { color: white; font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.1rem; }
.modal-close { background: rgba(255,255,255,0.2); border: none; border-radius: 50%; width: 28px; height: 28px; color: white; cursor: pointer; font-size: 0.85rem; display: flex; align-items: center; justify-content: center; }
.modal-body { padding: 1.5rem; }
.modal-store { color: #6b7280; font-size: 0.875rem; margin-bottom: 1.25rem; font-weight: 600; }

.star-selector { display: flex; gap: 0.5rem; justify-content: center; margin-bottom: 0.5rem; }
.star-btn {
  font-size: 2.5rem;
  background: none;
  border: none;
  cursor: pointer;
  color: #d1d5db;
  transition: all 0.15s;
  line-height: 1;
}
.star-btn.active { color: #f59e0b; transform: scale(1.1); }

.rating-label { text-align: center; font-size: 0.875rem; color: #6b7280; font-weight: 600; margin-bottom: 1rem; min-height: 20px; }

.rating-textarea {
  width: 100%;
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  padding: 0.75rem;
  font-family: 'DM Sans', sans-serif;
  font-size: 0.875rem;
  resize: none;
  color: #111827;
  margin-bottom: 1rem;
}
.rating-textarea:focus { outline: none; border-color: #10b981; }

.submit-rating-btn {
  width: 100%;
  background: linear-gradient(135deg, #10b981, #065f46);
  color: white;
  border: none;
  border-radius: 12px;
  padding: 0.85rem;
  font-size: 0.9rem;
  font-weight: 700;
  font-family: 'DM Sans', sans-serif;
  cursor: pointer;
  transition: all 0.2s;
}
.submit-rating-btn:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(16,185,129,0.4); }
.submit-rating-btn:disabled { opacity: 0.5; cursor: not-allowed; }
</style>
