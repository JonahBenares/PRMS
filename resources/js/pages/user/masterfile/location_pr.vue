<script setup>
	import { ref, onMounted, reactive } from "vue";
	import { Bars3Icon, PlusIcon, ExclamationTriangleIcon, XMarkIcon, PencilSquareIcon } from '@heroicons/vue/24/solid';
	import { useTable } from '@/composables/useTable'
	import searchbox from '@/composables/searchbox.vue';
	import pagination from "@/composables/pagination.vue";
	import navigation from "@/components/layouts/navigation.vue";
	import modal from "@/components/modal.vue";
	import axios from "axios";

	const {
		items,
		search,
		page,
		perPage,
		total,
		lastPage,
		loading,
		fetchData
	} = useTable('/api/pr-locations')

	const isSaving = ref(false)

	onMounted(fetchData)

	// modal state
	const showModal = ref(false)
	const showDeleteModal = ref(false)
	const isEdit = ref(false)

	const modalItem = reactive({
		id: null,
		location: ""
	})

	const errors = reactive({
		location: ""
	})

	// open add
	const openAddModal = () => {
		isEdit.value = false
		Object.assign(modalItem, { id: null, location: "" })
		Object.assign(errors, { location: "" })
		showModal.value = true
	}

	// open edit
	const openEditModal = (item) => {
		isEdit.value = true
		Object.assign(modalItem, item)
		Object.assign(errors, { location: "" })
		showModal.value = true
	}

	const saveLocation = async () => {
		if (isSaving.value) return

		errors.location = ""

		if (!modalItem.location) {
			errors.location = "Location is required"
			return
		}

		try {
			isSaving.value = true

			if (isEdit.value) {
				await axios.put(`/api/pr-locations/${modalItem.id}`, modalItem)
			} else {
				await axios.post("/api/pr-locations", modalItem)
			}

			await fetchData()
			showModal.value = false

		} catch (err) {
			if (err.response?.data?.errors) {
				Object.assign(errors, err.response.data.errors)
			}
			console.error(err)
		} finally {
			isSaving.value = false
		}
	}

	// delete
	const openDeleteModal = (item) => {
		Object.assign(modalItem, item)
		showDeleteModal.value = true
	}

	const deleteItem = async () => {
		await axios.delete(`/api/pr-locations/${modalItem.id}`)
		await fetchData()
		showDeleteModal.value = false
	}
</script>

<template>
	<navigation>
		<section class="items-center justify-center py-8 fade-up">

			<div class="bg-white  rounded-2xl shadow-xl max-w-6xl mx-auto">

			<!-- HEADER -->
			<div class="px-6 py-5 flex flex-col sm:flex-row sm:justify-between gap-4">
				<h2 class="text-2xl font-bold text-gray-600">PR Locations</h2>
				<div class="flex gap-3 w-full sm:w-auto">
					<div class="w-full sm:w-96">
						<searchbox v-model="search"/>
					</div>
					<button
					@click="openAddModal"
					class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700"
					>
					<PlusIcon class="w-4 h-4"/>
					Add Location
					</button>
				</div>
			</div>

			<div class="border-b">
				<pagination
				:page="page"
				:per-page="perPage"
				:last-page="lastPage"
				:total="total"
				@update:page="page = $event"
				@update:perPage="perPage = $event"
				/>
			</div>

			<!-- TABLE -->
			<table class="min-w-full text-sm">
				<thead class="bg-gray-100 text-xs uppercase text-gray-600">
					<tr>
						<th class="px-6 py-3 text-left">Location</th>
						<th class="px-6 py-3 w-1">
							<div class="flex justify-center">
								<Bars3Icon class="size-4"/>
							</div>
						</th>
					</tr>
				</thead>

				<tbody class="divide-y">
					<tr v-for="loc in items" :key="loc.id" class="">
						<td class="px-6 py-2">{{ loc.location }}</td>
						<td class="px-6 py-2 text-center">
							<button @click="openEditModal(loc)" class="p-2 text-blue-600 hover:bg-blue-50 rounded">
								<PencilSquareIcon class="w-4 h-4"/>
							</button>
						</td>
					</tr>

					<tr v-if="!loading && items.length === 0">
						<td colspan="2" class="py-10 text-center text-gray-500">
						No locations found.
						</td>
					</tr>
				</tbody>
			</table>

			<pagination
			:page="page"
			:per-page="perPage"
			:last-page="lastPage"
			:total="total"
			@update:page="page = $event"
			@update:perPage="perPage = $event"
			/>

			</div>
		</section>

		<!-- MODAL -->
		<modal v-model="showModal">
			<template #title>
				{{ isEdit ? 'Edit Location' : 'Add Location' }}
			</template>

			<template #close-icon>
				<XMarkIcon class="w-5 h-5 cursor-pointer" @click="showModal=false"/>
			</template>

			<div>
				<label class="text-sm font-medium">Location</label>
				<input v-model="modalItem.location" class="mt-1 w-full border rounded-lg px-3 py-2 text-sm"/>
				<span v-if="errors.location" class="text-red-500 text-xs">{{ errors.location }}</span>
			</div>

			<template #footer>
				<button @click="showModal=false" class="px-4 py-2 text-sm bg-gray-100 rounded-lg">
					Cancel
				</button>

				<button
					@click="saveLocation"
					:disabled="isSaving"
					class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg"
					>
					{{ isSaving ? 'Saving...' : isEdit ? 'Update' : 'Add' }}
				</button>
			</template>
		</modal>
	</navigation>
</template>


