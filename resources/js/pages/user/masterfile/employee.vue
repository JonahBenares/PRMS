<script setup>
	import { ref, onMounted, reactive, computed } from "vue";
	import { Bars3Icon, PlusIcon, ExclamationTriangleIcon, XMarkIcon, PencilSquareIcon } from '@heroicons/vue/24/solid';
	import { useTable } from '@/composables/useTable'
	import searchbox from '@/composables/searchbox.vue';
	import pagination from "@/composables/pagination.vue";
	import navigation from "@/components/layouts/navigation.vue";
	import modal from "@/components/modal.vue";
	import axios from "axios";

	// Table data using useTable composable
	const {
		items,
		search,
		page,
		perPage,
		total,
		lastPage,
		loading,
		fetchData
	} = useTable('/api/employees')

	onMounted(fetchData)
	const isSaving = ref(false)
	const pageCount = computed(() => lastPage.value)
	const totalItems = computed(() => total.value)

	// Departments list
	const listdepartment = ref([]);

	// Fetch departments
	const getdepartment = async () => {
		try {
			const response = await axios.get("/api/department_list");
			listdepartment.value = response.data.department;
		} catch (err) {
			console.error(err);
		}
	}

	onMounted(async () => {
		await fetchData();
		await getdepartment();
	})

	// Modal controls
	const showModal = ref(false);
	const showDeleteModal = ref(false);
	const isEdit = ref(false);

	// Reactive modal item
	const modalItem = reactive({
		id: null,
		employee_name: "",
		position: "",
		department_id: null,
		department_name: "",
	});

	// Reactive errors object
	const errors = reactive({
		employee_name: "",
		position: "",
		department_id: "",
	});

	// Open add modal
	const openAddModal = () => {
		isEdit.value = false;
		Object.assign(modalItem, {
			id: null,
			employee_name: "",
			position: "",
			department_id: null,
			department_name: "",
		});
		Object.assign(errors, {
			employee_name: "",
			position: "",
			department_id: "",
		});
		showModal.value = true;
	};

	// Open edit modal
	const openEditModal = (item) => {
		isEdit.value = true;
		Object.assign(modalItem, {
			id: item.id,
			employee_name: item.employee_name,
			position: item.position,
			department_id: item.department_id,
			department_name: item.department_name,
		});
		Object.assign(errors, {
			employee_name: "",
			position: "",
			department_id: "",
		});
		showModal.value = true;
	};

	// Close modal
	const closeModal = () => showModal.value = false;

	// Save employee
	const saveEmployee = async () => {
		if (isSaving.value) return;

		// Reset errors
		Object.assign(errors, {
			employee_name: "",
			position: "",
			department_id: "",
		});

		// Frontend validation
		if (!modalItem.employee_name) {
			errors.employee_name = "Employee name is required";
			return;
		}
		if (!modalItem.position) {
			errors.position = "Position is required";
			return;
		}
		if (!modalItem.department_id) {
			errors.department_id = "Department is required";
			return;
		}

		// Get department_name
		const selectedDept = listdepartment.value.find(d => d.id === modalItem.department_id);
		modalItem.department_name = selectedDept ? selectedDept.department_name : "";

		try {
			isSaving.value = true;

			if (isEdit.value) {
				await axios.put(`/api/employees/${modalItem.id}`, modalItem);
			} else {
				await axios.post("/api/employees", modalItem);
			}

			await fetchData();
			showModal.value = false;
		} catch (err) {
			if (err.response?.data?.errors) {
				Object.assign(errors, err.response.data.errors);
			}
			console.error(err);
		} finally {
			isSaving.value = false;
		}
	};

	// Delete modal
	const openDeleteModal = (item) => {
		Object.assign(modalItem, item);
		showDeleteModal.value = true;
	};
	const closeDeleteModal = () => showDeleteModal.value = false;
	const deleteItem = async () => {
		try {
			await axios.delete(`/api/employees/${modalItem.id}`);
			await fetchData();
			showDeleteModal.value = false;
		} catch (err) {
			console.error(err);
		}
	};
</script>
<template>
    <navigation>
		<section class="items-center justify-center py-8 fade-up">
            <div class="bg-white border border-white/20 rounded-2xl shadow-xl max-w-6xl mx-auto ">
                <!-- Header -->
                <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <!-- TITLE + SUBTITLE -->
                    <div class="flex flex-col gap-1">
                        <h2 class="text-2xl font-bold text-gray-600">Employees</h2>
                    </div>

                    <!-- SEARCH + ADD -->
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto">
                        
                        <!-- Search -->
                        <div class="flex-1 sm:flex-none w-full sm:w-96">
                            <searchbox v-model="search" />
                        </div>

                        <!-- Add Button -->
                        <button
                        @click="openAddModal"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2
                                px-4 py-2 bg-blue-600 text-white text-sm font-medium
                                rounded-lg shadow hover:bg-blue-700 transition"
                        >
                        <PlusIcon class="w-4 h-4" />
                        Add Employee
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

				<!-- Table -->
				<div class="overflow-hidden">
					<table class="min-w-full text-sm text-left">
						<thead class="bg-gray-100 sticky top-0 z-10">
							<tr class="text-gray-600 uppercase text-xs tracking-wide">
								<th class="px-6 py-3">Employee</th>
								<th class="px-6 py-3">Position</th>
								<th class="px-6 py-3">Department</th>
								<th class="px-6 py-3 w-1">
									<div class="flex justify-center">
										<Bars3Icon class="size-4" />
									</div>
								</th>
							</tr>
						</thead>

						<tbody class="divide-y">
							<tr
								v-for="employee in items"
								:key="employee.id"
								class="hover:bg-gray-50 transition"
							>
								<td class="px-6 py-2 font-medium text-gray-800">
									{{ employee.employee_name }}
								</td>

								<td class="px-6 py-2 text-gray-600">
									{{ employee.position }}
								</td>

								<td class="px-6 py-2 text-gray-600">
									{{ employee.department_name }}
								</td>

								<td class="px-6 py-2">
									<div class="flex justify-center">
										<button
											@click="openEditModal(employee)"
											class="inline-flex items-center justify-center p-2 rounded-md text-blue-600 hover:bg-blue-50"
											title="Edit"
										>
											<PencilSquareIcon class="w-4 h-4" />
										</button>
									</div>
								</td>
							</tr>

							<!-- Empty state -->
							<tr v-if="!loading && items.length === 0">
								<td colspan="4" class="py-10 text-center text-gray-500">
									No employees found.
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<!-- keep your pagination below -->
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

		<modal v-model="showModal">
			<!-- Title -->
			<template #title>
				{{ isEdit ? 'Edit Employee' : 'Add Employee' }}
			</template>

			<!-- Close icon -->
			<template #close-icon>
				<XMarkIcon class="w-5 h-5" @click="showModal = false" />
			</template>

			<!-- Body -->
			<div class="flex flex-col gap-4">
				<!-- Employee Name -->
				<div>
					<label class="text-sm font-medium">Employee Name</label>
					<input
						v-model="modalItem.employee_name"
						placeholder="Enter employee name"
						class="mt-1 w-full border rounded-lg px-3 py-2 text-sm"
					/>
					<span v-if="errors.employee_name" class="text-red-500 text-xs">
						{{ errors.employee_name }}
					</span>
				</div>

				<!-- Position -->
				<div>
					<label class="text-sm font-medium">Position</label>
					<input
						v-model="modalItem.position"
						placeholder="Enter position"
						class="mt-1 w-full border rounded-lg px-3 py-2 text-sm"
					/>
					<span v-if="errors.position" class="text-red-500 text-xs">
						{{ errors.position }}
					</span>
				</div>

				<!-- Department -->
				<div>
					<label class="text-sm font-medium">Department</label>
					<select
						v-model="modalItem.department_id"
						class="mt-1 w-full border rounded-lg px-3 py-2 text-sm"
					>
						<option value="" disabled>Select Department</option>
						<option v-for="dept in listdepartment" :key="dept.id" :value="dept.id">
							{{ dept.department_name }}
						</option>
					</select>
					<span v-if="errors.department_id" class="text-red-500 text-xs">
						{{ errors.department_id }}
					</span>
				</div>
			</div>

			<!-- Footer -->
			<template #footer>
				<button
					@click="showModal = false"
					class="px-4 py-2 text-sm bg-gray-100 rounded-lg"
					:disabled="isSaving"
				>
					Cancel
				</button>

				<button
					@click="saveEmployee"
					class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg flex items-center gap-2"
					:disabled="isSaving"
				>
					<span>{{ isSaving ? 'Saving...' : isEdit ? 'Update' : 'Add' }}</span>
				</button>
			</template>
		</modal>

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
    </navigation>
</template>
