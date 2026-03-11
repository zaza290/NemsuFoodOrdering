<template>
  <AdminLayout title="Users Management">
    <div class="users-mgmt">
      <!-- Header -->
      <div class="mgmt-header">
        <div>
          <h2 class="mgmt-title">👥 Registered Users ({{ users.total }})</h2>
          <p class="mgmt-sub">Manage NEMSU students and faculty accounts</p>
        </div>
        <div class="header-stats">
          <div class="hs">
            <span class="hs-val">{{ studentCount }}</span>
            <span class="hs-label">Students</span>
          </div>
          <div class="hs-div"></div>
          <div class="hs">
            <span class="hs-val">{{ facultyCount }}</span>
            <span class="hs-label">Faculty</span>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="filters-bar">
        <div class="search-wrap">
          <span class="si">🔍</span>
          <input v-model="search" type="text" placeholder="Search by name or email..." class="search-input" />
        </div>
        <div class="filter-group">
          <select v-model="roleFilter" class="filter-select">
            <option value="">All Roles</option>
            <option value="student">🎒 Student</option>
            <option value="faculty">👨‍🏫 Faculty</option>
          </select>
          <select v-model="statusFilter" class="filter-select">
            <option value="">All Status</option>
            <option value="active">✅ Active</option>
            <option value="inactive">❌ Inactive</option>
          </select>
        </div>
      </div>

      <!-- Table -->
      <div class="table-card">
        <table class="users-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Student ID</th>
              <th>Department</th>
              <th>Phone</th>
              <th>Orders</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(user, i) in filteredUsers" :key="user.id">
              <td class="num-cell">{{ i + 1 }}</td>
              <td>
                <div class="user-cell">
                  <div class="user-avatar">{{ user.name[0] }}</div>
                  <div>
                    <div class="user-name">{{ user.name }}</div>
                    <div class="user-joined">Joined {{ formatDate(user.created_at) }}</div>
                  </div>
                </div>
              </td>
              <td class="email-cell">{{ user.email }}</td>
              <td>
                <span :class="['role-badge', user.role]">
                  {{ user.role === 'student' ? '🎒' : '👨‍🏫' }} {{ user.role }}
                </span>
              </td>
              <td class="mono-cell">{{ user.student_id || '—' }}</td>
              <td class="dept-cell">{{ user.department || '—' }}</td>
              <td class="phone-cell">{{ user.phone || '—' }}</td>
              <td class="orders-cell">
                <span class="order-count">{{ user.orders_count || 0 }}</span>
              </td>
              <td>
                <button
                  @click="toggleStatus(user)"
                  :class="['status-toggle', user.status]"
                >
                  {{ user.status === 'active' ? '✅ Active' : '❌ Inactive' }}
                </button>
              </td>
              <td>
                <button @click="confirmDelete(user)" class="delete-btn">🗑️</button>
              </td>
            </tr>
            <tr v-if="filteredUsers.length === 0">
              <td colspan="10" class="no-data">No users found</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="users.last_page > 1" class="pagination">
        <Link
          v-for="page in users.last_page"
          :key="page"
          :href="users.path + '?page=' + page"
          :class="['page-btn', { active: page === users.current_page }]"
        >
          {{ page }}
        </Link>
      </div>
    </div>

    <!-- Delete Confirm -->
    <div v-if="deleteTarget" class="modal-overlay" @click.self="deleteTarget = null">
      <div class="modal-card">
        <div class="delete-icon">⚠️</div>
        <h3 class="delete-title">Remove User?</h3>
        <p class="delete-desc">Remove <strong>{{ deleteTarget.name }}</strong> from the system?</p>
        <div class="delete-actions">
          <button @click="deleteTarget = null" class="cancel-btn">Cancel</button>
          <button @click="deleteUser" class="delete-confirm-btn">Yes, Remove</button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ users: Object })

const search = ref('')
const roleFilter = ref('')
const statusFilter = ref('')
const deleteTarget = ref(null)

const allUsers = computed(() => props.users.data || [])

const filteredUsers = computed(() => {
  let data = allUsers.value
  if (search.value) {
    const q = search.value.toLowerCase()
    data = data.filter(u => u.name.toLowerCase().includes(q) || u.email.toLowerCase().includes(q))
  }
  if (roleFilter.value) data = data.filter(u => u.role === roleFilter.value)
  if (statusFilter.value) data = data.filter(u => u.status === statusFilter.value)
  return data
})

const studentCount = computed(() => allUsers.value.filter(u => u.role === 'student').length)
const facultyCount = computed(() => allUsers.value.filter(u => u.role === 'faculty').length)

const toggleStatus = (user) => {
  const nextStatus = user.status === 'active' ? 'inactive' : 'active'
  router.patch(`/admin/users/${user.id}`, {
    status: nextStatus
  }, {
    preserveScroll: true,
    onSuccess: () => { user.status = nextStatus }
  })
}

const confirmDelete = (user) => { deleteTarget.value = user }

const deleteUser = () => {
  router.delete(`/admin/users/${deleteTarget.value.id}`, {
    onSuccess: () => { deleteTarget.value = null }
  })
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-PH', { month: 'short', year: 'numeric' })
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap');

* { box-sizing: border-box; }
.users-mgmt { font-family: 'DM Sans', sans-serif; }

.mgmt-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem; }
.mgmt-title { font-family: 'Syne', sans-serif; font-size: 1.25rem; font-weight: 800; color: #0f172a; }
.mgmt-sub { font-size: 0.8rem; color: #9ca3af; margin-top: 0.2rem; }

.header-stats { display: flex; align-items: center; gap: 1rem; background: white; padding: 0.75rem 1.25rem; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); }
.hs { text-align: center; }
.hs-val { display: block; font-family: 'Syne', sans-serif; font-size: 1.5rem; font-weight: 800; color: #10b981; line-height: 1; }
.hs-label { font-size: 0.72rem; color: #9ca3af; text-transform: uppercase; font-weight: 600; }
.hs-div { width: 1px; height: 36px; background: #e5e7eb; }

.filters-bar {
  background: white;
  border-radius: 12px;
  padding: 0.85rem 1.1rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  margin-bottom: 1rem;
  box-shadow: 0 2px 6px rgba(0,0,0,0.04);
  flex-wrap: wrap;
}
.search-wrap { position: relative; flex: 1; max-width: 320px; }
.si { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); font-size: 0.9rem; }
.search-input {
  padding: 0.6rem 0.75rem 0.6rem 2.1rem;
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  font-size: 0.875rem;
  font-family: 'DM Sans', sans-serif;
  width: 100%;
}
.search-input:focus { outline: none; border-color: #10b981; }
.filter-group { display: flex; gap: 0.5rem; }
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

.table-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  overflow-x: auto;
  margin-bottom: 1.25rem;
}
.users-table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
.users-table th {
  padding: 0.8rem 1rem;
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
.users-table td {
  padding: 0.8rem 1rem;
  border-bottom: 1px solid #f3f4f6;
  vertical-align: middle;
  color: #374151;
}
.users-table tr:last-child td { border-bottom: none; }
.users-table tr:hover td { background: #fafafa; }

.num-cell { color: #9ca3af; font-size: 0.75rem; }

.user-cell { display: flex; align-items: center; gap: 0.7rem; }
.user-avatar {
  width: 34px; height: 34px;
  background: linear-gradient(135deg, #10b981, #065f46);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 800;
  font-size: 0.875rem;
  flex-shrink: 0;
}
.user-name { font-weight: 600; color: #111827; }
.user-joined { font-size: 0.7rem; color: #9ca3af; }

.email-cell { font-size: 0.78rem; color: #6b7280; }
.mono-cell { font-family: monospace; font-size: 0.78rem; color: #6b7280; }
.dept-cell { font-size: 0.8rem; color: #374151; }
.phone-cell { font-size: 0.8rem; color: #6b7280; }

.role-badge {
  font-size: 0.72rem;
  font-weight: 700;
  padding: 0.2rem 0.55rem;
  border-radius: 999px;
  display: inline-block;
  text-transform: capitalize;
}
.role-badge.student { background: #dbeafe; color: #1e40af; }
.role-badge.faculty { background: #fce7f3; color: #831843; }

.orders-cell { text-align: center; }
.order-count { font-family: 'Syne', sans-serif; font-weight: 800; color: #10b981; }

.status-toggle {
  font-size: 0.72rem;
  font-weight: 700;
  padding: 0.25rem 0.6rem;
  border-radius: 999px;
  border: none;
  cursor: pointer;
  font-family: 'DM Sans', sans-serif;
  transition: all 0.15s;
}
.status-toggle.active { background: #d1fae5; color: #065f46; }
.status-toggle.inactive { background: #fee2e2; color: #991b1b; }

.delete-btn {
  background: #fee2e2;
  border: none;
  border-radius: 8px;
  padding: 0.35rem 0.55rem;
  cursor: pointer;
  font-size: 0.9rem;
  transition: background 0.15s;
}
.delete-btn:hover { background: #fecaca; }

.no-data { text-align: center; color: #9ca3af; padding: 2rem; font-size: 0.875rem; }

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
  padding: 2rem;
  width: 100%;
  max-width: 380px;
  text-align: center;
  box-shadow: 0 20px 60px rgba(0,0,0,0.2);
}
.delete-icon { font-size: 3rem; margin-bottom: 0.75rem; }
.delete-title { font-family: 'Syne', sans-serif; font-size: 1.2rem; font-weight: 800; color: #0f172a; margin-bottom: 0.5rem; }
.delete-desc { font-size: 0.875rem; color: #6b7280; margin-bottom: 1.5rem; }
.delete-desc strong { color: #111827; }
.delete-actions { display: flex; gap: 0.75rem; justify-content: center; }
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
.delete-confirm-btn {
  padding: 0.65rem 1.5rem;
  border-radius: 10px;
  border: none;
  background: #ef4444;
  color: white;
  font-weight: 700;
  font-family: 'DM Sans', sans-serif;
  cursor: pointer;
  transition: background 0.2s;
}
.delete-confirm-btn:hover { background: #dc2626; }
</style>
