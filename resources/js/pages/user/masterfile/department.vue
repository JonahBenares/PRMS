<script setup>
    import { ref, reactive, onMounted, computed } from "vue";
    import axios from "axios";
    import { TrashIcon, PlusIcon, ExclamationTriangleIcon, XMarkIcon, PencilSquareIcon } from '@heroicons/vue/24/solid';
    import { useTable } from '@/composables/useTable'
	import searchbox from '@/composables/searchbox.vue';
	import pagination from "@/composables/pagination.vue";
    import navigation from "@/components/layouts/navigation.vue";

    const {
		items,
		search,
		page,
		perPage,
		total,
		lastPage,
		loading,
		fetchData
	} = useTable('/api/departments')

	onMounted(fetchData)

    const isSaving = ref(false)
    // Modal states
    const showModal = ref(false);
    const showDeleteModal = ref(false);
    const isEdit = ref(false);
    const modalItem = reactive({ id: null, department_name: "", department_code: "" });
    const errors = reactive({ department_name: "", department_code: "" });

    // Add/Edit modal
    const openAddModal = () => {
        isEdit.value = false;
        Object.assign(modalItem, { id: null, department_name: "", department_code: "" });
        Object.assign(errors, { department_name: "", department_code: "" });
        showModal.value = true;
    };

    const openEditModal = (dept) => {
        isEdit.value = true;
        Object.assign(modalItem, dept);
        Object.assign(errors, { department_name: "", department_code: "" });
        showModal.value = true;
    };

    const closeModal = () => showModal.value = false;

    // Save department
    const saveDept = async () => {
        if (isSaving.value) return

        // reset errors
        Object.assign(errors, {
            department_name: "",
            department_code: "",
        })

        // frontend validation (DO NOT start loading yet)
        if (!modalItem.department_name) {
            errors.department_name = "Department name is required"
            return
        }

        if (!modalItem.department_code) {
            errors.department_code = "Department code is required"
            return
        }

        try {
            isSaving.value = true

            if (isEdit.value) {
                await axios.put(`/api/departments/${modalItem.id}`, modalItem)
            } else {
                await axios.post("/api/departments", modalItem)
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

    const closeDeleteModal = () => showDeleteModal.value = false;

    const deleteItem = async () => {
        try {
            await axios.delete(`/api/departments/${modalItem.id}`);
            await fetchData();
            showDeleteModal.value = false;
        } catch (err) {
            console.error(err);
        }
    };

    // Initialize
    onMounted(async () => {
        await fetchData();
    });
</script>
<template>
    <navigation>
        <section class="items-center justify-center py-8">
            <div class="bg-white border border-white/20 rounded-2xl shadow-xl max-w-6xl mx-auto ">
                
                <!-- Header -->
                <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
  
                    <!-- TITLE + SUBTITLE -->
                    <div class="flex flex-col gap-1">
                        <h2 class="text-2xl font-bold text-gray-800">Departments</h2>
                    </div>

                    <!-- SEARCH + ADD -->
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto">
                        
                        <!-- Search -->
                        <div class="flex-1 sm:flex-none w-full sm:w-72">
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
                        Add Department
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
                                <th class="px-6 py-3">Department Name</th>
                                <th class="px-6 py-3">Code</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">
                            <tr
                                v-for="dept in items"
                                :key="dept.id"
                                class="hover:bg-gray-50 transition"
                            >
                                <td class="px-6 py-2 font-medium text-gray-800">
                                {{ dept.department_name }}
                                </td>

                                <td class="px-6 py-2 text-gray-600">
                                {{ dept.department_code }}
                                </td>

                                <td class="px-6 py-2 text-right">
                                    <button
                                        @click="openEditModal(dept)"
                                        class="inline-flex items-center justify-center p-2 rounded-md text-blue-600 hover:bg-blue-50"
                                        title="Edit"
                                    >
                                        <PencilSquareIcon class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>

                            <!-- Empty state -->
                            <tr v-if="!loading && items.length === 0">
                                <td colspan="3" class="py-10 text-center text-gray-500">
                                No departments found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

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

                <Transition name="backdrop">
                    <div
                        v-if="showModal"
                        class="fixed inset-0 z-50 flex items-center justify-center"
                    >
                        <!-- Backdrop -->
                        <div
                            class="absolute inset-0 bg-black/40 backdrop-blur-sm"
                            @click="closeModal"
                        ></div>

                        <!-- Modal -->
                        <Transition name="modal">
                            <div
                                class="relative bg-white rounded-2xl w-[600px] max-w-[90%] shadow-xl"
                            >
                                <!-- Header -->
                                <div class="flex justify-between items-center px-6 py-4 border-b">
                                    <h3 class="text-lg font-semibold">
                                        {{ isEdit ? 'Edit Department' : 'Add Department' }}
                                    </h3>
                                    <button @click="closeModal" class="text-gray-500 hover:text-gray-700">
                                        <XMarkIcon class="w-5 h-5" />
                                    </button>
                                </div>

                                <!-- Body -->
                                <div class="flex flex-col gap-4 px-6 py-4">
                                    <div>
                                        <label class="text-sm font-medium">Department Name</label>
                                        <input
                                            v-model="modalItem.department_name"
                                            class="mt-1 w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                            placeholder="Department Name"
                                        />
                                        <span v-if="errors.department_name" class="text-red-500 text-xs">
                                            {{ errors.department_name }}
                                        </span>
                                    </div>

                                    <div>
                                        <label class="text-sm font-medium">Department Code</label>
                                        <input
                                            v-model="modalItem.department_code"
                                            class="mt-1 w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                            placeholder="Department Code"
                                        />
                                        <span v-if="errors.department_code" class="text-red-500 text-xs">
                                            {{ errors.department_code }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Footer -->
                                <div class="flex justify-end gap-2 px-6 py-4 border-t">
                                    <button
                                        @click="closeModal"
                                        class="px-4 py-2 text-sm bg-gray-100 rounded-lg hover:bg-gray-200"
                                        :disabled="isSaving"
                                    >
                                        Cancel
                                    </button>

                                    <button
                                        @click="saveDept"
                                        class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2 disabled:opacity-60"
                                        :disabled="isSaving"
                                    >
                                        <!-- Spinner -->
                                        <svg
                                            v-if="isSaving"
                                            class="w-4 h-4 animate-spin"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                        >
                                            <circle
                                                class="opacity-25"
                                                cx="12"
                                                cy="12"
                                                r="10"
                                                stroke="currentColor"
                                                stroke-width="4"
                                            />
                                            <path
                                                class="opacity-75"
                                                fill="currentColor"
                                                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                                            />
                                        </svg>

                                        <span>
                                            {{ isSaving
                                                ? 'Saving...'
                                                : isEdit
                                                    ? 'Update Department'
                                                    : 'Add Department'
                                            }}
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </Transition>
                    </div>
                </Transition>


                <!-- Delete Modal -->
                <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                    <div class="bg-white rounded-2xl shadow-lg w-[600px] max-w-[90%] p-6 text-center absolute top-32">
                        <ExclamationTriangleIcon class="w-32 h-32 text-red-600 mx-auto mb-4" />
                        <h3 class="!text-2xl font-bold text-gray-800 mb-4">Confirm Deletion</h3>
                        <p class="text-gray-600 mb-6">
                            Are you sure you want to delete <span class="font-semibold">{{ modalItem.department_name }}</span>?
                        </p>
                        <div class="flex justify-center gap-3">
                            <button @click="closeDeleteModal" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancel</button>
                            <button @click="deleteItem" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </navigation>
</template>