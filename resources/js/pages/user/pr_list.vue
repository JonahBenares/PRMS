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
						<td class="px-4 py-2 cursor-pointer" width="10%">PR No</td>
						<td class="px-4 py-2 cursor-pointer" width="15%">Department</td>
						<td class="px-4 py-2 cursor-pointer" width="15%">Purpose</td>
						<td class="px-4 py-2 cursor-pointer" width="15%">Enduse</td>
						<td class="px-4 py-2 cursor-pointer" width="1%"></td>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200">
						<tr
						v-for="item in items"
						:key="item.id"
						class="hover:bg-gray-50"
						>
						<td class="px-4 py-2 align-top">{{ item.prNo }}</td>
						<td class="px-4 py-2 align-top">{{ item.department }}</td>
						<td class="px-4 py-2 align-top">{{ item.purpose }}</td>
						<td class="px-4 py-2 align-top">{{ item.enduse }}</td>
						<td class="px-4 py-2 align-top flex justify-end space-x-1">
							<a href="print_pr" class="flex items-center justify-center px-1 py-1 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
								<EyeIcon class="w-4 h-4" />
							</a>
							<button @click="openEditModal(item)" title="Revise PR" class="flex items-center justify-center px-1 py-1 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600" >
								<ArrowPathIcon class="w-4 h-4" />
							</button>
							<button @click="openDeleteModal(item)" class="flex items-center justify-center px-1 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600" >
								<TrashIcon class="w-4 h-4" />
							</button>
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

<script setup>
import { ref, onMounted, reactive } from "vue";
import $ from "jquery";
import "datatables.net";
import { TrashIcon, EyeIcon, PlusIcon, ArrowPathIcon,  ExclamationTriangleIcon, XMarkIcon} from '@heroicons/vue/24/solid';
import navigation from "@/components/layouts/navigation.vue";

// Table data

const items = ref([
  { 
    id: 1, 
    prNo: "PR-001", 
    department: "Finance", 
    purpose: "To allocate additional budget for upcoming quarterly projects including vendor payments and operational expenses.", 
    enduse: "For use in company-wide operational activities covering logistics, utilities, and essential supplies." 
  },
  { 
    id: 2, 
    prNo: "PR-002", 
    department: "Human Resources", 
    purpose: "To support the recruitment drive for filling multiple key positions in management and technical departments.", 
    enduse: "For onboarding new employees, covering training, orientation, and initial workplace setup requirements." 
  },
  { 
    id: 3, 
    prNo: "PR-003", 
    department: "Information Technology", 
    purpose: "To upgrade the internal IT infrastructure including servers, cloud systems, and cybersecurity measures.", 
    enduse: "For ensuring smooth company operations, data protection, and future scalability of IT resources." 
  },
]);

// Modals
const showModal = ref(false);
const showDeleteModal = ref(false);
const isEdit = ref(false);
const modalItem = reactive({ id: null, code: "", name: "", category: "" });

const openAddModal = () => {
  isEdit.value = false;
  Object.assign(modalItem, { id: null, code: "", name: "", category: "" });
  showModal.value = true;
};

const openEditModal = (item) => {
  isEdit.value = true;
  Object.assign(modalItem, item);
  showModal.value = true;
};

const closeModal = () => showModal.value = false;

const saveItem = () => {
  if (isEdit.value) {
    const index = items.findIndex(i => i.id === modalItem.id);
    if (index !== -1) items[index] = { ...modalItem };
  } else {
    items.push({ ...modalItem, id: Date.now() });
  }
  showModal.value = false;
};

const openDeleteModal = (item) => {
  Object.assign(modalItem, item);
  showDeleteModal.value = true;
};

const closeDeleteModal = () => showDeleteModal.value = false;

const deleteItem = () => {
  const index = items.findIndex(i => i.id === modalItem.id);
  if (index !== -1) items.splice(index, 1);
  showDeleteModal.value = false;
};

onMounted(() => {
  $('#itemTable').DataTable({
    dom: "<'flex justify-between items-center mb-4'lf>t<'flex justify-end items-center mt-4'p>",
    language: {
      search: "",
      searchPlaceholder: "Search items...",
      lengthMenu: "_MENU_ entries per page",
    },
  });
});
</script>
