<template>
  <AdminLayout title="Orders Management">
    <div class="orders-mgmt">
      <!-- Filters -->
      <div class="filters-bar">
        <div class="filters-left">
          <div class="search-wrap">
            <span class="search-icon">🔍</span>
            <input v-model="search" type="text" placeholder="Search order # or customer..." class="search-input" />
          </div>
        </div>
        <div class="filters-right">
          <select v-model="statusFilter" class="filter-select">
            <option value="">All Statuses</option>
            <option value="pending">⏳ Pending</option>
            <option value="confirmed">✅ Confirmed</option>
            <option value="preparing">👨‍🍳 Preparing</option>
            <option value="ready">🎉 Ready</option>
            <option value="delivered">📦 Delivered</option>
            <option value="cancelled">❌ Cancelled</option>
          </select>
          <select v-model="paymentFilter" class="filter-select">
            <option value="">All Payments</option>
            <option value="cod">💵 COD</option>
            <option value="gcash">📱 GCash</option>
            <option value="paymaya">💳 PayMaya</option>
          </select>
        </div>
      </div>

      <!-- Stats Row -->
      <div class="mini-stats">
        <div v-for="s in miniStats" :key="s.label" class="mini-stat" :class="s.color">
          <span class="mini-stat-icon">{{ s.icon }}</span>
          <div>
            <div class="mini-stat-val">{{ s.value }}</div>
            <div class="mini-stat-label">{{ s.label }}</div>
          </div>
        </div>
      </div>

      <!-- Orders Table -->
      <div class="table-card">
        <table class="orders-table">
          <thead>
            <tr>
              <th>Order #</th>
              <th>Customer</th>
              <th>Store</th>
              <th>Items</th>
              <th>Amount</th>
              <th>Payment</th>
              <th>Contact</th>
              <th>Status</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in filteredOrders" :key="order.id">
              <td class="mono">{{ order.order_number }}</td>
              <td>
                <div class="cust-name">{{ order.user?.name }}</div>
                <div class="cust-role">{{ order.user?.role }}</div>
              </td>
              <td class="mono">{{ order.contact_phone || '—' }}</td>
              <td class="store-cell">{{ order.store?.name }}</td>
              <td>
                <div class="items-preview">
                  <span v-for="item in (order.order_items || []).slice(0, 2)" :key="item.id" class="item-preview-tag">
                    {{ item.menu_item?.name }} ×{{ item.quantity }}
                  </span>
                  <span v-if="(order.order_items || []).length > 2" class="item-more">
                    +{{ order.order_items.length - 2 }}
                  </span>
                </div>
              </td>
              <td class="amount-cell">₱{{ parseFloat(order.total_amount).toFixed(2) }}</td>
              <td>
                <div class="pay-info">
                  <span :class="['pay-method', order.payment_method]">{{ order.payment_method?.toUpperCase() }}</span>
                  <span :class="['pay-status-dot', order.payment_status]">
                    {{ order.payment_status === 'paid' ? '✅' : '⏳' }}
                  </span>
                </div>
              </td>
              <td>
                <select
                  :value="order.status"
                  @change="updateStatus(order.id, $event.target.value)"
                  :class="['status-select', order.status]"
                >
                  <option value="pending">⏳ Pending</option>
                  <option value="confirmed">✅ Confirmed</option>
                  <option value="preparing">👨‍🍳 Preparing</option>
                  <option value="ready">🎉 Ready</option>
                  <option value="delivered">📦 Delivered</option>
                  <option value="cancelled">❌ Cancelled</option>
                </select>
              </td>
              <td class="date-cell">{{ formatDate(order.created_at) }}</td>
              <td>
                <div class="action-group">
                  <button
                    v-if="order.payment?.status !== 'verified' && order.payment_method !== 'cod'"
                    @click="verifyPayment(order.id)"
                    class="action-btn verify-btn"
                    title="Verify Payment"
                  >
                    💳 Verify
                  </button>
                  <span v-if="order.payment_status === 'paid'" class="paid-tag">✅ Paid</span>
                </div>
              </td>
            </tr>
            <tr v-if="filteredOrders.length === 0">
              <td colspan="9" class="no-data">No orders found</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="orders.last_page > 1" class="pagination">
        <Link
          v-for="page in orders.last_page"
          :key="page"
          :href="orders.path + '?page=' + page"
          :class="['page-btn', { active: page === orders.current_page }]"
        >
          {{ page }}
        </Link>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ orders: Object })

const search = ref('')
const statusFilter = ref('')
const paymentFilter = ref('')

const filteredOrders = computed(() => {
  let data = props.orders.data || []
  if (search.value) {
    const q = search.value.toLowerCase()
    data = data.filter(o =>
      o.order_number?.toLowerCase().includes(q) ||
      o.user?.name?.toLowerCase().includes(q)
    )
  }
  if (statusFilter.value) data = data.filter(o => o.status === statusFilter.value)
  if (paymentFilter.value) data = data.filter(o => o.payment_method === paymentFilter.value)
  return data
})

const miniStats = computed(() => {
  const data = props.orders.data || []
  return [
    { icon: '⏳', label: 'Pending', value: data.filter(o => o.status === 'pending').length, color: 'yellow' },
    { icon: '👨‍🍳', label: 'Preparing', value: data.filter(o => o.status === 'preparing').length, color: 'orange' },
    { icon: '🎉', label: 'Ready', value: data.filter(o => o.status === 'ready').length, color: 'teal' },
    { icon: '📦', label: 'Delivered', value: data.filter(o => o.status === 'delivered').length, color: 'green' },
  ]
})

const updateStatus = (orderId, status) => {
  router.patch(route('admin.orders.status', { order: orderId }), { status }, {
    preserveScroll: true
  })
}

const verifyPayment = (orderId) => {
  router.patch(route('admin.orders.verify-payment', { order: orderId }), {}, {
    preserveScroll: true
  })
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-PH', {
    month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
  })
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap');

* { box-sizing: border-box; }
.orders-mgmt { font-family: 'DM Sans', sans-serif; }

/* Filters */
.filters-bar {
  background: white;
  border-radius: 14px;
  padding: 1rem 1.25rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  flex-wrap: wrap;
}
.filters-left { flex: 1; }
.filters-right { display: flex; gap: 0.75rem; flex-wrap: wrap; }

.search-wrap { position: relative; max-width: 320px; }
.search-icon { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); }
.search-input {
  padding: 0.6rem 0.75rem 0.6rem 2.25rem;
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  font-size: 0.875rem;
  font-family: 'DM Sans', sans-serif;
  width: 100%;
}
.search-input:focus { outline: none; border-color: #10b981; }

.filter-select {
  padding: 0.6rem 0.75rem;
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  font-size: 0.8rem;
  font-family: 'DM Sans', sans-serif;
  background: white;
  color: #374151;
  cursor: pointer;
}
.filter-select:focus { outline: none; border-color: #10b981; }

/* Mini Stats */
.mini-stats {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.25rem;
  flex-wrap: wrap;
}
.mini-stat {
  background: white;
  border-radius: 12px;
  padding: 0.85rem 1.25rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  box-shadow: 0 2px 6px rgba(0,0,0,0.04);
  border-left: 4px solid transparent;
  flex: 1;
  min-width: 120px;
}
.mini-stat.yellow { border-left-color: #f59e0b; }
.mini-stat.orange { border-left-color: #f97316; }
.mini-stat.teal { border-left-color: #14b8a6; }
.mini-stat.green { border-left-color: #10b981; }
.mini-stat-icon { font-size: 1.5rem; }
.mini-stat-val { font-family: 'Syne', sans-serif; font-size: 1.4rem; font-weight: 800; color: #0f172a; line-height: 1; }
.mini-stat-label { font-size: 0.72rem; color: #9ca3af; font-weight: 600; text-transform: uppercase; }

/* Table */
.table-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  overflow: hidden;
  margin-bottom: 1.25rem;
}
.orders-table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
.orders-table th {
  padding: 0.85rem 1rem;
  text-align: left;
  font-size: 0.7rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #9ca3af;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
  white-space: nowrap;
}
.orders-table td {
  padding: 0.8rem 1rem;
  border-bottom: 1px solid #f3f4f6;
  color: #374151;
  vertical-align: middle;
}
.orders-table tr:last-child td { border-bottom: none; }
.orders-table tr:hover td { background: #fafafa; }

.mono { font-family: monospace; font-size: 0.72rem; color: #64748b; }
.cust-name { font-weight: 600; }
.cust-role { font-size: 0.68rem; text-transform: uppercase; color: #94a3b8; }
.store-cell { font-size: 0.8rem; color: #374151; max-width: 140px; }

.items-preview { display: flex; flex-direction: column; gap: 0.2rem; }
.item-preview-tag { font-size: 0.72rem; color: #6b7280; background: #f3f4f6; padding: 0.1rem 0.4rem; border-radius: 4px; display: inline-block; max-width: 160px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.item-more { font-size: 0.68rem; color: #10b981; font-weight: 700; }

.amount-cell { font-weight: 700; color: #064e3b; white-space: nowrap; }

.pay-info { display: flex; align-items: center; gap: 0.35rem; }
.pay-method { font-size: 0.7rem; font-weight: 700; padding: 0.15rem 0.4rem; border-radius: 4px; background: #f3f4f6; }
.pay-method.cod { background: #fef9c3; color: #854d0e; }
.pay-method.gcash { background: #dbeafe; color: #1e3a8a; }
.pay-method.paymaya { background: #fce7f3; color: #831843; }
.pay-status-dot { font-size: 0.9rem; }

.status-select {
  padding: 0.35rem 0.5rem;
  border-radius: 8px;
  border: 2px solid #e5e7eb;
  font-size: 0.78rem;
  font-family: 'DM Sans', sans-serif;
  cursor: pointer;
  font-weight: 600;
}
.status-select:focus { outline: none; }
.status-select.pending { border-color: #f59e0b; background: #fef9c3; color: #854d0e; }
.status-select.confirmed { border-color: #3b82f6; background: #dbeafe; color: #1e3a8a; }
.status-select.preparing { border-color: #f97316; background: #fed7aa; color: #7c2d12; }
.status-select.ready { border-color: #10b981; background: #d1fae5; color: #065f46; }
.status-select.delivered { border-color: #22c55e; background: #dcfce7; color: #14532d; }
.status-select.cancelled { border-color: #ef4444; background: #fee2e2; color: #991b1b; }

.date-cell { font-size: 0.75rem; color: #9ca3af; white-space: nowrap; }

.action-group { display: flex; gap: 0.35rem; align-items: center; }
.action-btn {
  padding: 0.35rem 0.65rem;
  border-radius: 8px;
  border: none;
  font-size: 0.75rem;
  font-weight: 700;
  cursor: pointer;
  font-family: 'DM Sans', sans-serif;
  transition: all 0.15s;
  white-space: nowrap;
}
.verify-btn { background: #dbeafe; color: #1e40af; }
.verify-btn:hover { background: #bfdbfe; }
.paid-tag { font-size: 0.75rem; color: #10b981; font-weight: 700; }

.no-data { text-align: center; color: #9ca3af; padding: 2rem; font-size: 0.875rem; }

/* Pagination */
.pagination { display: flex; gap: 0.35rem; justify-content: center; }
.page-btn {
  width: 34px; height: 34px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  background: white;
  color: #374151;
  font-size: 0.8rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.15s;
}
.page-btn.active { background: #10b981; border-color: #10b981; color: white; }
.page-btn:hover:not(.active) { border-color: #10b981; color: #10b981; }
</style>
