<template>
  <AdminLayout title="Stores Management">
    <div class="stores-mgmt">
      <!-- Header -->
      <div class="mgmt-header">
        <div>
          <h2 class="mgmt-title">🏪 All Stores ({{ stores.length }})</h2>
          <p class="mgmt-sub">Manage all NEMSU Chow Zone stores and their menus</p>
        </div>
        <button @click="openCreateModal" class="create-btn">+ Add Store</button>
      </div>

      <!-- Stores Grid - same store image as customer Stores page -->
      <div class="stores-grid">
        <div v-for="store in stores" :key="store.id" class="store-card">
          <div class="card-top">
            <img :src="storeImageSrc(store)" :alt="store.name" class="card-store-img" loading="lazy" @error="onStoreImgError" />
            <div class="card-status">
              <button
                @click="toggleStoreStatus(store)"
                :class="['toggle-btn', store.is_open ? 'open' : 'closed']"
              >
                {{ store.is_open ? '● Open' : '● Closed' }}
              </button>
            </div>
          </div>
          <div class="card-body">
            <h3 class="card-name">{{ store.name }}</h3>
            <div class="card-meta">
              <span>{{ (store.menu_items || []).length }} menu items</span>
              <span>·</span>
              <span>{{ store.orders_count || 0 }} orders</span>
            </div>
            <div class="card-rating">
              <span class="star-val">⭐ {{ parseFloat(store.ratings_avg_rating || 0).toFixed(1) }}</span>
            </div>
            <div class="card-actions">
              <Link :href="route('admin.stores.menus.index', store.slug)" class="card-action-btn menu-btn">
                📋 Menus
              </Link>
              <button @click="openEditModal(store)" class="card-action-btn edit-btn">✏️ Edit</button>
              <button @click="confirmDelete(store)" class="card-action-btn delete-btn">🗑️</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty -->
      <div v-if="stores.length === 0" class="empty-state">
        <div class="empty-icon">🏪</div>
        <p>No stores yet. Add your first store!</p>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-card">
        <div class="modal-header">
          <h3>{{ editingStore ? '✏️ Edit Store' : '➕ Add New Store' }}</h3>
          <button @click="closeModal" class="modal-close">✕</button>
        </div>
        <form @submit.prevent="saveStore" class="modal-form">
          <div class="form-group">
            <label>Store Name *</label>
            <input v-model="storeForm.name" type="text" placeholder="e.g. SISIG REPUBLIC" class="form-input" required />
            <span v-if="storeForm.errors.name" class="form-error">{{ storeForm.errors.name }}</span>
          </div>
          <div class="form-group">
            <label>Description</label>
            <textarea v-model="storeForm.description" rows="2" placeholder="Brief description..." class="form-input form-textarea"></textarea>
          </div>
          <div class="form-group">
            <label>Status</label>
            <select v-model="storeForm.is_open" class="form-input">
              <option :value="true">🟢 Open</option>
              <option :value="false">🔴 Closed</option>
            </select>
          </div>
          <div class="modal-footer">
            <button type="button" @click="closeModal" class="cancel-btn">Cancel</button>
            <button type="submit" :disabled="storeForm.processing" class="save-btn">
              <span v-if="storeForm.processing" class="spinner"></span>
              {{ storeForm.processing ? 'Saving...' : (editingStore ? 'Update Store' : 'Create Store') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Confirm Modal -->
    <div v-if="deleteTarget" class="modal-overlay" @click.self="deleteTarget = null">
      <div class="modal-card delete-modal">
        <div class="delete-icon">🗑️</div>
        <h3 class="delete-title">Delete Store?</h3>
        <p class="delete-desc">Are you sure you want to delete <strong>{{ deleteTarget.name }}</strong>? This cannot be undone.</p>
        <div class="delete-actions">
          <button @click="deleteTarget = null" class="cancel-btn">Cancel</button>
          <button @click="deleteStore" class="delete-confirm-btn">Yes, Delete</button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ stores: Array })

const showModal = ref(false)
const editingStore = ref(null)
const deleteTarget = ref(null)

const storeForm = useForm({
  name: '',
  description: '',
  is_open: true,
})

const openCreateModal = () => {
  editingStore.value = null
  storeForm.reset()
  storeForm.is_open = true
  showModal.value = true
}

const openEditModal = (store) => {
  editingStore.value = store
  storeForm.name = store.name
  storeForm.description = store.description || ''
  storeForm.is_open = store.is_open
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingStore.value = null
  storeForm.reset()
}

const saveStore = () => {
  if (editingStore.value) {
    storeForm.put(route('admin.stores.update', { store: editingStore.value.slug }), {
      onSuccess: () => closeModal()
    })
  } else {
    storeForm.post(route('admin.stores.store'), {
      onSuccess: () => closeModal()
    })
  }
}

const confirmDelete = (store) => {
  deleteTarget.value = store
}

const deleteStore = () => {
  router.delete(route('admin.stores.destroy', { store: deleteTarget.value.slug }), {
    onSuccess: () => { deleteTarget.value = null }
  })
}

const toggleStoreStatus = (store) => {
  router.patch(route('admin.stores.update', { store: store.slug }), {
    name: store.name,
    is_open: !store.is_open,
  }, { preserveScroll: true })
}

/** Same store image as customer Stores page & Admin Dashboard */
const DEFAULT_STORE_IMAGE = 'https://images.unsplash.com/photo-1555392886-6f5ac0d4c58b?w=400&h=240&fit=crop&q=80'

function storeImageSrc(store) {
  if (!store) return DEFAULT_STORE_IMAGE
  if (store.image) {
    if (String(store.image).startsWith('http')) return store.image
    return '/storage/' + String(store.image).replace(/^[\\/]+/, '')
  }
  return DEFAULT_STORE_IMAGE
}

function onStoreImgError(e) {
  e.target.src = DEFAULT_STORE_IMAGE
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap');

* { box-sizing: border-box; }
.stores-mgmt { font-family: 'DM Sans', sans-serif; }

.mgmt-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; gap: 1rem; flex-wrap: wrap; }
.mgmt-title { font-family: 'Syne', sans-serif; font-size: 1.25rem; font-weight: 800; color: #0f172a; }
.mgmt-sub { font-size: 0.8rem; color: #9ca3af; margin-top: 0.2rem; }
.create-btn {
  background: linear-gradient(135deg, #10b981, #065f46);
  color: white;
  border: none;
  border-radius: 10px;
  padding: 0.7rem 1.25rem;
  font-weight: 700;
  font-size: 0.875rem;
  font-family: 'DM Sans', sans-serif;
  cursor: pointer;
  white-space: nowrap;
  transition: all 0.2s;
}
.create-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(16,185,129,0.4); }

.stores-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1.25rem;
}

.store-card {
  background: white;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 2px 10px rgba(0,0,0,0.06);
  border: 1px solid #f3f4f6;
  transition: box-shadow 0.2s;
}
.store-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,0.1); }

.card-top {
  height: 120px;
  position: relative;
  overflow: hidden;
  background: #e2e8f0;
}
.card-store-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}
.card-status { position: absolute; top: 0.6rem; right: 0.6rem; }
.toggle-btn {
  font-size: 0.68rem;
  font-weight: 700;
  padding: 0.2rem 0.55rem;
  border-radius: 999px;
  border: none;
  cursor: pointer;
  font-family: 'DM Sans', sans-serif;
  transition: all 0.15s;
}
.toggle-btn.open { background: rgba(16,185,129,0.85); color: white; }
.toggle-btn.closed { background: rgba(239,68,68,0.85); color: white; }

.card-body { padding: 1rem 1.1rem; }
.card-name { font-family: 'Syne', sans-serif; font-size: 0.9rem; font-weight: 800; color: #0f172a; margin-bottom: 0.4rem; line-height: 1.3; }
.card-meta { font-size: 0.75rem; color: #9ca3af; margin-bottom: 0.3rem; display: flex; gap: 0.4rem; }
.card-rating { margin-bottom: 0.85rem; }
.star-val { font-size: 0.8rem; font-weight: 700; color: #92400e; }

.card-actions { display: flex; gap: 0.4rem; flex-wrap: wrap; }
.card-action-btn {
  padding: 0.4rem 0.65rem;
  border-radius: 8px;
  font-size: 0.75rem;
  font-weight: 700;
  border: none;
  cursor: pointer;
  font-family: 'DM Sans', sans-serif;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  transition: all 0.15s;
}
.menu-btn { background: #f0fdf4; color: #065f46; }
.menu-btn:hover { background: #dcfce7; }
.edit-btn { background: #fef9c3; color: #854d0e; }
.edit-btn:hover { background: #fef08a; }
.delete-btn { background: #fee2e2; color: #991b1b; }
.delete-btn:hover { background: #fecaca; }

.empty-state { text-align: center; padding: 4rem; color: #9ca3af; }
.empty-icon { font-size: 3rem; margin-bottom: 0.75rem; }

/* Modal */
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
  max-width: 460px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.2);
  overflow: hidden;
}
.modal-header {
  background: linear-gradient(135deg, #064e3b, #10b981);
  padding: 1.25rem 1.5rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.modal-header h3 { color: white; font-family: 'Syne', sans-serif; font-weight: 800; }
.modal-close { background: rgba(255,255,255,0.2); border: none; border-radius: 50%; width: 28px; height: 28px; color: white; cursor: pointer; font-size: 0.85rem; }

.modal-form { padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem; }
.form-group { display: flex; flex-direction: column; gap: 0.35rem; }
.form-group label { font-size: 0.825rem; font-weight: 600; color: #374151; }
.form-input {
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  padding: 0.65rem 0.85rem;
  font-size: 0.875rem;
  font-family: 'DM Sans', sans-serif;
  color: #111827;
  transition: border-color 0.2s;
  width: 100%;
}
.form-input:focus { outline: none; border-color: #10b981; }
.form-textarea { resize: none; }
.form-error { color: #ef4444; font-size: 0.75rem; }

.modal-footer { display: flex; gap: 0.75rem; justify-content: flex-end; }
.cancel-btn {
  padding: 0.65rem 1.25rem;
  border-radius: 10px;
  border: 2px solid #e5e7eb;
  background: white;
  color: #6b7280;
  font-weight: 700;
  font-family: 'DM Sans', sans-serif;
  cursor: pointer;
  font-size: 0.875rem;
}
.save-btn {
  padding: 0.65rem 1.5rem;
  border-radius: 10px;
  border: none;
  background: linear-gradient(135deg, #10b981, #065f46);
  color: white;
  font-weight: 700;
  font-family: 'DM Sans', sans-serif;
  cursor: pointer;
  font-size: 0.875rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.save-btn:disabled { opacity: 0.6; cursor: not-allowed; }

/* Delete Modal */
.delete-modal { padding: 2rem 2rem 1.5rem; text-align: center; }
.delete-icon { font-size: 3rem; margin-bottom: 0.75rem; }
.delete-title { font-family: 'Syne', sans-serif; font-size: 1.2rem; font-weight: 800; color: #0f172a; margin-bottom: 0.5rem; }
.delete-desc { font-size: 0.875rem; color: #6b7280; margin-bottom: 1.5rem; }
.delete-desc strong { color: #111827; }
.delete-actions { display: flex; gap: 0.75rem; justify-content: center; }
.delete-confirm-btn {
  padding: 0.65rem 1.5rem;
  border-radius: 10px;
  border: none;
  background: #ef4444;
  color: white;
  font-weight: 700;
  font-family: 'DM Sans', sans-serif;
  cursor: pointer;
  font-size: 0.875rem;
  transition: background 0.2s;
}
.delete-confirm-btn:hover { background: #dc2626; }

.spinner { width: 14px; height: 14px; border: 2px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
</style>
