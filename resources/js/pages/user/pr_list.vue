<script setup>
import { ref, reactive, onMounted } from "vue";
import $ from "jquery";
import "datatables.net";
import axios from "axios";
import { TrashIcon, EyeIcon, PlusIcon, ArrowPathIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/solid';
import navigation from "@/components/layouts/navigation.vue";

const prs = ref([]);
const showDeleteModal = ref(false);
const modalItem = reactive({ id: null, pr_no: "" });

// Fetch PRs from API
const fetchPRs = async () => {
  try {
    const res = await axios.get("/api/prs");
    prs.value = res.data;
  } catch (err) {
    console.error(err);
  }
};

const openDeleteModal = (pr) => {
  Object.assign(modalItem, pr);
  showDeleteModal.value = true;
};

const closeDeleteModal = () => showDeleteModal.value = false;

const deletePR = async () => {
  try {
    await axios.delete(`/api/prs/${modalItem.id}`);
    await fetchPRs();
    showDeleteModal.value = false;
  } catch (err) {
    console.error(err);
  }
};

const openEditModal = (pr) => {
  // Implement PR revision/edit logic
  console.log("Edit PR", pr);
};

onMounted(async () => {
  await fetchPRs();
  $('#prTable').DataTable({
    dom: "<'flex justify-between items-center mb-4'lf>t<'flex justify-end items-center mt-4'p>",
    language: { search: "", searchPlaceholder: "Search PRs...", lengthMenu: "_MENU_ entries per page" },
  });
});
</script>
<template>
    <navigation>
		<section class="items-center justify-center py-8">
			<div class="bg-white rounded-lg shadow-lg max-w-6xl mx-auto">
				<!-- Header -->
				<div class="px-6 py-4 flex justify-between items-center mb-4 bg-white border-b rounded-t-lg">
					<h2 class="text-lg font-semibold">PR List</h2>
					<a
						href="/create_pr"
						class="flex text-sm items-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
						>
						<PlusIcon class="w-5 h-5 mr-1" />
						Create New
					</a>
				</div>

				<!-- Table -->
				<div class="px-6 pt-2 pb-6">
					<table
					id="itemTable"
					class="min-w-full text-sm text-left text-gray-700 border border-gray-200 rounded-lg overflow-hidden"
					>
					<thead class="bg-gray-100 text-gray-900 font-semibold">
						<tr>
						<td class="px-4 py-2 cursor-pointer" width="10%">date_prepared</td>
						<td class="px-4 py-2 cursor-pointer" width="10%">PR No</td>
						<td class="px-4 py-2 cursor-pointer" width="15%">Department</td>
						<td class="px-4 py-2 cursor-pointer" width="15%">Purpose</td>
						<td class="px-4 py-2 cursor-pointer" width="15%">Enduse</td>
						<td class="px-4 py-2 cursor-pointer" width="1%"></td>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200">
						<tr v-for="pr in prs" :key="pr.id" class="hover:bg-gray-50">
						<td class="px-4 py-2">{{ pr.date_prepared }}</td>
						<td class="px-4 py-2">{{ pr.pr_no }}</td>
						<td class="px-4 py-2">{{ pr.department_name }}</td>
						<td class="px-4 py-2">{{ pr.purpose_name }}</td>
						<td class="px-4 py-2">{{ pr.enduse_name }}</td>
						<td class="px-4 py-2 align-top flex justify-end space-x-1">
							<a :href="`/print_pr/${pr.id}`" class="flex items-center justify-center px-1 py-1 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
								<EyeIcon class="w-4 h-4" />
							</a>
							<button @click="openEditModal(item)" title="Revise PR" class="flex items-center justify-center px-1 py-1 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600" >
								<ArrowPathIcon class="w-4 h-4" />
							</button>
							<!-- <button @click="openDeleteModal(item)" class="flex items-center justify-center px-1 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600" >
								<TrashIcon class="w-4 h-4" />
							</button> -->
						</td>
						</tr>
					</tbody>
					</table>
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
