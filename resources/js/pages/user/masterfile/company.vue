<script setup>
	import { ref, reactive, onMounted } from 'vue'
	import axios from 'axios'

	import { useTable } from '@/composables/useTable'
	import searchbox from '@/composables/searchbox.vue'
	import pagination from "@/composables/pagination.vue"
	import navigation from "@/components/layouts/navigation.vue"
	import modal from "@/components/modal.vue"

	import { Bars3Icon, PencilSquareIcon, PlusIcon, XMarkIcon } from '@heroicons/vue/24/solid'

	// -------------------
	// TABLE
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
	} = useTable('/api/companies')

	onMounted(fetchData)

	// -------------------
	// MODAL STATE
	// -------------------
	const showModal = ref(false)
	const isSaving = ref(false)
	const isEdit = ref(false)
	const isSub = ref(false)

	const parentCompany = ref(null)

	const logoPreview = ref(null)

	const modalItem = reactive({
		id: null,
		company_name: '',
		company_code: '',
		company_logo: null,
		location: '',
		address: '',
		telephone: '',
		telefax: ''
	})

	const errors = reactive({
		company_name: '',
		company_code: '',
		location: ''
	})

	// -------------------
	// LOGO UPLOAD
	// -------------------
	const handleLogoUpload = (e) => {
		const file = e.target.files[0]
		if (!file) return

		modalItem.company_logo = file
		logoPreview.value = URL.createObjectURL(file)
	}

	// -------------------
	// MODAL ACTIONS
	// -------------------
	const openAddModal = (parent = null) => {
		isEdit.value = false
		isSub.value = !!parent
		parentCompany.value = parent

		Object.assign(modalItem, {
			id: null,
			company_name: '',
			company_code: '',
			company_logo: null,
			location: '',
			address: '',
			telephone: '',
			telefax: ''
		})

		logoPreview.value = null
		clearErrors()
		showModal.value = true
	}

	const openEditModal = (item, sub = false, parent = null) => {
		isEdit.value = true
		isSub.value = sub
		parentCompany.value = parent

		Object.assign(modalItem, { ...item })

		if (!sub && item.company_logo) {
			logoPreview.value = `/storage/logos/${item.company_logo}`
		} else {
			logoPreview.value = null
		}

		clearErrors()
		showModal.value = true
	}

	function clearErrors() {
		errors.company_name = ''
		errors.company_code = ''
		errors.location = ''
	}

	// -------------------
	// SAVE
	// -------------------
	const saveItem = async () => {
		if (isSaving.value) return
		clearErrors()

		// validation
		if (!isSub.value) {
			if (!modalItem.company_name) {
				errors.company_name = "Company name is required"
				return
			}
		} else {
			if (!modalItem.location) {
				errors.location = "Location is required"
				return
			}
		}

		try {
			isSaving.value = true

			if (isSub.value) {
				// LOCATION
				const payload = {
					company_id: parentCompany.value.id,
					location: modalItem.location,
					address: modalItem.address,
					telephone: modalItem.telephone,
					telefax: modalItem.telefax
				}

				if (isEdit.value) {
					await axios.put(`/api/company_locations/${modalItem.id}`, payload)
				} else {
					await axios.post(`/api/company_locations`, payload)
				}

			} else {
				// COMPANY
				const formData = new FormData()
				formData.append("company_name", modalItem.company_name)
				formData.append("company_code", modalItem.company_code)

				if (modalItem.company_logo instanceof File) {
					formData.append("company_logo", modalItem.company_logo)
				}

				const url = isEdit.value
					? `/api/companies/${modalItem.id}?_method=PUT`
					: `/api/companies`

				await axios.post(url, formData, {
					headers: { "Content-Type": "multipart/form-data" }
				})
			}

			await fetchData()
			showModal.value = false

		} catch (err) {
			console.error(err.response?.data || err)
		} finally {
			isSaving.value = false
		}
	}
</script>

<template>
	<navigation>
		<section class="items-center justify-center py-8 fade-up">

			<div class="bg-white  rounded-2xl shadow-xl max-w-6xl mx-auto">

				<!-- HEADER -->
				<div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
					<div>
						<h2 class="text-2xl font-bold text-gray-700">Companies</h2>
					</div>

					<div class="flex gap-3 w-full sm:w-auto">

						<div class="w-full sm:w-96">
							<searchbox v-model="search" />
						</div>

						<button
							@click="openAddModal()"
							class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
							<PlusIcon class="w-4 h-4"/>
							Add Company
						</button>

					</div>
				</div>

				<!-- TOP PAGINATION -->
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
				<div class="overflow-hidden">
					<table class="min-w-full text-sm text-left">
						<thead class="bg-gray-100 text-xs uppercase text-gray-600">
							<tr>
								<th class="px-6 py-3">Company</th>
								<th class="px-6 py-3">Code</th>
								<th class="px-6 py-3">Logo</th>
								<th class="px-6 py-3 w-1 text-center">
								<Bars3Icon class="size-4"/>
								</th>
							</tr>
						</thead>

						<tbody class="divide-y">

							<template v-for="company in items" :key="company.id">

								<!-- COMPANY -->
								<tr class="font-medium">
									<td class="px-6 py-2">{{ company.company_name }}</td>
									<td class="px-6 py-2">{{ company.company_code }}</td>

									<td class="px-6 py-2">
										<img v-if="company.company_logo"
										:src="`/storage/logos/${company.company_logo}`"
										class="h-10 w-10 object-cover rounded"/>
									</td>

									<td class="px-6 py-2 text-right">
										<div class="flex justify-end gap-1">
											<button @click="openEditModal(company)"
											class="p-2 text-blue-600 hover:bg-blue-50 rounded">
											<PencilSquareIcon class="w-4 h-4"/>
											</button>

											<button @click="openAddModal(company)"
											class="p-2 text-green-600 hover:bg-green-50 rounded">
											<PlusIcon class="w-4 h-4"/>
											</button>
										</div>
									</td>
								</tr>

							<!-- LOCATIONS -->
								<tr v-for="loc in company.companylocation"
								:key="loc.id"
								class="text-gray-600 hover:bg-gray-50">

									<td colspan="3" class="px-6 py-2 pl-12">
									↳ {{ loc.location }}
										{{ loc.address }}
										{{ loc.telephone }}
										{{ loc.telefax }}
									</td>

									<td class="px-6 py-2 text-right">
									<button @click="openEditModal(loc, true, company)"
									class="p-2 text-blue-500 hover:bg-blue-50 rounded">
									<PencilSquareIcon class="w-4 h-4"/>
									</button>
									</td>

								</tr>

							</template>

						</tbody>
					</table>
				</div>

				<!-- BOTTOM PAGINATION -->
				<div class="border-t">
				<pagination
				:page="page"
				:per-page="perPage"
				:last-page="lastPage"
				:total="total"
				@update:page="page = $event"
				@update:perPage="perPage = $event"
				/>
				</div>

			</div>
		</section>

		<!-- MODAL -->
		<modal v-model="showModal">

			<template #title>
				{{ isEdit
				? `Edit ${isSub ? 'Location' : 'Company'}`
				: `Add ${isSub ? 'Location' : 'Company'}`
				}}
			</template>

			<template #close-icon>
				<XMarkIcon class="w-5 h-5"/>
			</template>

			<!-- COMPANY -->
			<div v-if="!isSub"> 
				<div>
					<label class="text-sm">Company Name</label>
					<input v-model="modalItem.company_name" class="border px-3 py-2 rounded text-sm w-full"/>
					<span v-if="errors.company_name" class="text-red-500 text-xs">
						{{ errors.company_name }}
					</span>
				</div>
				<div>
					<label class="text-sm">Company Code</label>
					<input v-model="modalItem.company_code" class="border px-3 py-2 rounded text-sm w-full"/>
				</div>
				<label class="text-sm">Company Logo</label>
				<div class="flex items-center gap-4">
					<div
						class="h-20 w-20 border rounded flex items-center justify-center overflow-hidden bg-gray-50"
					>
						<img
						v-if="logoPreview"
						:src="logoPreview"
						class="h-full w-full object-cover"
						/>
						<span v-else class="text-xs text-gray-400">No Image</span>
					</div>

					<input
						type="file"
						accept="image/*"
						@change="handleLogoUpload"
						class="border px-3 py-2 rounded text-sm w-full"
					/>
				</div>
			</div>

			<!-- LOCATION -->
			<div v-if="isSub">
				<label class="text-sm">Location Name</label>
				<input v-model="modalItem.location" class="border px-3 py-2 rounded text-sm w-full"/>

				<label class="text-sm">Address</label>
				<input v-model="modalItem.address" class="border px-3 py-2 rounded text-sm w-full"/>

				<label class="text-sm">Telephone Number</label>
				<input v-model="modalItem.telephone" class="border px-3 py-2 rounded text-sm w-full"/>

				<label class="text-sm">Telefax</label>
				<input v-model="modalItem.telefax" class="border px-3 py-2 rounded text-sm w-full"/>
			</div>

			<template #footer>

				<button @click="showModal=false"
				class="px-4 py-2 text-sm bg-gray-100 rounded">
				Cancel
				</button>

				<button @click="saveItem"
				class="px-4 py-2 text-sm bg-blue-600 text-white rounded"
				:disabled="isSaving">
				{{ isSaving ? 'Saving...' : 'Save' }}
				</button>

			</template>

		</modal>
	</navigation>
</template>
