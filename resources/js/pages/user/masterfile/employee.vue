<template>
    <navigation>
		<section class="items-center justify-center py-8">
			<div class="bg-white rounded-lg shadow-lg max-w-6xl mx-auto">
				<!-- Header -->
				<div class="px-6 py-4 flex justify-between items-center mb-4 bg-white border-b rounded-t-lg">
					<h2 class="text-lg font-semibold">Employee List</h2>
					<button 
                        @click="openAddModal(item)"
						class="flex text-sm items-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
						>
						<PlusIcon class="w-5 h-5 mr-1" />
						Add Employee
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
                                <td class="px-4 py-2 cursor-pointer">Employee Name</td>
                                <td class="px-4 py-2 cursor-pointer">Position</td>
                                <td class="px-4 py-2 cursor-pointer">Department</td>
                                <td class="px-4 py-2 cursor-pointer" width="5%"></td>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr
                                v-for="employee in employees"
                                :key="employee.id"
                                class="hover:bg-gray-50"
                            >
                                <td class="px-4 py-2 align-top">{{ employee.name }}</td>
                                <td class="px-4 py-2 align-top">{{ employee.position }}</td>
                                <td class="px-4 py-2 align-top">{{ employee.department }}</td>
                                <td class="px-4 py-2 align-top flex items-center space-x-1">
                                    <button 
                                        @click="openEditModal(employee)"
                                        class="flex items-center justify-center px-1 py-1 bg-blue-500 text-white rounded-lg hover:bg-blue-700"
                                    >
                                        <PencilSquareIcon class="w-4 h-4" />
                                    </button>
                                    <button
                                        @click="openDeleteModal(employee)"
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
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center ">
                <div class="bg-black/50 w-full h-full"  @click="closeModal"></div>
                <div class="bg-white rounded-lg w-[600px] absolute top-32">
                    <div class="flex justify-between px-6 py-4 bg-gray-100 rounded-t-lg">
                        <h3 class="text-lg font-semibold">{{ isEdit ? 'Edit Employee' : 'Add Employee' }}</h3>
                        <button @click="closeModal">
                            <XMarkIcon class="size-4"></XMarkIcon>
                        </button>
                    </div>
                    
                    <div class="flex flex-col gap-3 px-6 py-4">
                        <div class="flex flex-col gap-1">
                            <label class="text-sm">Name</label>
                            <input v-model="modalItem.name" placeholder="Employee Name" class="border px-3 py-2 rounded text-sm"/>
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-sm">Position</label>
                            <input v-model="modalItem.position" placeholder="Position" class="border px-3 py-2 rounded text-sm"/>
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-sm">Department</label>
                            <input v-model="modalItem.department" placeholder="Department" class="border px-3 py-2 rounded text-sm"/>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 px-6 py-4">
                        <button @click="closeModal" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-400">Cancel</button>
                        <button @click="saveEmployee" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">{{ isEdit ? 'Update' : 'Add' }}</button>
                    </div>
                </div>
            </div>

            <!-- Delete Warning Modal -->
            <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                <div class="bg-white rounded-2xl shadow-lg w-[600px] max-w-[90%] p-6 px-8 text-center max-h-[90vh] overflow-y-auto absolute top-32">
                    <!-- Warning Icon -->
                    <div class="flex justify-center">
                        <ExclamationTriangleIcon class="w-32 h-32 text-red-600" />
                    </div>

                    <!-- Title -->
                    <h3 class="!text-2xl font-bold text-gray-800 mb-6">
                        Confirm Deletion
                    </h3>

                    <!-- Message -->
                    <p class="text-gray-600 break-words">
                        Are you sure you want to delete 
                        <span class="text-gray-600 break-words font-semibold whitespace-normal mb-4 max-w-xs">
                            {{ modalItem.name }}
                        </span>
                    ?
                    </p>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-wrap justify-center gap-3 mt-10">
                        <button  @click="closeDeleteModal"  class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition" >
                            Cancel
                        </button>

                        <button  @click="deleteItem" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            Delete
                        </button>
                    </div>
                </div>
            </div>

		</section>
    </navigation>
</template>

<script setup>
import { ref, onMounted, reactive } from "vue";
import $ from "jquery";
import "datatables.net";
import { TrashIcon, PlusIcon, ExclamationTriangleIcon, XMarkIcon, PencilSquareIcon } from '@heroicons/vue/24/solid';
import navigation from "@/components/layouts/navigation.vue";

// Table data
const employees = reactive([
  { id: 1, name: "John Doe", position: "Software Engineer", department: "IT" },
  { id: 2, name: "Jane Smith", position: "HR Manager", department: "Human Resources" },
  { id: 3, name: "Michael Brown", position: "Accountant", department: "Finance" },
]);

// Modals
const showModal = ref(false);
const showDeleteModal = ref(false);
const isEdit = ref(false);
const modalItem = reactive({ id: null, name: "", position: "", department: "" });

const openAddModal = () => {
  isEdit.value = false;
  Object.assign(modalItem, { id: null, name: "", position: "", department: "" });
  showModal.value = true;
};

const openEditModal = (item) => {
  isEdit.value = true;
  Object.assign(modalItem, item);
  showModal.value = true;
};

const closeModal = () => showModal.value = false;

const saveEmployee = () => {
  if (isEdit.value) {
    const index = employees.findIndex(i => i.id === modalItem.id);
    if (index !== -1) employees[index] = { ...modalItem };
  } else {
    employees.push({ ...modalItem, id: Date.now() });
  }
  showModal.value = false;
};

const openDeleteModal = (item) => {
  Object.assign(modalItem, item);
  showDeleteModal.value = true;
};

const closeDeleteModal = () => showDeleteModal.value = false;

const deleteItem = () => {
  const index = employees.findIndex(i => i.id === modalItem.id);
  if (index !== -1) employees.splice(index, 1);
  showDeleteModal.value = false;
};

onMounted(() => {
  $('#itemTable').DataTable({
    dom: "<'flex justify-between items-center mb-4'lf>t<'flex justify-end items-center mt-4'p>",
    language: {
      search: "",
      searchPlaceholder: "Search employees...",
      lengthMenu: "_MENU_ entries per page",
    },
  });
});
</script>
