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
    } = useTable('/api/enduses')

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

    // Enduses data
    const enduses = ref([]);

    // Modal states
    const showModal = ref(false);
    const showDeleteModal = ref(false);
    const isEdit = ref(false);
    const modalItem = reactive({ id: null, enduse_name: "" });
    const errors = reactive({ enduse_name: "" });

    // Add/Edit modal
    const openAddModal = () => {
        isEdit.value = false;
        Object.assign(modalItem, { id: null, enduse_name: "" });
        Object.assign(errors, { enduse_name: "" });
        showModal.value = true;
    };

    const openEditModal = (enduse) => {
        isEdit.value = true;
        Object.assign(modalItem, enduse);
        Object.assign(errors, { enduse_name: "" });
        showModal.value = true;
    };

    const closeModal = () => showModal.value = false;

    // Save Enduse
    const saveEnduse = async () => {
        Object.assign(errors, { enduse_name: "" });

        if (!modalItem.enduse_name) {
            errors.enduse_name = "Enduse name is required";
            return;
        }

        try {
            if (isEdit.value) {
                await axios.put(`/api/enduses/${modalItem.id}`, modalItem);
            } else {
                await axios.post("/api/enduses", modalItem);
            }
            await fetchData();
            showModal.value = false;
        } catch (err) {
            if (err.response?.data?.errors) {
                Object.assign(errors, err.response.data.errors);
            }
            console.error(err);
        }
    };

    // Delete modal
    const openDeleteModal = (enduse) => {
        Object.assign(modalItem, enduse);
        showDeleteModal.value = true;
    };

    const closeDeleteModal = () => showDeleteModal.value = false;

    const deleteItem = async () => {
        try {
            await axios.delete(`/api/enduses/${modalItem.id}`);
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
        <div class="bg-white rounded-lg shadow-lg max-w-6xl mx-auto">
            <!-- Header -->
            <div class="px-6 py-4 flex justify-between items-center mb-4 bg-white border-b rounded-t-lg">
                <h2 class="text-lg font-semibold">Enduse List</h2>
                <button 
                    @click="openAddModal"
                    class="flex text-sm items-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                >
                    <PlusIcon class="w-5 h-5 mr-1" /> Add Enduse
                </button>
            </div>

            <searchbox v-model="search" />

            <!-- Table -->
            <div class="overflow-x-auto">
                <table
                    id="itemTable"
                    class="min-w-full text-sm text-left text-gray-700 border border-gray-200 rounded-lg overflow-hidden"
                >
                <thead class="bg-gray-100 text-gray-900 font-semibold">
                    <tr>
                        <td class="px-4 py-2 cursor-pointer">Enduse Name</td>
                        <td class="px-4 py-2 cursor-pointer" width="3%"></td>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-for="enduse in items" :key="enduse.id" class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ enduse.enduse_name }}</td>
                        <td class="px-4 py-2 flex items-center space-x-1">
                            <button @click="openEditModal(enduse)" class="flex items-center justify-center px-1 py-1 bg-blue-500 text-white rounded-lg hover:bg-blue-700">
                                <PencilSquareIcon class="w-4 h-4" />
                            </button>
                            <!-- <button @click="openDeleteModal(enduse)" class="flex items-center justify-center px-1 py-1 bg-red-500 text-white rounded-lg hover:bg-red-700">
                                <TrashIcon class="w-4 h-4" />
                            </button> -->
                        </td>
                    </tr>
                    <tr v-if="items.length === 0">
                        <td colspan="2" class="text-center py-4 text-gray-500">No enduse found.</td>
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

            <!-- Add/Edit Modal -->
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="bg-black/50 w-full h-full" @click="closeModal"></div>
                <div class="bg-white rounded-lg w-[600px] absolute top-32">
                    <div class="flex justify-between px-6 py-4 bg-gray-100 rounded-t-lg">
                        <h3 class="text-lg font-semibold">{{ isEdit ? 'Edit Enduse' : 'Add Enduse' }}</h3>
                        <button @click="closeModal"><XMarkIcon class="w-5 h-5"/></button>
                    </div>
                    <div class="flex flex-col gap-3 px-6 py-4">
                        <div class="flex flex-col gap-1">
                            <label class="text-sm">Enduse Name</label>
                            <input v-model="modalItem.enduse_name" placeholder="Enduse Name" class="border px-3 py-2 rounded text-sm"/>
                            <span v-if="errors.enduse_name" class="text-red-500 text-xs">{{ errors.enduse_name }}</span>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 px-6 py-4">
                        <button @click="closeModal" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-400">Cancel</button>
                        <button @click="saveEnduse" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            {{ isEdit ? 'Update' : 'Add' }}
                        </button>
                    </div>
                </div>
            </div>

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
        </div>
    </section>
</navigation>
</template>