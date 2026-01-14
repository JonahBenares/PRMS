<script setup>
import { ref, reactive, onMounted, watch } from "vue";
import { TrashIcon, PlusIcon, ExclamationTriangleIcon, XMarkIcon, PencilSquareIcon } from '@heroicons/vue/24/solid';
import navigation from "@/components/layouts/navigation.vue";

const companies = ref([]);
const logoFile = ref(null);
const logoPreview = ref(null);

// Fetch departments from API
const fetchCompanies = async () => {
    try {
        const response = await axios.get("/api/companies");
        companies.value = response.data;
    } catch (err) {
        console.error(err);
    }
};

const handleLogoUpload = (e) => {
  const file = e.target.files[0];
  if (!file) return;

  modalItem.company_logo = file;
  logoPreview.value = URL.createObjectURL(file);
};

onMounted(async () => {
	fetchCompanies()
})

// Modal States
const showModal = ref(false);
const showDeleteModal = ref(false);
const isEdit = ref(false);
const isSub = ref(false);
const parentCompany = ref(null);

const modalItem = reactive({
  id: null,

  company_name: "",
  company_code: "",
  company_logo: null,

  location: "",
  address: "",
  telephone: "",
  telefax: "",
});

const openAddModal = (parent = null) => {
  isEdit.value = false;
  isSub.value = !!parent;
  parentCompany.value = parent;

  Object.assign(modalItem, {
    id: null,
    company_name: "",
    company_code: "",
    company_logo: null,
    location: "",
    address: "",
    telephone: "",
    telefax: "",
  });

  showModal.value = true;
};

const openEditModal = (item, sub = false) => {
  isEdit.value = true;
  isSub.value = sub;

  Object.assign(modalItem, { ...item });

  // 👇 THIS IS THE FIX
  if (!sub && item.company_logo) {
    logoPreview.value = `/storage/logos/${item.company_logo}`;
  } else {
    logoPreview.value = null;
  }

  logoFile.value = null;
  showModal.value = true;
};

// Save Company or Location
const saveCompany = async () => {
  try {
    if (isSub.value) {
      // Save location
      const payload = {
        company_id: parentCompany.value.id,
        location: modalItem.location,
        address: modalItem.address,
        telephone: modalItem.telephone,
        telefax: modalItem.telefax
      };

      if (isEdit.value) {
        await axios.put(`/api/company_locations/${modalItem.id}`, payload);
      } else {
        await axios.post("/api/company_locations", payload);
      }

    } else {
      // Save company
      const formData = new FormData();
      formData.append("company_name", modalItem.company_name);
      formData.append("company_code", modalItem.company_code);

      // ✅ Only append logo if it's a File (new upload)
      if (modalItem.company_logo instanceof File) {
        formData.append("company_logo", modalItem.company_logo);
      }

      const url = isEdit.value 
        ? `/api/companies/${modalItem.id}?_method=PUT` 
        : "/api/companies";

      await axios.post(url, formData, {
        headers: { "Content-Type": "multipart/form-data" }
      });
    }

    showModal.value = false;
    await fetchCompanies();
  } catch (err) {
    console.error("Save failed:", err.response?.data || err);
  }
}

const openDeleteModal = (item, sub = false, parent = null) => {
  isSub.value = sub;
  parentCompany.value = parent;
  Object.assign(modalItem, { ...item });
  showDeleteModal.value = true;
};

const deleteCompany = async () => {
  try {
    if (isSub.value) {
      await axios.delete(`/api/company_locations/${modalItem.id}`);
    } else {
      await axios.delete(`/api/companies/${modalItem.id}`);
    }

    showDeleteModal.value = false;
    await fetchCompanies();
  } catch (err) {
    console.error("Delete failed:", err);
  }
};

const closeModal = () => {
  showModal.value = false;
  logoFile.value = null;
  logoPreview.value = null;
};

const closeDeleteModal = () => showDeleteModal.value = false;
</script>
<template>
	<navigation>
		<section class="items-center justify-center py-8">
		<div class="bg-white rounded-lg shadow-lg max-w-6xl mx-auto">

			<!-- Header -->
			<div class="px-6 py-4 flex justify-between items-center mb-4 bg-white border-b rounded-t-lg">
			<h2 class="text-lg font-semibold">Company List</h2>

			<button 
				@click="openAddModal()"
				class="flex text-sm items-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
			>
				<PlusIcon class="w-5 h-5 mr-1" />
				Add Company
			</button>
			</div>

			<!-- Table -->
			<div class="px-6 pt-2 pb-6">
				<table
					class="min-w-full text-sm text-left text-gray-700 border border-gray-200 rounded-lg overflow-hidden"
					>
					<thead class="bg-gray-100 text-gray-900 font-semibold">
						<tr>
						<td class="px-4 py-2 w-40">Company</td>
						<td class="px-4 py-2 w-32">Code</td>
						<td class="px-4 py-2 w-28">Logo</td>
						<td class="px-4 py-2">Location</td>
						<td class="px-4 py-2 w-40">Address</td>
						<td class="px-4 py-2 w-32">Telephone</td>
						<td class="px-4 py-2 w-32">Telefax</td>
						<td class="px-4 py-2 w-20"></td>
						</tr>
					</thead>

					<tbody class="divide-y divide-gray-200">
						<!-- COMPANY ROW -->
						<template v-for="company in companies" :key="company.id">
						<tr class="bg-gray-50">
							<td class="px-4 py-2 font-semibold">{{ company.company_name }}</td>
							<td class="px-4 py-2">{{ company.company_code }}</td>

							<!-- LOGO PREVIEW -->
							<td class="px-4 py-2">
							<img 
								v-if="company.company_logo"
								:src="`/storage/logos/${company.company_logo}`"
								class="h-10 w-10 object-cover rounded"
							/>
							<span v-else class="text-gray-400 text-xs">No Logo</span>
							</td>

							<!-- EMPTY FIELDS FOR SPAN -->
							<td></td>
							<td></td>
							<td></td>
							<td></td>

							<td class="px-4 py-2 flex space-x-1">
							<button @click="openEditModal(company, false)" class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-700">
								<PencilSquareIcon class="w-4 h-4"/>
							</button>
							<button @click="openDeleteModal(company, false)" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-700">
								<TrashIcon class="w-4 h-4"/>
							</button>
							<button @click="openAddModal(company)" class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-700">+</button>
							</td>
						</tr>

						<!-- LOCATION ROWS -->
						<tr v-for="loc in company.companylocation" :key="loc.id">
							<td></td>
							<td></td>
							<td></td>

							<td class="px-4 py-2 text-gray-600">↳ {{ loc.location }}</td>
							<td class="px-4 py-2">{{ loc.address }}</td>
							<td class="px-4 py-2">{{ loc.telephone }}</td>
							<td class="px-4 py-2">{{ loc.telefax }}</td>

							<td class="px-4 py-2 flex space-x-1">
							<button @click="openEditModal(loc, true)" class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-700">
								<PencilSquareIcon class="w-4 h-4"/>
							</button>
							<button @click="openDeleteModal(loc, true, company)" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-700">
								<TrashIcon class="w-4 h-4"/>
							</button>
							</td>
						</tr>
						</template>
					</tbody>

					</table>

			</div>
		</div>

		<!-- Add/Edit Modal -->
		<div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center">
			<div class="bg-black/50 w-full h-full" @click="closeModal"></div>

			<div class="bg-white rounded-lg w-[600px] absolute top-32">
			<div class="flex justify-between px-6 py-4 bg-gray-100 rounded-t-lg">
				<h3 class="text-lg font-semibold">
				{{ isEdit ? 'Edit ' + (isSub ? 'Location' : 'Company') : 'Add ' + (isSub ? 'Location' : 'Company') }}
				</h3>
				<button @click="closeModal"><XMarkIcon class="size-4" /></button>
			</div>

			<div class="flex flex-col gap-3 px-6 py-4">

				<!-- COMPANY FIELDS -->
				<div v-if="!isSub">
				<label class="text-sm">Company Name</label>
				<input v-model="modalItem.company_name" class="border px-3 py-2 rounded text-sm w-full"/>

				<label class="text-sm">Company Code</label>
				<input v-model="modalItem.company_code" class="border px-3 py-2 rounded text-sm w-full"/>

				<label class="text-sm">Company Logo</label>
				<div class="flex items-center gap-4">
					<div
						class="h-20 w-20 border rounded flex items-center justify-center overflow-hidden bg-gray-50"
					>
						<img
						v-if="logoPreview"
						:src="logoPreview"
						class="h-full w-full object-cover"
						/>
						<span v-else class="text-xs text-gray-400">No Image</span>
					</div>

					<input
						type="file"
						accept="image/*"
						@change="handleLogoUpload"
						class="border px-3 py-2 rounded text-sm w-full"
					/>
					</div>
				</div>

				<!-- LOCATION FIELDS -->
				<div v-if="isSub">
				<label class="text-sm">Location Name</label>
				<input v-model="modalItem.location" class="border px-3 py-2 rounded text-sm w-full"/>

				<label class="text-sm">Address</label>
				<input v-model="modalItem.address" class="border px-3 py-2 rounded text-sm w-full"/>

				<label class="text-sm">Telephone Number</label>
				<input v-model="modalItem.telephone" class="border px-3 py-2 rounded text-sm w-full"/>

				<label class="text-sm">Telefax</label>
				<input v-model="modalItem.telefax" class="border px-3 py-2 rounded text-sm w-full"/>
				</div>
			</div>

			<div class="flex justify-end gap-2 px-6 py-4">
				<button @click="closeModal" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-400">Cancel</button>
				<button @click="saveCompany" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
				{{ isEdit ? 'Update' : 'Add' }}
				</button>
			</div>
			</div>
		</div>

		<!-- Delete Modal -->
		<div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
			<div class="bg-white rounded-2xl shadow-lg w-[600px] max-w-[90%] p-6 px-8 text-center">
			<ExclamationTriangleIcon class="w-20 h-20 text-red-600 mx-auto"/>
			<h3 class="text-xl font-bold text-gray-800 mb-4">Confirm Deletion</h3>
			<p class="text-gray-600">
				Are you sure you want to delete <b>{{ modalItem.company_name || modalItem.location_name }}</b>?
			</p>

			<div class="flex justify-center gap-3 mt-6">
				<button @click="closeDeleteModal" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-400">Cancel</button>
				<button @click="deleteItem" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
			</div>
			</div>
		</div>
		</section>
	</navigation>
</template>
