<script setup>
import { ref, reactive, onMounted } from "vue";
import axios from "axios";
import { Bars3Icon, PlusIcon, ArrowPathIcon, EyeIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/solid';
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
} = useTable('/api/prs')

onMounted(fetchData)

// Delete Modal
const showDeleteModal = ref(false)
const modalItem = reactive({ id: null, pr_no: "" })

const openDeleteModal = (pr) => {
	Object.assign(modalItem, pr)
	showDeleteModal.value = true
}

const closeDeleteModal = () => showDeleteModal.value = false

const deleteItem = async () => {
	try {
		await axios.delete(`/api/prs/${modalItem.id}`)
		await fetchData()
		showDeleteModal.value = false
	} catch (err) {
		console.error(err)
	}
}

const openEditModal = (pr) => {
	console.log("Edit PR", pr)
}
</script>

<template>
    <navigation>
		<section class="items-center justify-center py-8">
			<div class="bg-white rounded-lg shadow-lg max-w-6xl mx-auto">
				<div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    
					<div>
						<h2 class="text-2xl font-bold text-gray-600">PR List</h2>
					</div>

					<div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto">
						
						<div class="flex-1 sm:flex-none w-full sm:w-96">
							<searchbox v-model="search" />
						</div>

						<a
							href="/create_pr"
							class="inline-flex items-center gap-2
									px-4 py-2 bg-blue-600 text-white text-sm font-medium
									rounded-lg shadow hover:bg-blue-700 transition"
						>
							<PlusIcon class="w-4 h-4" />
							Create PR
						</a>
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

				<div class="overflow-hidden">
					<table class="min-w-full text-sm text-left">
						<thead class="bg-gray-100 sticky top-0 z-10">
							<tr class="text-gray-600 uppercase text-xs tracking-wide">
								<th class="px-6 py-3">Date</th>
								<th class="px-6 py-3">PR No</th>
								<th class="px-6 py-3">Department</th>
								<th class="px-6 py-3">Purpose</th>
								<th class="px-6 py-3">End Use</th>
								<th class="px-6 py-3 w-1">
									<div class="flex justify-center">
										<Bars3Icon class="size-4"></Bars3Icon>
									</div>
								</th>
							</tr>
						</thead>

						<tbody class="divide-y">
							<tr
								v-for="pr in items"
								:key="pr.id"
								class="transition hover:bg-gray-50"
							>
								<td class="px-6 py-2">{{ pr.date_prepared }}</td>
								<td class="px-6 py-2 font-medium">{{ pr.pr_no }}</td>
								<td class="px-6 py-2">{{ pr.department_name }}</td>
								<td class="px-6 py-2">{{ pr.purpose_name }}</td>
								<td class="px-6 py-2">{{ pr.enduse_name }}</td>

								<td class="px-6 py-2 text-right flex justify-end gap-2">
									<a
										:href="`/print_pr/${pr.id}`"
										class="inline-flex items-center justify-center p-2 rounded-md text-blue-600 hover:bg-blue-50"
										title="View"
									>
										<EyeIcon class="w-4 h-4" />
									</a>

									<button
										@click="openEditModal(pr)"
										class="inline-flex items-center justify-center p-2 rounded-md text-emerald-600 hover:bg-emerald-50"
										title="Revise"
									>
										<ArrowPathIcon class="w-4 h-4" />
									</button>
								</td>
							</tr>

							<tr v-if="!loading && (!items || items.length === 0)">
								<td colspan="6" class="py-10 text-center text-gray-500">
									No PR records found.
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


			<div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
				<div class="bg-white rounded-2xl shadow-lg w-[600px] max-w-[90%] p-6 px-8 text-center max-h-[90vh] overflow-y-auto">
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


		</section>
    </navigation>
</template>
