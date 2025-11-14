<template>
	<navigation>
		<section class="items-center justify-center py-8">
			<div class="bg-white rounded-lg shadow-lg max-w-6xl mx-auto">
				<!-- Header -->
				<div class="px-6 py-4 flex justify-between items-center mb-4 bg-white border-b rounded-t-lg">
				<h2 class="text-lg font-semibold">Category List</h2>
				<button 
					@click="openAddModal()"
					class="flex text-sm items-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
				>
					<PlusIcon class="w-5 h-5 mr-1" />
					Add Category
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
						<td class="px-4 py-2">Category / Subcategory</td>
						<td class="px-4 py-2" width="10%"></td>
					</tr>
					</thead>
					<tbody class="divide-y divide-gray-200">
					<!-- Categories -->
					<template v-for="category in categories" :key="category.id">
						<tr class="bg-gray-50">
						<td class="px-4 py-2 font-semibold">{{ category.name }}</td>
						<td class="px-4 py-2 flex space-x-1">
							<button 
							@click="openEditModal(category, false)"
							class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-700"
							>
							<PencilSquareIcon class="w-4 h-4"/>
							</button>
							<button
							@click="openDeleteModal(category, false)"
							class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-700"
							>
							<TrashIcon class="w-4 h-4"/>
							</button>
							<button
							@click="openAddModal(category)"
							class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-700"
							>
							+ 
							</button>
						</td>
						</tr>

						<!-- Subcategories (nested inside each category) -->
						<tr
						v-for="subcategory in category.subcategories"
						:key="subcategory.id"
						>
						<td class="px-8 py-2 text-gray-600">↳ {{ subcategory.name }}</td>
						<td class="px-4 py-2 flex space-x-1">
							<button 
							@click="openEditModal(subcategory, true)"
							class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-700"
							>
							<PencilSquareIcon class="w-4 h-4"/>
							</button>
							<button
							@click="openDeleteModal(subcategory, true, category)"
							class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-700"
							>
							<TrashIcon class="w-4 h-4"/>
							</button>
						</td>
						</tr>
					</template>
					</tbody>

				</table>
				</div>
			</div>

			<!-- Add/Edit Modal -->
			<div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center">
				<div class="bg-black/50 w-full h-full" @click="closeModal"></div>
				<div class="bg-white rounded-lg w-[600px] absolute top-32">
				<div class="flex justify-between px-6 py-4 bg-gray-100 rounded-t-lg">
					<h3 class="text-lg font-semibold">
					{{ isEdit ? 'Edit ' + (isSub ? 'Subcategory' : 'Category') : 'Add ' + (isSub ? 'Subcategory' : 'Category') }}
					</h3>
					<button @click="closeModal">
					<XMarkIcon class="size-4"></XMarkIcon>
					</button>
				</div>
				<div class="flex flex-col gap-3 px-6 py-4">
					<label class="text-sm">
					{{ isSub ? 'Subcategory' : 'Category' }} Name
					</label>
					<input v-model="modalItem.name" placeholder="Enter name" class="border px-3 py-2 rounded text-sm"/>
				</div>
				<div class="flex justify-end gap-2 px-6 py-4">
					<button @click="closeModal" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-400">Cancel</button>
					<button @click="saveItem" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">{{ isEdit ? 'Update' : 'Add' }}</button>
				</div>
				</div>
			</div>

			<!-- Delete Modal -->
			<div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
				<div class="bg-white rounded-2xl shadow-lg w-[600px] max-w-[90%] p-6 px-8 text-center">
				<ExclamationTriangleIcon class="w-20 h-20 text-red-600 mx-auto"/>
				<h3 class="text-xl font-bold text-gray-800 mb-4">Confirm Deletion</h3>
				<p class="text-gray-600">
					Are you sure you want to delete <b>{{ modalItem.name }}</b>?
				</p>
				<div class="flex justify-center gap-3 mt-6">
					<button @click="closeDeleteModal" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-400">Cancel</button>
					<button @click="deleteItem" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
				</div>
				</div>
			</div>
		</section>
	</navigation>
</template>

<script setup>
import { ref, reactive } from "vue";
import { TrashIcon, PlusIcon, ExclamationTriangleIcon, XMarkIcon, PencilSquareIcon } from '@heroicons/vue/24/solid';
import navigation from "@/components/layouts/navigation.vue";

// Data
const categories = reactive([
  { id: 1, name: "Inventory", subcategories: [{ id: 11, name: "Spare Parts" }, { id: 12, name: "Consumables" }] },
  { id: 2, name: "Technology", subcategories: [{ id: 21, name: "Software" }, { id: 22, name: "Hardware" }] },
]);

// Modal states
const showModal = ref(false);
const showDeleteModal = ref(false);
const isEdit = ref(false);
const isSub = ref(false);
const parentCategory = ref(null);
const modalItem = reactive({ id: null, name: "" });

const openAddModal = (parent = null) => {
  isEdit.value = false;
  isSub.value = !!parent;
  parentCategory.value = parent;
  Object.assign(modalItem, { id: null, name: "" });
  showModal.value = true;
};

const openEditModal = (item, sub = false) => {
  isEdit.value = true;
  isSub.value = sub;
  Object.assign(modalItem, { ...item });
  if (sub) parentCategory.value = categories.find(c => c.subcategories.some(s => s.id === item.id));
  showModal.value = true;
};

const saveItem = () => {
  if (isEdit.value) {
    if (isSub.value) {
      const subIndex = parentCategory.value.subcategories.findIndex(s => s.id === modalItem.id);
      parentCategory.value.subcategories[subIndex] = { ...modalItem };
    } else {
      const catIndex = categories.findIndex(c => c.id === modalItem.id);
      categories[catIndex] = { ...categories[catIndex], name: modalItem.name };
    }
  } else {
    if (isSub.value) {
      parentCategory.value.subcategories.push({ id: Date.now(), name: modalItem.name });
    } else {
      categories.push({ id: Date.now(), name: modalItem.name, subcategories: [] });
    }
  }
  showModal.value = false;
};

const openDeleteModal = (item, sub = false, parent = null) => {
  isSub.value = sub;
  parentCategory.value = parent;
  Object.assign(modalItem, { ...item });
  showDeleteModal.value = true;
};

const deleteItem = () => {
  if (isSub.value) {
    const subIndex = parentCategory.value.subcategories.findIndex(s => s.id === modalItem.id);
    if (subIndex !== -1) parentCategory.value.subcategories.splice(subIndex, 1);
  } else {
    const index = categories.findIndex(c => c.id === modalItem.id);
    if (index !== -1) categories.splice(index, 1);
  }
  showDeleteModal.value = false;
};

const closeModal = () => showModal.value = false;
const closeDeleteModal = () => showDeleteModal.value = false;
</script>
