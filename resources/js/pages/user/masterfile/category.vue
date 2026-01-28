<script setup>
	import { onMounted, computed, ref } from 'vue'
	import { useTable } from '@/composables/useTable'
	import searchbox from '@/composables/searchbox.vue';
	import pagination from "@/composables/pagination.vue";
	import navigation from "@/components/layouts/navigation.vue";
	import { PencilSquareIcon, PlusIcon, XMarkIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/solid'
	import axios from 'axios'

	// -------------------
	// TABLE DATA & PAGINATION
	// -------------------
	const {
		items,
		search,
		page,
		perPage,
		total,
		lastPage,
		loading,
		fetchData
	} = useTable('/api/categories')

	onMounted(fetchData)
	const pageCount = computed(() => lastPage.value)
	const totalItems = computed(() => total.value)

	// Flatten categories + subcategories for table rows
	const tableRows = computed(() => {
		const rows = []
		items.value.forEach(category => {
			rows.push({
			id: `c-${category.id}`,
			name: category.category_name,
			type: 'category',
			raw: category
			})
			category.subcategories?.forEach(sub => {
			rows.push({
				id: `s-${sub.id}`,
				name: `↳ ${sub.sub_cat_name}`,
				type: 'subcategory',
				raw: sub,
				parent: category
			})
			})
		})
		return rows
	})

	
	const visiblePages = computed(() => {
		const total = pageCount.value
		const current = page.value
		const delta = 2 // pages on each side

		if (total <= 7) {
			return Array.from({ length: total }, (_, i) => i + 1)
		}

		const pages = new Set()

		pages.add(1)
		pages.add(total)

		for (let i = current - delta; i <= current + delta; i++) {
			if (i > 1 && i < total) {
			pages.add(i)
			}
		}

		return Array.from(pages).sort((a, b) => a - b)
	})

	// -------------------
	// MODAL STATE
	// -------------------
	const showModal = ref(false)
	const isEdit = ref(false)
	const isSub = ref(false)
	const parentCategory = ref(null)

	const modalItem = ref({
		id: null,
		name: ''
	})

	const showDeleteModal = ref(false)

	// -------------------
	// MODAL ACTIONS
	// -------------------

	// Open Add Modal (Category or Subcategory)
	const openAddModal = (parent = null) => {
		isEdit.value = false
		isSub.value = !!parent
		parentCategory.value = parent

		modalItem.value = { id: null, name: '' }

		showModal.value = true
	}

	// Open Edit Modal (Category or Subcategory)
	const openEditModal = (item, sub = false) => {
		isEdit.value = true
		isSub.value = sub

		modalItem.value = {
			id: item.id,
			name: sub ? item.sub_cat_name : item.category_name
		}

		parentCategory.value = sub
			? items.value.find(c => c.subcategories?.some(s => s.id === item.id)) || null
			: null

		showModal.value = true
	}

	// Save (Add / Update)
	const saveItem = async () => {
		const name = modalItem.value.name?.trim()
		if (!name) {
			console.warn('Name is empty')
			return
		}

		try {
			let response

			if (isEdit.value) {
			// UPDATE
			if (isSub.value) {
				response = await axios.put(`/api/update_subcategory/${modalItem.value.id}`, {
				sub_cat_name: name
				})
			} else {
				response = await axios.put(`/api/update_category/${modalItem.value.id}`, {
				category_name: name
				})
			}
			} else {
			// CREATE
			if (isSub.value) {
				if (!parentCategory.value?.id) {
				console.error('Parent category missing ID', parentCategory.value)
				return
				}

				response = await axios.post('/api/new_subcategory', {
				sub_cat_name: name,
				category_id: parentCategory.value.id
				})
			} else {
				response = await axios.post('/api/new_category', {
				category_name: name
				})
			}
			}

			console.log('Saved:', response.data)
			showModal.value = false
			await fetchData() // refresh the table
		} catch (err) {
			if (err.response) {
			console.error('Server Error:', err.response.data)
			} else {
			console.error(err)
			}
		}
	}


	// Delete Modal
	const openDeleteModal = (item, sub = false, parent = null) => {
		isSub.value = sub
		parentCategory.value = parent
		modalItem.value = { ...item }
		showDeleteModal.value = true
	}

	const deleteItem = async () => {
		try {
			if (isSub.value) {
			await axios.delete(`/api/delete_subcategory/${modalItem.value.id}`)
			} else {
			await axios.delete(`/api/delete_category/${modalItem.value.id}`)
			}

			showDeleteModal.value = false
			await fetchData()
		} catch (err) {
			console.error(err)
		}
	}

	// Close modals
	const closeModal = () => showModal.value = false
	const closeDeleteModal = () => showDeleteModal.value = false

	// Table button helpers
	const openEdit = row => openEditModal(row.raw, row.type === 'subcategory')
	const openAddSub = (category) => {
		isEdit.value = false
		isSub.value = true

		parentCategory.value = {
			id: category.id,
			category_name: category.category_name
		}

		modalItem.id = null
		modalItem.name = ""

		showModal.value = true
	}
</script>

<template>
	<navigation>
		<section class="max-w-6xl mx-auto bg-white rounded-lg shadow">

			<!-- HEADER -->
			<div class="flex justify-between items-center px-6 py-4 border-b">
				<h2 class="font-semibold text-lg">Categories</h2>
				<button @click="openAddModal()" class="flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
				<PlusIcon class="w-4 h-4 mr-1" />
				Add Category
				</button>
			</div>

			<!-- SEARCH -->
			<searchbox v-model="search" />

			<!-- TABLE -->
			<div class="overflow-x-auto">
				<table class="w-full text-sm">
					<thead class="bg-gray-100 font-semibold">
						<tr>
						<td class="px-4 py-2">Name</td>
						<td class="px-4 py-2 w-32">Actions</td>
						</tr>
					</thead>

					<tbody>
						<!-- <tr v-if="loading">
							<td colspan="2" class="text-center py-6 text-gray-500">Loading...</td>
						</tr> -->

						<template v-for="category in items" :key="category.id">
							<!-- CATEGORY ROW -->
							<tr class="border-t font-semibold">
								<td class="px-4 py-2">
									{{ category.category_name }}
								</td>
								<td class="px-4 py-2">
									<div class="flex gap-1">
									<button @click="openEditModal(category)" class="p-1 bg-blue-500 text-white rounded">
										<PencilSquareIcon class="w-4 h-4"/>
									</button>
									<button @click="openAddSub(category)" class="p-1 bg-green-500 text-white rounded">
										<PlusIcon class="w-4 h-4"></PlusIcon>
									</button>
									</div>
								</td>
							</tr>

							<!-- SUBCATEGORIES -->
							<tr
								v-for="sub in category.subcategories"
								:key="sub.id"
								class="border-t text-gray-600"
								>
								<td class="pl-8 py-2">
									↳ {{ sub.sub_cat_name }}
								</td>
								<td class="px-4 py-2">
									<button
									@click="openEditModal(sub, true)"
									class="p-1 bg-blue-400 text-white rounded"
									>
									<PencilSquareIcon class="w-4 h-4"/>
									</button>
								</td>
							</tr>
						</template>
					</tbody>
				</table>
			</div>

			<!-- PAGINATION -->
			<pagination
				:page="page"
				:per-page="perPage"
				:last-page="lastPage"
				:total="total"
				@update:page="page = $event"
				@update:perPage="perPage = $event"
			/>

			<!-- ADD / EDIT MODAL -->
			<div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center">
				<div class="bg-black/50 w-full h-full" @click="closeModal"></div>
				<div class="bg-white rounded-lg w-[600px] absolute top-32">
				<div class="flex justify-between px-6 py-4 bg-gray-100 rounded-t-lg">
					<h3 class="text-lg font-semibold">
					{{ isEdit ? 'Edit ' + (isSub ? 'Subcategory' : 'Category') : 'Add ' + (isSub ? 'Subcategory' : 'Category') }}
					</h3>
					<button @click="closeModal"><XMarkIcon class="w-5 h-5"/></button>
				</div>
				<div class="flex flex-col gap-3 px-6 py-4">
					<label class="text-sm">{{ isSub ? 'Subcategory' : 'Category' }} Name</label>
					<input v-model="modalItem.name" placeholder="Enter name" class="border px-3 py-2 rounded text-sm"/>
				</div>
				<div class="flex justify-end gap-2 px-6 py-4">
					<button @click="closeModal" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-400">Cancel</button>
					<button @click="saveItem" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">{{ isEdit ? 'Update' : 'Add' }}</button>
				</div>
				</div>
			</div>

			<!-- DELETE MODAL -->
			<div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
				<div class="bg-white rounded-2xl shadow-lg w-[600px] max-w-[90%] p-6 px-8 text-center">
				<ExclamationTriangleIcon class="w-20 h-20 text-red-600 mx-auto"/>
				<h3 class="text-xl font-bold text-gray-800 mb-4">Confirm Deletion</h3>
				<p class="text-gray-600">Are you sure you want to delete <b>{{ modalItem.name }}</b>?</p>
				<div class="flex justify-center gap-3 mt-6">
					<button @click="closeDeleteModal" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-400">Cancel</button>
					<button @click="deleteItem" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
				</div>
				</div>
			</div>

		</section>
	</navigation>
</template>
