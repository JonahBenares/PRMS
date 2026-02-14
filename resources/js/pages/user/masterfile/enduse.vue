<script setup>
import { ref, reactive, onMounted, watch } from "vue";
import axios from "axios";
import { Bars3Icon, PlusIcon, ExclamationTriangleIcon, XMarkIcon, PencilSquareIcon } from '@heroicons/vue/24/solid';
import searchbox from '@/composables/searchbox.vue';
import pagination from "@/composables/pagination.vue";
import navigation from "@/components/layouts/navigation.vue";
import modal from "@/components/modal.vue";

// Table state
const items = ref([])
const search = ref('')
const page = ref(1)
const perPage = ref(10)
const total = ref(0)
const lastPage = ref(0)
const loading = ref(false)

// Modal state
const isSaving = ref(false)
const showModal = ref(false)
const showDeleteModal = ref(false)
const isEdit = ref(false)
const modalItem = reactive({ id: null, enduse_name: "" })
const errors = reactive({ enduse_name: "" })

// Fetch data
const fetchData = async () => {
    loading.value = true
    try {
        const response = await axios.get('/api/enduses', {
            params: {
                page: page.value,
                per_page: perPage.value,
                search: search.value.trim()
            }
        })

        // Directly assign the array
        items.value = response.data

        // If you want pagination info
        total.value = response.data.length
        lastPage.value = 1  // since API doesn't return pages
    } catch (err) {
        console.error(err)
        items.value = []
        total.value = 0
        lastPage.value = 0
    } finally {
        loading.value = false
    }
}

// Watchers
watch(search, () => { page.value = 1; fetchData() })
watch(page, () => fetchData())
watch(perPage, () => { page.value = 1; fetchData() })

// Modals
const openAddModal = () => {
    isEdit.value = false
    modalItem.id = null
    modalItem.enduse_name = ""
    errors.enduse_name = ""
    showModal.value = true
}

const openEditModal = (enduse) => {
    isEdit.value = true
    modalItem.id = enduse.id
    modalItem.enduse_name = enduse.enduse_name
    errors.enduse_name = ""
    showModal.value = true
}

const closeModal = () => showModal.value = false

const saveEnduse = async () => {
    errors.enduse_name = ""
    if (!modalItem.enduse_name.trim()) {
        errors.enduse_name = "Enduse name is required."
        return
    }
    isSaving.value = true
    try {
        if (isEdit.value) {
            await axios.put(`/api/enduses/${modalItem.id}`, { enduse_name: modalItem.enduse_name })
        } else {
            await axios.post('/api/enduses', { enduse_name: modalItem.enduse_name })
        }
        await fetchData()
        showModal.value = false
    } catch (err) {
        if (err.response?.data?.errors) {
            Object.keys(err.response.data.errors).forEach(key => {
                errors[key] = err.response.data.errors[key][0]
            })
        }
        console.error(err)
    } finally { isSaving.value = false }
}

const openDeleteModal = (enduse) => {
    modalItem.id = enduse.id
    modalItem.enduse_name = enduse.enduse_name
    showDeleteModal.value = true
}

const closeDeleteModal = () => showDeleteModal.value = false

const deleteItem = async () => {
    try {
        await axios.delete(`/api/enduses/${modalItem.id}`)
        await fetchData()
        showDeleteModal.value = false
    } catch (err) {
        console.error(err)
    }
}

// Initialize
onMounted(fetchData)
</script>


<template>
    <navigation>
        <section class="items-center justify-center py-8 fade-up">
            <div class="bg-white border border-white/20 rounded-2xl shadow-xl max-w-6xl mx-auto ">
                <!-- Header -->
                <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <!-- TITLE + SUBTITLE -->
                    <div class="flex flex-col gap-1">
                        <h2 class="text-2xl font-bold text-gray-600">Enduse</h2>
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
                        Add Enduse
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
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-gray-100 sticky top-0 z-10">
                            <tr class="text-gray-600 uppercase text-xs tracking-wide">
                                <th class="px-6 py-3">Enduse Name</th>
                                <th class="px-6 py-3 w-1" >
                                    <div class="flex justify-center">
                                        <Bars3Icon class="size-4"></Bars3Icon>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr
                                v-for="enduse in items"
                                :key="enduse.id"
                                class="transition"
                            >
                                <td class="px-6 py-2 font-medium text-gray-800">
                                {{ enduse.enduse_name }}
                                </td>
                                <td class="px-6 py-2 text-right">
                                    <button
                                        @click="openEditModal(enduse)"
                                        class="inline-flex items-center justify-center p-2 rounded-md text-blue-600 hover:bg-blue-50"
                                        title="Edit"
                                    >
                                        <PencilSquareIcon class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                            <!-- Empty state -->
                            <tr v-if="!loading && items?.length === 0">
                                <td colspan="2" class="py-10 text-center text-gray-500">
                                No enduse found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

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
                {{ isEdit ? 'Edit Enduse' : 'Add Enduse' }}
            </template>

            <!-- Close icon -->
            <template #close-icon>
                <XMarkIcon class="w-5 h-5" />
            </template>

            <!-- Body -->
            <div class="flex flex-col gap-4">
                <div>
                    <label class="text-sm font-medium">Enduse Name</label>
                    <input
                        v-model="modalItem.enduse_name"
                        class="mt-1 w-full border rounded-lg px-3 py-2 text-sm"
                    />
                    <span v-if="errors.enduse_name" class="text-red-500 text-xs">
                        {{ errors.enduse_name }}
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
                    @click="saveEnduse"
                    class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg flex items-center gap-2"
                    :disabled="isSaving"
                    >
                    <span>{{ isSaving ? 'Saving...' : 'Save' }}</span>
                </button>
            </template>
        </modal>
            
        <!-- Delete Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white rounded-2xl shadow-lg w-[600px] max-w-[90%] p-6 text-center absolute top-32">
                <ExclamationTriangleIcon class="w-32 h-32 text-red-600 mx-auto mb-4" />
                <h3 class="!text-2xl font-bold text-gray-800 mb-4">Confirm Deletion</h3>
                <p class="text-gray-600 mb-6">
                    Are you sure you want to delete <span class="font-semibold">{{ modalItem.enduse_name }}</span>?
                </p>
                <div class="flex justify-center gap-3">
                    <button @click="closeDeleteModal" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancel</button>
                    <button @click="deleteItem" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
                </div>
            </div>
        </div>
    </navigation>
</template>