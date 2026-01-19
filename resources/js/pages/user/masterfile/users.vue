<template>
  <navigation>
    <section class="items-center justify-center py-8">
      <div class="bg-white rounded-lg shadow-lg max-w-6xl mx-auto">
        <!-- Header -->
        <div class="px-6 py-4 flex justify-between items-center mb-4 bg-white border-b rounded-t-lg">
          <h2 class="text-lg font-semibold">User List</h2>
          <button 
            @click="openAddModal"
            class="flex text-sm items-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
          >
            <PlusIcon class="w-5 h-5 mr-1" />
            Add User
          </button>
        </div>

        <!-- Table -->
        <div class="px-6 pt-2 pb-6">
          <table class="min-w-full text-sm text-left text-gray-700 border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-gray-100 text-gray-900 font-semibold">
              <tr>
                <td class="px-4 py-2">Name</td>
                <td class="px-4 py-2">Email</td>
                <td class="px-4 py-2">Role</td>
                <td class="px-4 py-2" width="5%"></td>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50">
                <td class="px-4 py-2">{{ user.name }}</td>
                <td class="px-4 py-2">{{ user.email }}</td>
                <td class="px-4 py-2">{{ user.role || '-' }}</td>
                <td class="px-4 py-2 flex items-center space-x-1">
                  <button @click="openEditModal(user)" class="flex items-center justify-center px-1 py-1 bg-blue-500 text-white rounded-lg hover:bg-blue-700">
                    <PencilSquareIcon class="w-4 h-4" />
                  </button>
                  <button @click="openDeleteModal(user)" class="flex items-center justify-center px-1 py-1 bg-red-500 text-white rounded-lg hover:bg-red-700">
                    <TrashIcon class="w-4 h-4" />
                  </button>
                </td>
              </tr>
              <tr v-if="users.length === 0">
                <td colspan="4" class="text-center py-4 text-gray-500">No users found.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Add/Edit Modal -->
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="bg-black/50 w-full h-full" @click="closeModal"></div>
        <div class="bg-white rounded-lg w-[600px] absolute top-32">
          <div class="flex justify-between px-6 py-4 bg-gray-100 rounded-t-lg">
            <h3 class="text-lg font-semibold">{{ isEdit ? 'Edit User' : 'Add User' }}</h3>
            <button @click="closeModal">
              <XMarkIcon class="w-5 h-5"/>
            </button>
          </div>

          <div class="flex flex-col gap-3 px-6 py-4">
            <div class="flex flex-col gap-1">
              <label class="text-sm">Name</label>
              <input v-model="modalItem.name" placeholder="Full Name" class="border px-3 py-2 rounded text-sm"/>
              <span v-if="errors.name" class="text-red-500 text-xs">{{ errors.name }}</span>
            </div>
            <div class="flex flex-col gap-1">
              <label class="text-sm">Email</label>
              <input v-model="modalItem.email" placeholder="Email Address" class="border px-3 py-2 rounded text-sm"/>
              <span v-if="errors.email" class="text-red-500 text-xs">{{ errors.email }}</span>
            </div>
            <div class="flex flex-col gap-1">
              <label class="text-sm">Role</label>
              <select v-model="modalItem.role" class="border px-3 py-2 rounded text-sm">
                <option value="" disabled>Select Role</option>
                <option value="Admin">Admin</option>
                <option value="Enduser">Enduser</option>
                <option value="Supervisor">Supervisor</option>
              </select>
              <span v-if="errors.role" class="text-red-500 text-xs">{{ errors.role }}</span>
            </div>
          </div>

          <div class="flex justify-end gap-2 px-6 py-4">
            <button @click="closeModal" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-400">Cancel</button>
            <button @click="saveUser" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">{{ isEdit ? 'Update' : 'Add' }}</button>
          </div>
        </div>
      </div>

      <!-- Delete Modal -->
      <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-2xl shadow-lg w-[600px] max-w-[90%] p-6 text-center absolute top-32">
          <ExclamationTriangleIcon class="w-32 h-32 text-red-600 mx-auto mb-4" />
          <h3 class="!text-2xl font-bold text-gray-800 mb-4">Confirm Deletion</h3>
          <p class="text-gray-600 mb-6">
            Are you sure you want to delete <span class="font-semibold">{{ modalItem.name }}</span>?
          </p>
          <div class="flex justify-center gap-3">
            <button @click="closeDeleteModal" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancel</button>
            <button @click="deleteItem" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
          </div>
        </div>
      </div>
    </section>
  </navigation>
</template>

<script setup>
import { ref, reactive, onMounted } from "vue";
import axios from "axios";
import { TrashIcon, PlusIcon, ExclamationTriangleIcon, XMarkIcon, PencilSquareIcon } from '@heroicons/vue/24/solid';
import navigation from "@/components/layouts/navigation.vue";

// Users data
const users = ref([]);

// Modal states
const showModal = ref(false);
const showDeleteModal = ref(false);
const isEdit = ref(false);
const modalItem = reactive({ id: null, name: "", email: "", role: "" });
const errors = reactive({ name: "", email: "", role: "" });

// Fetch users from API
const fetchUsers = async () => {
  try {
    const res = await axios.get("/api/users");
    users.value = res.data;
  } catch (err) {
    console.error(err);
  }
};

// Add/Edit modal
const openAddModal = () => {
  isEdit.value = false;
  Object.assign(modalItem, { id: null, name: "", email: "", role: "" });
  Object.assign(errors, { name: "", email: "", role: "" });
  showModal.value = true;
};

const openEditModal = (user) => {
  isEdit.value = true;
  Object.assign(modalItem, user);
  Object.assign(errors, { name: "", email: "", role: "" });
  showModal.value = true;
};

const closeModal = () => showModal.value = false;

// Save user
const saveUser = async () => {
  Object.assign(errors, { name: "", email: "", role: "" });
  try {
    if (isEdit.value) {
      await axios.put(`/api/users/${modalItem.id}`, modalItem);
    } else {
      await axios.post("/api/users", modalItem);
    }
    await fetchUsers();
    showModal.value = false;
  } catch (err) {
    if (err.response?.data?.errors) {
      Object.assign(errors, err.response.data.errors);
    }
    console.error(err);
  }
};

// Delete modal
const openDeleteModal = (user) => {
  Object.assign(modalItem, user);
  showDeleteModal.value = true;
};

const closeDeleteModal = () => showDeleteModal.value = false;

const deleteItem = async () => {
  try {
    await axios.delete(`/api/users/${modalItem.id}`);
    await fetchUsers();
    showDeleteModal.value = false;
  } catch (err) {
    console.error(err);
  }
};

// Initialize
onMounted(() => {
  fetchUsers();
});
</script>