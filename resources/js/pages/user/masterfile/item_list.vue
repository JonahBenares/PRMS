<template>
    <navigation>
		<section class="items-center justify-center py-8">
			<div class="bg-white rounded-lg shadow-lg max-w-6xl mx-auto">
				<!-- Header -->
				<div class="px-6 py-4 flex justify-between items-center mb-4 bg-white border-b rounded-t-lg">
					<h2 class="text-lg font-semibold">Item List</h2>
					<a
						href="/masterfile/items/add"
						class="flex text-sm items-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
						>
						<PlusIcon class="w-5 h-5 mr-1" />
						Add Item
					</a>
				</div>

				<!-- Table -->
				<div class="px-6 pt-2 pb-6">
					<table
					id="itemTable"
					class="min-w-full text-sm text-left text-gray-700 border border-gray-200 rounded-lg overflow-hidden"
					>
					<thead class="bg-gray-100 text-gray-900 font-semibold">
						<tr>
						<td class="px-4 py-2 cursor-pointer">Item Code</td>
						<td class="px-4 py-2 cursor-pointer">Item Name</td>
						<td class="px-4 py-2 cursor-pointer">Category</td>
						<td class="px-4 py-2 cursor-pointer" width="3%"></td>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200">
						<tr
						v-for="item in items"
						:key="item.id"
						class="hover:bg-gray-50"
						>
						<td class="px-4 py-2 align-top w-5">{{ item.code }}</td>
						<td class="px-4 py-2 align-top">{{ item.name }}</td>
						<td class="px-4 py-2 align-top">{{ item.category }}</td>
						<td class="px-4 py-2 align-top flex items-center space-x-1">
							<a href="/masterfile/items/edit"
								class="flex items-center justify-center px-1 py-1 bg-blue-500 text-white rounded-lg hover:bg-blue-700"
								>
								<PencilSquareIcon class="w-4 h-4" />
							</a>
							<button
							@click="openDeleteModal(item)"
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
				<div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
				<div class="bg-white rounded-lg w-96 p-6">
					<h3 class="text-lg font-semibold mb-4">{{ isEdit ? 'Edit Item' : 'Add Item' }}</h3>
					<div class="flex flex-col gap-3">
					<input v-model="modalItem.code" placeholder="Item Code" class="border px-3 py-2 rounded"/>
					<input v-model="modalItem.name" placeholder="Item Name" class="border px-3 py-2 rounded"/>
					<input v-model="modalItem.category" placeholder="Category" class="border px-3 py-2 rounded"/>
					</div>
					<div class="flex justify-end gap-2 mt-4">
					<button @click="closeModal" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
					<button @click="saveItem" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">{{ isEdit ? 'Update' : 'Add' }}</button>
					</div>
				</div>
				</div>

				<!-- Delete Warning Modal -->
				<div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
				<div class="bg-white rounded-lg w-80 p-6">
					<h3 class="text-lg font-semibold mb-4">Confirm Deletion</h3>
					<p>Are you sure you want to delete <strong>{{ modalItem.name }}</strong>?</p>
					<div class="flex justify-end gap-2 mt-4">
					<button @click="closeDeleteModal" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
					<button @click="deleteItem" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
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
import { TrashIcon, PencilSquareIcon, PlusIcon } from '@heroicons/vue/24/solid';
import navigation from "@/components/layouts/navigation.vue";

// Table data
const items = reactive([
  { id: 1, code: "234", name: "Sample item - PRT03399; Category;Brand;Model;Size;Color;Material;Unit;Serial", category: "5675" },
  { id: 2, code: "234", name: "Sample item - PRT03399; Category;Brand;Model;Size;Color;Material;Unit;Serial", category: "5675" },
  { id: 3, code: "234", name: "Sample item - PRT03399; Category;Brand;Model;Size;Color;Material;Unit;Serial", category: "5675" },
]);

// Modals
const showModal = ref(false);
const showDeleteModal = ref(false);
const isEdit = ref(false);
const modalItem = reactive({ id: null, code: "", name: "", category: "" });

const openAddModal = () => {
  isEdit.value = false;
  Object.assign(modalItem, { id: null, code: "", name: "", category: "" });
  showModal.value = true;
};

const openEditModal = (item) => {
  isEdit.value = true;
  Object.assign(modalItem, item);
  showModal.value = true;
};

const closeModal = () => showModal.value = false;

const saveItem = () => {
  if (isEdit.value) {
    const index = items.findIndex(i => i.id === modalItem.id);
    if (index !== -1) items[index] = { ...modalItem };
  } else {
    items.push({ ...modalItem, id: Date.now() });
  }
  showModal.value = false;
};

const openDeleteModal = (item) => {
  Object.assign(modalItem, item);
  showDeleteModal.value = true;
};

const closeDeleteModal = () => showDeleteModal.value = false;

const deleteItem = () => {
  const index = items.findIndex(i => i.id === modalItem.id);
  if (index !== -1) items.splice(index, 1);
  showDeleteModal.value = false;
};

onMounted(() => {
  $('#itemTable').DataTable({
    dom: "<'flex justify-between items-center mb-4'lf>t<'flex justify-end items-center mt-4'p>",
    language: {
      search: "",
      searchPlaceholder: "Search items...",
      lengthMenu: "_MENU_ entries per page",
    },
  });
});
</script>
