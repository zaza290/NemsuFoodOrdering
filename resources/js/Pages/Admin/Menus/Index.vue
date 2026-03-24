<template>
  <AdminLayout :title="`${store.name} — Menus`">
    <div class="menu-mgmt">
      <!-- Header -->
      <div class="mgmt-header">
        <div class="header-left">
          <Link :href="route('admin.stores.index')" class="back-link">← Back to Stores</Link>
          <h2 class="mgmt-title">{{ store.name }}</h2>
          <p class="mgmt-sub">{{ menuItems.length }} menu items</p>
        </div>
        <button @click="openCreateModal" class="create-btn">+ Add Menu Item</button>
      </div>

      <!-- Menu Items Grid -->
      <div class="menu-grid">
        <div v-for="item in menuItems" :key="item.id" class="menu-card" :class="{ unavailable: !item.is_available }">
          <div class="menu-card-top">
            <div class="menu-item-emoji">🍴</div>
            <div class="menu-avail-toggle">
              <button
                @click="toggleAvailability(item)"
                :class="['avail-btn', item.is_available ? 'avail' : 'unavail']"
              >
                {{ item.is_available ? '✅ Available' : '❌ Unavailable' }}
              </button>
            </div>
          </div>
          <div class="menu-card-body">
            <h3 class="menu-item-name">{{ item.name }}</h3>
            <p v-if="item.description" class="menu-item-desc">{{ item.description }}</p>
            <div class="menu-item-price">₱{{ parseFloat(item.price).toFixed(2) }}</div>
            <div class="menu-item-meta">
              <div class="meta-row">
                <span class="meta-label">Stock</span>
                <span class="meta-value" :class="{ 'text-red-500 font-bold': item.current_stock === 0 }">
                  {{ item.current_stock ?? 0 }}
                  <span v-if="item.current_stock === 0"> (Sold Out)</span>
                </span>
              </div>
            </div>
            <div class="menu-card-actions">
              <button @click="openEditModal(item)" class="edit-btn">✏️ Edit</button>
              <button @click="confirmDelete(item)" class="del-btn">🗑️ Delete</button>
            </div>
          </div>
        </div>
      </div>

      <div v-if="menuItems.length === 0" class="empty-state">
        <div>🍽️</div>
        <p>No menu items yet. Add your first item!</p>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-card">
        <div class="modal-header">
          <h3>{{ editingItem ? '✏️ Edit Menu Item' : '➕ Add Menu Item' }}</h3>
          <button @click="closeModal" class="modal-close">✕</button>
        </div>
        <form @submit.prevent="saveItem" class="modal-form">
          <div class="form-group">
            <label>Item Name *</label>
            <input v-model="itemForm.name" type="text" placeholder="e.g. Pork Sisig" class="form-input" required />
            <span v-if="itemForm.errors.name" class="form-error">{{ itemForm.errors.name }}</span>
          </div>
          <div class="form-group">
            <label>Description</label>
            <textarea v-model="itemForm.description" rows="2" placeholder="Brief description..." class="form-input form-textarea"></textarea>
          </div>
          <div class="form-group">
            <label>Price (₱) *</label>
            <input v-model="itemForm.price" type="number" step="0.01" min="0" placeholder="0.00" class="form-input" required />
            <span v-if="itemForm.errors.price" class="form-error">{{ itemForm.errors.price }}</span>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Current Stock</label>
              <input v-model.number="itemForm.current_stock" type="number" min="0" placeholder="0" class="form-input" />
            </div>
            <div class="form-group">
              <label>Daily Target Stock</label>
              <input v-model.number="itemForm.daily_target_stock" type="number" min="1" placeholder="50" class="form-input" />
            </div>
          </div>
          <div class="form-group">
            <label>Availability</label>
            <select v-model="itemForm.is_available" class="form-input">
              <option :value="true">✅ Available</option>
              <option :value="false">❌ Unavailable</option>
            </select>
          </div>
          <div class="modal-footer">
            <button type="button" @click="closeModal" class="cancel-btn">Cancel</button>
            <button type="submit" :disabled="itemForm.processing" class="save-btn">
              <span v-if="itemForm.processing" class="spinner"></span>
              {{ itemForm.processing ? 'Saving...' : (editingItem ? 'Update Item' : 'Add Item') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Modal -->
    <div v-if="deleteTarget" class="modal-overlay" @click.self="deleteTarget = null">
      <div class="modal-card delete-modal">
        <div class="delete-icon">🗑️</div>
        <h3>Delete Item?</h3>
        <p>Delete <strong>{{ deleteTarget.name }}</strong>?</p>
        <div class="delete-actions">
          <button @click="deleteTarget = null" class="cancel-btn">Cancel</button>
          <button @click="deleteItem" class="delete-confirm-btn">Yes, Delete</button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ store: Object, menuItems: Array })

const showModal = ref(false)
const editingItem = ref(null)
const deleteTarget = ref(null)

const itemForm = useForm({
  store_id: props.store.id,
  name: '',
  description: '',
  price: '',
  current_stock: 0,
  daily_target_stock: 50,
  is_available: true,
})

const openCreateModal = () => {
  editingItem.value = null
  itemForm.reset()
  itemForm.store_id = props.store.id
  itemForm.is_available = true
  itemForm.current_stock = 0
  itemForm.daily_target_stock = 50
  showModal.value = true
}

const openEditModal = (item) => {
  editingItem.value = item
  itemForm.name = item.name
  itemForm.description = item.description || ''
  itemForm.price = item.price
  itemForm.current_stock = item.current_stock ?? 0
  itemForm.daily_target_stock = item.daily_target_stock ?? 50
  itemForm.is_available = item.is_available
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingItem.value = null
  itemForm.reset()
}

const saveItem = () => {
  if (editingItem.value) {
    itemForm.put(route('admin.stores.menus.update', { store: props.store.slug, menu: editingItem.value.id }), {
      onSuccess: () => closeModal()
    })
  } else {
    itemForm.post(route('admin.stores.menus.store', props.store.slug), {
      onSuccess: () => closeModal()
    })
  }
}

const confirmDelete = (item) => { deleteTarget.value = item }

const deleteItem = () => {
  router.delete(route('admin.stores.menus.destroy', { store: props.store.slug, menu: deleteTarget.value.id }), {
    onSuccess: () => { deleteTarget.value = null }
  })
}

const toggleAvailability = (item) => {
  router.patch(route('admin.stores.menus.update', { store: props.store.slug, menu: item.id }), {
    is_available: !item.is_available
  }, { preserveScroll: true })
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap');

* { box-sizing: border-box; }
.menu-mgmt { font-family: 'DM Sans', sans-serif; }

.mgmt-header { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
.header-left { }
.back-link { color: #10b981; text-decoration: none; font-size: 0.825rem; font-weight: 600; display: block; margin-bottom: 0.4rem; }
.back-link:hover { color: #065f46; }
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

.menu-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 1rem;
}

.menu-card {
  background: white;
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  border: 1px solid #f3f4f6;
  transition: box-shadow 0.2s;
}
.menu-card:hover { box-shadow: 0 6px 18px rgba(0,0,0,0.08); }
.menu-card.unavailable { opacity: 0.65; }

.menu-card-top {
  height: 80px;
  background: linear-gradient(135deg, #f0fdf4, #d1fae5);
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}
.menu-item-emoji { font-size: 2.5rem; }
.menu-avail-toggle { position: absolute; top: 0.5rem; right: 0.5rem; }
.avail-btn {
  font-size: 0.65rem;
  font-weight: 700;
  padding: 0.15rem 0.4rem;
  border-radius: 999px;
  border: none;
  cursor: pointer;
  font-family: 'DM Sans', sans-serif;
}
.avail-btn.avail { background: rgba(16,185,129,0.15); color: #065f46; }
.avail-btn.unavail { background: rgba(239,68,68,0.15); color: #991b1b; }

.menu-card-body { padding: 0.85rem 1rem; }
.menu-item-name { font-weight: 700; font-size: 0.9rem; color: #111827; margin-bottom: 0.2rem; }
.menu-item-desc { font-size: 0.75rem; color: #9ca3af; margin-bottom: 0.4rem; }
.menu-item-price { font-weight: 800; color: #10b981; font-size: 1.1rem; margin-bottom: 0.75rem; }
.menu-item-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 0.35rem 0.75rem; margin-bottom: 0.6rem; }
.meta-row { display: flex; gap: 0.35rem; align-items: center; font-size: 0.78rem; color: #6b7280; }
.meta-label { font-weight: 700; color: #374151; }
.meta-value { font-weight: 700; color: #111827; }

.menu-card-actions { display: flex; gap: 0.4rem; }
.edit-btn, .del-btn {
  flex: 1;
  padding: 0.4rem 0;
  border-radius: 8px;
  font-size: 0.75rem;
  font-weight: 700;
  border: none;
  cursor: pointer;
  font-family: 'DM Sans', sans-serif;
  transition: all 0.15s;
}
.edit-btn { background: #fef9c3; color: #854d0e; }
.edit-btn:hover { background: #fef08a; }
.del-btn { background: #fee2e2; color: #991b1b; }
.del-btn:hover { background: #fecaca; }

.empty-state { text-align: center; padding: 4rem; color: #9ca3af; font-size: 1rem; }
.empty-state div { font-size: 3rem; margin-bottom: 0.75rem; }

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
  max-width: 440px;
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
.modal-close { background: rgba(255,255,255,0.2); border: none; border-radius: 50%; width: 28px; height: 28px; color: white; cursor: pointer; }

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
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }

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
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.save-btn:disabled { opacity: 0.6; cursor: not-allowed; }

.delete-modal { padding: 2rem; text-align: center; }
.delete-icon { font-size: 3rem; margin-bottom: 0.75rem; }
.delete-modal h3 { font-family: 'Syne', sans-serif; font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 0.4rem; }
.delete-modal p { font-size: 0.875rem; color: #6b7280; margin-bottom: 1.25rem; }
.delete-modal strong { color: #111827; }
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
}
.delete-confirm-btn:hover { background: #dc2626; }

.spinner { width: 14px; height: 14px; border: 2px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
</style>
