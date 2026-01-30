<script setup>
    import { ref, reactive, onMounted, computed } from "vue";
    import axios from "axios";
    import { Bars3Icon, PlusIcon, ExclamationTriangleIcon, XMarkIcon, PencilSquareIcon } from '@heroicons/vue/24/solid';
    import { useTable } from '@/composables/useTable'
	import searchbox from '@/composables/searchbox.vue';
	import pagination from "@/composables/pagination.vue";
    import navigation from "@/components/layouts/navigation.vue";
    import modal from "@/components/modal.vue";

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
        <section class="items-center justify-center py-8 fade-up">
            <div class="bg-white border border-white/20 rounded-2xl shadow-xl max-w-6xl mx-auto ">
                <!-- Header -->
                <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
  
                    <!-- TITLE + SUBTITLE -->
                    <div class="flex flex-col gap-1">
                        <h2 class="text-2xl font-bold text-gray-600">Departments</h2>
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
                                <th class="px-6 py-3">Department</th>
                                <th class="px-6 py-3">Code</th>
                                <th class="px-6 py-3 w-1" >
                                    <div class="flex justify-center">
                                        <Bars3Icon class="size-4"></Bars3Icon>
                                    </div>
                                </th>
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
            </div>
        </section>

        <modal v-model="showModal">
            <!-- Title -->
            <template #title>
                {{ isEdit ? 'Edit Department' : 'Add Department' }}
            </template>

            <!-- Close icon -->
            <template #close-icon>
                <XMarkIcon class="w-5 h-5" />
            </template>

            <!-- Body -->
            <div class="flex flex-col gap-4">
                <div>
                    <label class="text-sm font-medium">Department Name</label>
                    <input
                        v-model="modalItem.department_name"
                        class="mt-1 w-full border rounded-lg px-3 py-2 text-sm"
                    />
                    <span v-if="errors.department_name" class="text-red-500 text-xs">
                        {{ errors.department_name }}
                    </span>
                </div>

                <div>
                    <label class="text-sm font-medium">Department Code</label>
                    <input
                        v-model="modalItem.department_code"
                        class="mt-1 w-full border rounded-lg px-3 py-2 text-sm"
                    />
                    <span v-if="errors.department_code" class="text-red-500 text-xs">
                        {{ errors.department_code }}
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
                    @click="saveDept"
                    class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg flex items-center gap-2"
                    :disabled="isSaving"
                    >
                    <span>{{ isSaving ? 'Saving...' : 'Save' }}</span>
                </button>
            </template>
        </modal>

    </navigation>
</template>