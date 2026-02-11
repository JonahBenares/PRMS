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
} = useTable('/api/categories')

onMounted(fetchData)

// -------------------
// MODAL STATE
// -------------------
const showModal = ref(false)
const isSaving = ref(false)
const isEdit = ref(false)
const isSub = ref(false)

const parentCategory = ref(null)

const modalItem = reactive({
    id: null,
    name: ''
})

const errors = reactive({
    name: ''
})

// -------------------
// MODAL ACTIONS
// -------------------
const openAddModal = (parent = null) => {
    isEdit.value = false
    isSub.value = !!parent
    parentCategory.value = parent

    Object.assign(modalItem, { id: null, name: '' })
    errors.name = ''

    showModal.value = true
}

const openEditModal = (item, sub = false, parent = null) => {
    isEdit.value = true
    isSub.value = sub

    if (sub) {
        parentCategory.value = parent   // ✅ THIS FIXES IT
    } else {
        parentCategory.value = null
    }

    Object.assign(modalItem, {
        id: item.id,
        name: sub ? item.sub_cat_name : item.category_name
    })

    errors.name = ''
    showModal.value = true
}

const saveItem = async () => {
    if (isSaving.value) return

    errors.name = ''
    if (!modalItem.name.trim()) {
        errors.name = "Name is required"
        return
    }

    try {
        isSaving.value = true

        if (isEdit.value) {
            if (isSub.value) {
                await axios.put(`/api/update_subcategory/${modalItem.id}`, {
                    sub_cat_name: modalItem.name
                })
            } else {
                await axios.put(`/api/update_category/${modalItem.id}`, {
                    category_name: modalItem.name
                })
            }
        } else {
            if (isSub.value) {
                await axios.post('/api/new_subcategory', {
                    sub_cat_name: modalItem.name,
                    category_id: parentCategory.value.id
                })
            } else {
                await axios.post('/api/new_category', {
                    category_name: modalItem.name
                })
            }
        }

        await fetchData()
        showModal.value = false

    } catch (err) {
		if (err.response?.status === 422) {
			const validationErrors = err.response.data.errors

			// CATEGORY
			if (validationErrors.category_name) {
				errors.name = validationErrors.category_name[0]
			}

			// SUBCATEGORY (if you later add unique validation)
			if (validationErrors.sub_cat_name) {
				errors.name = validationErrors.sub_cat_name[0]
			}
		} else {
			console.error(err)
		}
	} finally {
        isSaving.value = false
    }
}
</script>
<template>
	<navigation>
		<section class="items-center justify-center py-8 fade-up">
			<div class="bg-white border border-white/20 rounded-2xl shadow-xl max-w-6xl mx-auto">
				<!-- Header -->
				<div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

					<div class="flex flex-col gap-1">
						<h2 class="text-2xl font-bold text-gray-600">Categories</h2>
					</div>

					<div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto">

						<div class="flex-1 sm:flex-none w-full sm:w-96">
							<searchbox v-model="search" />
						</div>

						<button
						@click="openAddModal()"
						class="w-full sm:w-auto inline-flex items-center justify-center gap-2
							px-4 py-2 bg-blue-600 text-white text-sm font-medium
							rounded-lg shadow hover:bg-blue-700 transition">
							<PlusIcon class="w-4 h-4"/>
							Add Category
						</button>

					</div>
				</div>

				<!-- Top Pagination -->
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
							<th class="px-6 py-3">Name</th>
							<th class="px-6 py-3 w-1">
								<div class="flex justify-center">
									<Bars3Icon class="size-4"/>
								</div>
							</th>
							</tr>
						</thead>

						<tbody class="divide-y">
							<template v-for="category in items" :key="category.id">
								<!-- Category -->
								<tr class="font-medium">
									<td class="px-6 py-2 text-gray-800">
										{{ category.category_name }}
									</td>

									<td class="px-6 py-2 text-right">
										<div class="flex justify-end gap-1">

										<button
										@click="openEditModal(category)"
										class="p-2 rounded-md text-blue-600 hover:bg-blue-50">
										<PencilSquareIcon class="w-4 h-4"/>
										</button>

										<button
										@click="openAddModal(category)"
										class="p-2 rounded-md text-green-600 hover:bg-green-50">
										<PlusIcon class="w-4 h-4"/>
										</button>

										</div>
									</td>
								</tr>
								<!-- Subcategories -->
								<tr v-for="sub in category.subcategories"
								:key="sub.id"
								class="text-gray-600">
									<td class="px-6 py-2 pl-12">
									↳ {{ sub.sub_cat_name }}
									</td>
									<td class="px-6 py-2 text-right">
										<button
										@click="openEditModal(sub, true, category)"
										class="p-2 rounded-md text-blue-500 hover:bg-blue-50">
										<PencilSquareIcon class="w-4 h-4"/>
										</button>
									</td>
								</tr>
							</template>
							<tr v-if="!loading && items.length === 0">
								<td colspan="2" class="py-10 text-center text-gray-500">
									No categories found.
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<!-- Bottom Pagination -->
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
			? `Edit ${isSub ? 'Subcategory' : 'Category'}`
			: `Add ${isSub ? 'Subcategory' : 'Category'}`
			}}
			</template>

			<template #close-icon>
			<XMarkIcon class="w-5 h-5"/>
			</template>

			<div v-if="isSub">
				<label class="text-sm font-medium">Category</label>
				<input
				type="text"
				:value="parentCategory?.category_name"
				class="mt-1 mb-4 w-full border rounded-lg px-3 py-2 text-sm bg-gray-100 cursor-not-allowed"
				readonly
				/>
			</div>

			<!-- Subcategory / Category Name -->
			<div>
				<label class="text-sm font-medium">
				{{ isSub ? 'Subcategory' : 'Category' }} Name
				</label>
				<input
				v-model="modalItem.name"
				class="mt-1 w-full border rounded-lg px-3 py-2 text-sm"
				placeholder="Enter name"
				/>
				<span v-if="errors.name" class="text-red-500 text-xs">
				{{ errors.name }}
				</span>
			</div>

			<template #footer>

			<button
			@click="showModal=false"
			class="px-4 py-2 text-sm bg-gray-100 rounded-lg"
			:disabled="isSaving">
			Cancel
			</button>

			<button
			@click="saveItem"
			class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg"
			:disabled="isSaving">
			{{ isSaving ? 'Saving...' : 'Save' }}
			</button>

			</template>

		</modal>
	</navigation>
</template>
