<template>
	<navigation>
		<section class="">
			<div class="bg-white shadow rounded-xl p-6">
				<!-- Header -->
				<div class="flex justify-between items-center mb-4">
					<h2 class="text-xl font-semibold">Item List</h2>
					<button
						class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg shadow hover:bg-blue-700"
					>
						<PlusIcon class="h-5 w-5" /> Add Item
					</button>
				</div>

				<!-- Search -->
				<div class="mb-4">
					<input
						v-model="search"
						type="text"
						placeholder="Search items..."
						class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-blue-300"
					/>
				</div>

				<!-- Table -->
				<div class="overflow-x-auto">
					<table class="min-w-full text-sm border rounded-lg">
						<thead class="bg-gray-100">
							<tr>
								<th class="px-4 py-2 text-left cursor-pointer" @click="sort('code')">Item Code</th>
								<th class="px-4 py-2 text-left cursor-pointer" @click="sort('name')">Item Name</th>
								<th class="px-4 py-2 text-left cursor-pointer" @click="sort('category')">Category</th>
								<th class="px-4 py-2 text-left">Actions</th>
							</tr>
						</thead>
						<tbody>
							<tr
								v-for="item in paginatedItems"
								:key="item.id"
								class="border-t hover:bg-gray-50"
							>
								<td class="px-4 py-2">{{ item.code }}</td>
								<td class="px-4 py-2">{{ item.name }}</td>
								<td class="px-4 py-2">{{ item.category }}</td>
								<td class="px-4 py-2">
									<button class="text-blue-600 hover:underline text-sm">Edit</button>
									<button class="text-red-600 hover:underline text-sm ml-3">Delete</button>
								</td>
							</tr>
							<tr v-if="filteredItems.length === 0">
								<td colspan="4" class="px-4 py-6 text-center text-gray-500">No items found</td>
							</tr>
						</tbody>
					</table>
				</div>

				<!-- Pagination -->
				<div class="flex justify-between items-center mt-4">
					<p class="text-sm text-gray-600">
						Showing {{ startIndex + 1 }}–{{ endIndex }} of {{ filteredItems.length }}
					</p>
					<div class="flex gap-2">
						<button
							:disabled="currentPage === 1"
							@click="currentPage--"
							class="px-3 py-1 border rounded-lg text-sm disabled:opacity-50"
						>
							Prev
						</button>
						<button
							:disabled="currentPage === totalPages"
							@click="currentPage++"
							class="px-3 py-1 border rounded-lg text-sm disabled:opacity-50"
						>
							Next
						</button>
					</div>
				</div>
			</div>
		</section>
	</navigation>
</template>

<script setup>
import { ref, computed } from "vue";
import { PlusIcon } from "@heroicons/vue/24/solid";
import navigation from "@/components/layouts/navigation_admin.vue";

// Sample Data
const items = ref([
	{ id: 1, code: "ITM001", name: "Laptop", category: "Electronics" },
	{ id: 2, code: "ITM002", name: "Office Chair", category: "Furniture" },
	{ id: 3, code: "ITM003", name: "Notebook", category: "Stationery" },
	{ id: 4, code: "ITM004", name: "Smartphone", category: "Electronics" },
	{ id: 5, code: "ITM005", name: "Desk", category: "Furniture" },
]);

// Search
const search = ref("");

// Sorting
const sortKey = ref("");
const sortAsc = ref(true);
const sort = (key) => {
	if (sortKey.value === key) {
		sortAsc.value = !sortAsc.value;
	} else {
		sortKey.value = key;
		sortAsc.value = true;
	}
};

// Filter + Sort
const filteredItems = computed(() => {
	let result = items.value.filter(
		(item) =>
			item.name.toLowerCase().includes(search.value.toLowerCase()) ||
			item.code.toLowerCase().includes(search.value.toLowerCase()) ||
			item.category.toLowerCase().includes(search.value.toLowerCase())
	);

	if (sortKey.value) {
		result = result.sort((a, b) => {
			const valA = a[sortKey.value].toString().toLowerCase();
			const valB = b[sortKey.value].toString().toLowerCase();
			if (valA < valB) return sortAsc.value ? -1 : 1;
			if (valA > valB) return sortAsc.value ? 1 : -1;
			return 0;
		});
	}

	return result;
});

// Pagination
const currentPage = ref(1);
const perPage = 3;
const totalPages = computed(() => Math.ceil(filteredItems.value.length / perPage));

const paginatedItems = computed(() => {
	const start = (currentPage.value - 1) * perPage;
	const end = start + perPage;
	return filteredItems.value.slice(start, end);
});

const startIndex = computed(() => (currentPage.value - 1) * perPage);
const endIndex = computed(() => Math.min(startIndex.value + perPage, filteredItems.value.length));
</script>
