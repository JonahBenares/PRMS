<template>
  <navigation>
    <section class="items-center justify-center py-8">
      <div class="bg-white rounded-lg shadow-lg max-w-6xl mx-auto">
        <!-- Header -->
        <div class="px-6 py-4 flex justify-between items-center mb-4 bg-white border-b rounded-t-lg">
          <h2 class="text-lg font-semibold">Company List</h2>
          <button 
            @click="openAddModal" 
            class="flex text-sm items-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
          >
            <PlusIcon class="w-5 h-5 mr-1" /> Add Company
          </button>
        </div>

        <!-- Table -->
        <div class="px-6 pt-2 pb-6">
          <table 
            id="itemTable" 
            class="min-w-full text-sm text-left text-gray-700 border border-gray-200 rounded-lg overflow-hidden"
          >
            <thead class="bg-gray-100 text-gray-900 font-semibold">
              <tr>
                <td class="px-4 py-2">Logo</td>
                <td class="px-4 py-2">Company Name</td>
                <td class="px-4 py-2">Address 1</td>
                <td class="px-4 py-2">Address 2</td>
                <td class="px-4 py-2">Phone Number</td>
                <td class="px-4 py-2">Telefax</td>
                <td class="px-4 py-2" width="8%"></td>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="comp in companies" :key="comp.id" class="hover:bg-gray-50">
                <td class="px-4 py-2">
                  <img :src="comp.logo" alt="logo" class="w-12 h-12 object-contain rounded border"/>
                </td>
                <td class="px-4 py-2">{{ comp.name }}</td>
                <td class="px-4 py-2">{{ comp.address1 }}</td>
                <td class="px-4 py-2">{{ comp.address2 }}</td>
                <td class="px-4 py-2">{{ comp.phone }}</td>
                <td class="px-4 py-2">{{ comp.telefax }}</td>
                <td class="px-4 py-2 flex items-center space-x-1">
                  <button 
                    @click="openEditModal(comp)" 
                    class="flex items-center justify-center px-1 py-1 bg-blue-500 text-white rounded-lg hover:bg-blue-700"
                  >
                    <PencilSquareIcon class="w-4 h-4" />
                  </button>
                  <button 
                    @click="openDeleteModal(comp)" 
                    class="flex items-center justify-center px-1 py-1 bg-red-500 text-white rounded-lg hover:bg-red-700"
                  >
                    <TrashIcon class="w-4 h-4" />
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Add/Edit Modal -->
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="bg-black/50 w-full h-full" @click="closeModal"></div>
        <div class="bg-white rounded-lg w-[600px] absolute top-20">
          <div class="flex justify-between px-6 py-4 bg-gray-100 rounded-t-lg">
            <h3 class="text-lg font-semibold">
              {{ isEdit ? 'Edit Company' : 'Add Company' }}
            </h3>
            <button @click="closeModal">
              <XMarkIcon class="size-4"/>
            </button>
          </div>

          <div class="flex flex-col gap-3 px-6 py-4">
            <!-- Logo -->
            <div class="flex flex-col gap-1">
              <label class="text-sm">Logo</label>
              <input type="file" @change="handleFileUpload" class="border px-3 py-2 rounded text-sm"/>
              <img v-if="modalItem.logo" :src="modalItem.logo" class="w-20 h-20 mt-2 object-contain border rounded"/>
            </div>

            <!-- Company Name -->
            <div class="flex flex-col gap-1">
              <label class="text-sm">Company Name</label>
              <input v-model="modalItem.name" placeholder="Company Name" class="border px-3 py-2 rounded text-sm"/>
            </div>

            <!-- Address 1 -->
            <div class="flex flex-col gap-1">
              <label class="text-sm">Address 1</label>
              <input v-model="modalItem.address1" placeholder="Address Line 1" class="border px-3 py-2 rounded text-sm"/>
            </div>

            <!-- Address 2 -->
            <div class="flex flex-col gap-1">
              <label class="text-sm">Address 2</label>
              <input v-model="modalItem.address2" placeholder="Address Line 2" class="border px-3 py-2 rounded text-sm"/>
            </div>

            <!-- Phone -->
            <div class="flex flex-col gap-1">
              <label class="text-sm">Phone Number</label>
              <input v-model="modalItem.phone" placeholder="Phone Number" class="border px-3 py-2 rounded text-sm"/>
            </div>

            <!-- Telefax -->
            <div class="flex flex-col gap-1">
              <label class="text-sm">Telefax</label>
              <input v-model="modalItem.telefax" placeholder="Telefax" class="border px-3 py-2 rounded text-sm"/>
            </div>
          </div>

          <div class="flex justify-end gap-2 px-6 py-4">
            <button @click="closeModal" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-400">Cancel</button>
            <button @click="saveCompany" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
              {{ isEdit ? 'Update' : 'Add' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Delete Modal -->
      <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-2xl shadow-lg w-[600px] max-w-[90%] p-6 px-8 text-center absolute top-20">
          <div class="flex justify-center">
            <ExclamationTriangleIcon class="w-32 h-32 text-red-600"/>
          </div>
          <h3 class="!text-2xl font-bold text-gray-800 mb-6">Confirm Deletion</h3>
          <p class="text-gray-600">
            Are you sure you want to delete <span class="font-semibold">{{ modalItem.name }}</span>?
          </p>
          <div class="flex justify-center gap-3 mt-10">
            <button @click="closeDeleteModal" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Cancel</button>
            <button @click="deleteItem" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete</button>
          </div>
        </div>
      </div>
    </section>
  </navigation>
</template>

<script setup>
import { ref, reactive, onMounted } from "vue";
import $ from "jquery";
import "datatables.net";
import { TrashIcon, PlusIcon, ExclamationTriangleIcon, XMarkIcon, PencilSquareIcon } from '@heroicons/vue/24/solid';
import navigation from "@/components/layouts/navigation.vue";

// Table data
const companies = reactive([
  { id: 1, logo: "", name: "ABC Corp", address1: "123 Main St", address2: "Suite 101", phone: "123-4567", telefax: "123-4568" },
  { id: 2, logo: "", name: "XYZ Inc", address1: "456 High St", address2: "Floor 3", phone: "987-6543", telefax: "987-6544" },
]);

// Modals
const showModal = ref(false);
const showDeleteModal = ref(false);
const isEdit = ref(false);

const modalItem = reactive({ id: null, logo: "", name: "", address1: "", address2: "", phone: "", telefax: "" });

// File upload handler
const handleFileUpload = (e) => {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (event) => {
      modalItem.logo = event.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const openAddModal = () => {
  isEdit.value = false;
  Object.assign(modalItem, { id: null, logo: "", name: "", address1: "", address2: "", phone: "", telefax: "" });
  showModal.value = true;
};
const openEditModal = (item) => {
  isEdit.value = true;
  Object.assign(modalItem, item);
  showModal.value = true;
};
const closeModal = () => showModal.value = false;

const saveCompany = () => {
  if (isEdit.value) {
    const index = companies.findIndex(i => i.id === modalItem.id);
    if (index !== -1) companies[index] = { ...modalItem };
  } else {
    companies.push({ ...modalItem, id: Date.now() });
  }
  showModal.value = false;
};

const openDeleteModal = (item) => {
  Object.assign(modalItem, item);
  showDeleteModal.value = true;
};
const closeDeleteModal = () => showDeleteModal.value = false;
const deleteItem = () => {
  const index = companies.findIndex(i => i.id === modalItem.id);
  if (index !== -1) companies.splice(index, 1);
  showDeleteModal.value = false;
};

onMounted(() => {
  $('#itemTable').DataTable({
    dom: "<'flex justify-between items-center mb-4'lf>t<'flex justify-end items-center mt-4'p>",
    language: {
      search: "",
      searchPlaceholder: "Search companies...",
      lengthMenu: "_MENU_ entries per page",
    },
  });
});
</script>
