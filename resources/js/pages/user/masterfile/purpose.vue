<script setup>
	import { ref, reactive, onMounted, nextTick, computed } from "vue";
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
	} = useTable('/api/purposes')

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

	const purposes = ref([]);
	const isSaving = ref(false)
	const showModal = ref(false);
	const showDeleteModal = ref(false);
	const isEdit = ref(false);
	const modalItem = reactive({ id: null, purpose_name: "" });
	const errors = reactive({ purpose_name: "" });

	// Modals
	const openAddModal = () => {
		isEdit.value = false;
		Object.assign(modalItem, { id: null, purpose_name: "" });
		Object.assign(errors, { purpose_name: "" });
		showModal.value = true;
	};

	const openEditModal = (item) => {
		isEdit.value = true;
		Object.assign(modalItem, item);
		Object.assign(errors, { purpose_name: "" });
		showModal.value = true;
	};

	const closeModal = () => showModal.value = false;


	const savePurpose = async () => {
        if (isSaving.value) return

        // reset errors
        Object.assign(errors, {
            purpose_name: "",
        })

        // frontend validation (DO NOT start loading yet)
        if (!modalItem.purpose_name) {
            errors.purpose_name = "Purpose name is required"
            return
        }


        try {
            isSaving.value = true

            if (isEdit.value) {
                await axios.put(`/api/purposes/${modalItem.id}`, modalItem)
            } else {
                await axios.post("/api/purposes", modalItem)
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


	const openDeleteModal = (item) => {
		Object.assign(modalItem, item);
		showDeleteModal.value = true;
	};

	const closeDeleteModal = () => showDeleteModal.value = false;

	const deleteItem = async () => {
		try {
			await axios.delete(`/api/purposes/${modalItem.id}`);
			await fetchData();
			showDeleteModal.value = false;
		} catch (err) {
			console.error(err);
		}
	};

	onMounted(fetchData);
</script>

<template>
	<navigation>
		<section class="items-center justify-center py-8 fade-up">
            <div class="bg-white border border-white/20 rounded-2xl shadow-xl max-w-6xl mx-auto ">
                <!-- Header -->
                <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <!-- TITLE + SUBTITLE -->
                    <div class="flex flex-col gap-1">
                        <h2 class="text-2xl font-bold text-gray-600">Purpose</h2>
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
                        Add Purpose
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
                                <th class="px-6 py-3">Purpose Name</th>
                                <th class="px-6 py-3 w-1" >
                                    <div class="flex justify-center">
                                        <Bars3Icon class="size-4"></Bars3Icon>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="purpose in items" :key="purpose.id" class="transition" >
                                <td class="px-6 py-2 font-medium text-gray-800">
                                {{ purpose.purpose_name }}
                                </td>
                                <td class="px-6 py-2 text-right">
                                    <button
                                        @click="openEditModal(purpose)"
                                        class="inline-flex items-center justify-center p-2 rounded-md text-blue-600 hover:bg-blue-50"
                                        title="Edit"
                                    >
                                        <PencilSquareIcon class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                            <!-- Empty state -->
                            <tr v-if="!loading && items.length === 0">
                                <td colspan="2" class="py-10 text-center text-gray-500">
                                	No purpose found.
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
		
		<!-- Add/Edit Modal -->
		<modal v-model="showModal">
            <!-- Title -->
            <template #title>
                {{ isEdit ? 'Edit Purpose' : 'Add Purpose' }}
            </template>

            <!-- Close icon -->
            <template #close-icon>
                <XMarkIcon class="w-5 h-5" />
            </template>

            <!-- Body -->
            <div class="flex flex-col gap-4">
                <div>
                    <label class="text-sm font-medium">Purpose Name</label>
                    <input
                        v-model="modalItem.purpose_name"
                        class="mt-1 w-full border rounded-lg px-3 py-2 text-sm"
                    />
                    <span v-if="errors.purpose_name" class="text-red-500 text-xs">
                        {{ errors.purpose_name }}
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
                    @click="savePurpose"
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
				<p class="text-gray-600 mb-6">Are you sure you want to delete <span class="font-semibold">{{ modalItem.purpose_name }}</span>?</p>
				<div class="flex justify-center gap-3">
					<button @click="closeDeleteModal" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancel</button>
					<button @click="deleteItem" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
				</div>
			</div>
		</div>
	</navigation>
</template>