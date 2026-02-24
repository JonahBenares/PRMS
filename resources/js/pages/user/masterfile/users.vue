<script setup>
	import { ref, onMounted, reactive, computed } from "vue";
	import { Bars3Icon, PlusIcon, ExclamationTriangleIcon, XMarkIcon, PencilSquareIcon } from '@heroicons/vue/24/solid';
	import { useTable } from '@/composables/useTable'
	import searchbox from '@/composables/searchbox.vue';
	import pagination from "@/composables/pagination.vue";
	import navigation from "@/components/layouts/navigation.vue";
	import modal from "@/components/modal.vue";
	import axios from "axios";

	// table composable (same as employee)
	const {
		items,
		search,
		page,
		perPage,
		total,
		lastPage,
		loading,
		fetchData
	} = useTable('/api/users')

	const isSaving = ref(false)

	onMounted(fetchData)

	const pageCount = computed(() => lastPage.value)
	const totalItems = computed(() => total.value)

	// modal states
	const showModal = ref(false)
	const showDeleteModal = ref(false)
	const isEdit = ref(false)

	// modal data
	const modalItem = reactive({
		id: null,
		name: "",
		email: "",
		role: "",
		password: ""
	})

	// errors
	const errors = reactive({
		name: "",
		email: "",
		role: "",
		password: ""
	})

	// open add
	const openAddModal = () => {
		isEdit.value = false
		Object.assign(modalItem, {
			id: null,
			name: "",
			email: "",
			role: "",
			password: ""
		})
		Object.assign(errors, {
			name: "",
			email: "",
			role: "",
			password: ""
		})
		showModal.value = true
	}

	// open edit
	const openEditModal = (user) => {
		isEdit.value = true
		Object.assign(modalItem, {
			id: user.id,
			name: user.name,
			email: user.email,
			role: user.role,
			password: ""
		})
		Object.assign(errors, {
			name: "",
			email: "",
			role: "",
			password: ""
		})
		showModal.value = true
	}

	const closeModal = () => showModal.value = false

	// save user (same flow as employee save)
	const saveUser = async () => {
		if (isSaving.value) return

		Object.assign(errors, {
			name: "",
			email: "",
			role: "",
			password: ""
		})

		// frontend validation
		if (!modalItem.name) {
			errors.name = "Name is required"
			return
		}
		if (!modalItem.email) {
			errors.email = "Email is required"
			return
		}
		if (!modalItem.role) {
			errors.role = "Role is required"
			return
		}

		try {
			isSaving.value = true

			if (isEdit.value) {
				await axios.put(`/api/users/${modalItem.id}`, modalItem)
			} else {
				await axios.post("/api/users", modalItem)
			}

			await fetchData()
			showModal.value = false

		} catch (err) {
            if (err.response?.data?.errors) {
                const backendErrors = err.response.data.errors

                Object.keys(backendErrors).forEach(key => {
                    errors[key] = backendErrors[key][0] // get FIRST message only
                })
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

	const closeDeleteModal = () => showDeleteModal.value = false

	const deleteItem = async () => {
		try {
			await axios.delete(`/api/users/${modalItem.id}`)
			await fetchData()
			showDeleteModal.value = false
		} catch (err) {
			console.error(err)
		}
	}
</script>

<template>
	<navigation>
	<section class="items-center justify-center py-8 fade-up">
		<div class="bg-white border border-white/20 rounded-2xl shadow-xl max-w-6xl mx-auto">
			<!-- HEADER (same style as employee) -->
			<div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
				<div>
					<h2 class="text-2xl font-bold text-gray-600">Users</h2>
				</div>

				<div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
					<div class="w-full sm:w-96">
						<searchbox v-model="search" />
					</div>

					<button
						@click="openAddModal"
						class="inline-flex items-center justify-center gap-2
						px-4 py-2 bg-blue-600 text-white text-sm font-medium
						rounded-lg shadow hover:bg-blue-700 transition"
					>
						<PlusIcon class="w-4 h-4" />
						Add User
					</button>
				</div>
			</div>

			<!-- pagination top -->
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
					<thead class="bg-gray-100 sticky top-0 z-10">
					<tr class="text-gray-600 uppercase text-xs tracking-wide">
						<th class="px-6 py-3">Name</th>
						<th class="px-6 py-3">Email</th>
						<th class="px-6 py-3">Role</th>
						<th class="px-6 py-3 w-1">
							<div class="flex justify-center">
								<Bars3Icon class="size-4" />
							</div>
						</th>
					</tr>
					</thead>

					<tbody class="divide-y">
					<tr v-for="user in items" :key="user.id" class="hover:bg-gray-50">
						<td class="px-6 py-2 font-medium text-gray-800">{{ user.name }}</td>
						<td class="px-6 py-2 text-gray-600">{{ user.email }}</td>
						<td class="px-6 py-2 text-gray-600">{{ user.role }}</td>

						<td class="px-6 py-2">
							<div class="flex justify-center">
								<button
									@click="openEditModal(user)"
									class="p-2 rounded-md text-blue-600 hover:bg-blue-50"
								>
									<PencilSquareIcon class="w-4 h-4" />
								</button>
							</div>
						</td>
					</tr>

					<tr v-if="!loading && items.length === 0">
						<td colspan="4" class="py-10 text-center text-gray-500">
							No users found.
						</td>
					</tr>
					</tbody>
				</table>
			</div>

			<!-- pagination bottom -->
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

	<!-- MODAL (same reusable modal component as employee) -->
	<modal v-model="showModal">
		<template #title>
			{{ isEdit ? 'Edit User' : 'Add User' }}
		</template>
		<template #close-icon>
			<XMarkIcon class="w-5 h-5 cursor-pointer" @click="showModal=false" />
		</template>
		<div class="flex flex-col gap-4">
			<div>
				<label class="text-sm font-medium">Name</label>
				<input v-model="modalItem.name" :class="['mt-1 w-full border rounded-lg px-3 py-2 text-sm', errors.name ? 'input-error' : '']"/>
				<span v-if="errors.name" class="text-red-500 text-xs">{{ errors.name }}</span>
			</div>

			<div>
				<label class="text-sm font-medium">Email</label>
				<input v-model="modalItem.email" :class="['mt-1 w-full border rounded-lg px-3 py-2 text-sm', errors.email ? 'input-error' : '']"/>
				<span v-if="errors.email" class="text-red-500 text-xs">{{ errors.email }}</span>
			</div>

			<div>
				<label class="text-sm font-medium">Role</label>
				<select v-model="modalItem.role" :class="['mt-1 w-full border rounded-lg px-3 py-2 text-sm', errors.role ? 'input-error' : '']"">
					<option disabled value="">Select Role</option>
					<option>Admin</option>
					<option>Supervisor</option>
					<option>Enduser</option>
				</select>
				<span v-if="errors.role" class="text-red-500 text-xs">{{ errors.role }}</span>
			</div>
		</div>

		<template #footer>
			<button @click="showModal=false" class="px-4 py-2 text-sm bg-gray-100 rounded-lg" :disabled="isSaving">
				Cancel
			</button>

			<button
				@click="saveUser"
				class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg"
				:disabled="isSaving"
				>
				{{ isSaving ? 'Saving...' : isEdit ? 'Update' : 'Add' }}
			</button>
		</template>

	</modal>

	<!-- DELETE MODAL -->
	<div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
	<div class="bg-white rounded-2xl shadow-lg w-[600px] p-6 text-center absolute top-32">

	<ExclamationTriangleIcon class="w-32 h-32 text-red-600 mx-auto" />

	<h3 class="text-2xl font-bold mb-4">Confirm Deletion</h3>

	<p>Delete <strong>{{ modalItem.name }}</strong> ?</p>

	<div class="flex justify-center gap-3 mt-8">
	<button @click="closeDeleteModal" class="px-4 py-2 bg-gray-200 rounded">
	Cancel
	</button>

	<button @click="deleteItem" class="px-4 py-2 bg-red-600 text-white rounded">
	Delete
	</button>
	</div>

	</div>
	</div>

	</navigation>
</template>
