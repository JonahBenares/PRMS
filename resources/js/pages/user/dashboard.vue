<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { PlusIcon, XMarkIcon } from "@heroicons/vue/24/solid";
import navigation from "@/components/layouts/navigation.vue";
import pageCard from "@/components/card.vue";
import axios from "axios";
import { useRouter } from 'vue-router';
const router = useRouter();

const companies = ref([]);
const locations = ref([]);
const selectedCompany = ref("");
const selectedCompanyId = ref(null);
const selectedLocation = ref("");
const departments = ref([]);
const selectedDepartment = ref("");
const departmentCode = ref("");
const requestors = ref([]);
const selectedRequestor = ref("");
const urgencies = ref([]);
const selectedUrgency = ref("");
const purposes = ref([]);
const selectedPurpose = ref("");
const enduses = ref([]);
const selectedEnduse = ref("");
const prNumber = ref('');
const datePrepared = ref('');
const notes = ref('');

const selectedPurchase = ref("");

onMounted(async () => {
    fetchCompanies();
    fetchDepartments();
    fetchEmployees();
    fetchUrgencies();
    fetchPurposes();
    fetchEnduses();
    fetchItems();
	const today = new Date();
    // format YYYY-MM-DD for date input
    datePrepared.value = today.toISOString().split('T')[0];
});

watch([selectedCompany, selectedDepartment], async ([company, department]) => {
    if (company) {
        const res = await axios.get('/api/next-pr-series', {
            params: { company_name: company, department_name: department }
        });
        prNumber.value = res.data.pr_number; // empty if department not selected yet
    }
});

	const fetchCompanies = async () => {
	try {
		const response = await axios.get('/api/companies');
		companies.value = response.data.data;
	} catch (err) {
		console.error(err);
	}
	};

watch(selectedCompany, (newVal) => {
    selectedLocation.value = "";
    locations.value = [];

    if (!newVal) return;

    const company = companies.value.find(
        c => c.company_name === newVal
    );

    if (company) {
        selectedCompanyId.value = company.id;
        locations.value = company.companylocation ?? [];
        if (locations.value.length === 1) {
            selectedLocation.value = locations.value[0].location;
        }
    }
});

const fetchDepartments = async () => {
    try {
		const response = await axios.get('/api/departments');
		departments.value = response.data.data;
    } catch (err) {
        console.error(err);
    }
};

watch(selectedDepartment, (newVal) => {
    departmentCode.value = "";

    if (!newVal) return;

    const dept = departments.value.find(
        d => d.department_name === newVal
    );

    if (dept) {
        departmentCode.value = dept.department_code;
    }
});

const fetchEmployees = async () => {
    try {
        const response = await axios.get("/api/employees");
        requestors.value = response.data.data;
    } catch (err) {
        console.error(err);
    }
};

const fetchUrgencies = async () => {
    try {
        const res = await axios.get("/api/urgencies");
        urgencies.value = res.data; // assuming your API returns the array directly
    } catch (err) {
        console.error(err);
    }
};

const fetchPurposes = async () => {
    try {
        const res = await axios.get("/api/purposes");
        purposes.value = res.data;
    } catch (err) {
        console.error(err);
    }
};

const fetchEnduses = async () => {
    try {
        const res = await axios.get("/api/enduses");
        enduses.value = res.data;
    } catch (err) {
        console.error(err);
    }
};

// ===== ITEM TABLE LOGIC =====
const items = ref([]);
const showDropdownIndex = ref(null);
const filteredItems = ref([]);
const noResults = ref(false);


const newRow = () => ({
  id: Date.now() + Math.random(),
  qty: "",
  uom: "",
  searchQuery: "",
  itemCode: "",
  itemName: "",
  pn: "",
  category: "",
  brand: "",
  model: "",
  size: "",
  color: "",
  material: "",
  unit: "",
  serial: "",
  whStock: "",
  dateNeeded: "",
});

const rows = ref([newRow()]);

	const addRow = () => rows.value.push(newRow());
	const removeRow = (index) => rows.value.splice(index, 1);

  const fetchItems = async () => {
      try {
        const res = await axios.get("/api/items");
        items.value = res.data;
      } catch (err) {
        console.error(err);
      }
    };
// Filter items for dropdown based on search
const filterItems = async (index) => {
  const query = rows.value[index].searchQuery || "";

  if (!query) {
    filteredItems.value = [];
    noResults.value = false;
    return;
  }

  try {
    const res = await axios.get("/api/items-search", {
      params: { q: query }
    });

    filteredItems.value = res.data;
    noResults.value = res.data.length === 0;
  } catch (err) {
    console.error(err);
    filteredItems.value = [];
    noResults.value = true;
  }
};

const onSearchInput = (index) => {
  const row = rows.value[index];

  const currentCode = row.variant?.variant_item_code || "";

  // If input is empty OR input no longer matches the selected variant → clear
  if (!row.searchQuery || row.searchQuery !== currentCode) {
    row.itemCode = "";
    row.itemName = "";
    row.pn = "";
    row.uom = "";
    row.brand = "";
    row.model = "";
    row.size = "";
    row.color = "";
    row.material = "";
    row.type = "";
    row.variant = null;
  }

  // Always call filterItems to show dropdown while typing
  filterItems(index);
};

const selectItem = (rowIndex, item) => {
  const variant = item.item_variants?.[0] || {};

  rows.value[rowIndex] = {
    ...rows.value[rowIndex],
    itemCode: variant.variant_item_code || item.item_code,
    itemName: item.item_description,
    pn: variant.part_no || "",
    uom: variant.uom || "", // ✅ get uom from variant
    brand: variant.brand || "",
    model: variant.model || "",
    size: variant.size || "",
    color: variant.color || "",
    material: variant.material || "",
    type: variant.type || "",
    searchQuery: variant.variant_item_code || item.item_code,
    variant: variant, // optional: store selected variant for future use
  };

  filteredItems.value = [];
  showDropdownIndex.value = null;
};


const saveAndProceed  = async () => {
    try {
        const itemsData = rows.value.map(row => ({
            qty: row.qty,
            itemCode: row.itemCode,
            item_variant_id: row.variant?.id,
            uom: row.uom,
            brand: row.brand,
            model: row.model,
            size: row.size,
            color: row.color,
            material: row.material,
            type: row.type,
            whStock: row.whStock,
            dateNeeded: row.dateNeeded
        }));

        const payload = {
            company_id: selectedCompanyId.value,
            company_code: companies.value.find(c => c.company_name === selectedCompany.value)?.company_code,
            company_name: selectedCompany.value,
            pr_no: prNumber.value,
            location_id: locations.value.find(l => l.location === selectedLocation.value)?.id ?? null,
            location_name: selectedLocation.value,
            date_prepared: datePrepared.value,
            department_id: departments.value.find(d => d.department_name === selectedDepartment.value)?.id ?? null,
            department_name: selectedDepartment.value,
            department_code: departmentCode.value,
            requestor_id: requestors.value.find(r => r.employee_name === selectedRequestor.value)?.id ?? null,
            requestor_name: selectedRequestor.value,
            urgency_id: urgencies.value.find(u => u.qualifier_name === selectedUrgency.value)?.id ?? null,
            urgency_name: selectedUrgency.value,
            purpose_id: purposes.value.find(p => p.purpose_name === selectedPurpose.value)?.id ?? null,
            purpose_name: selectedPurpose.value,
            enduse_id: enduses.value.find(e => e.enduse_name === selectedEnduse.value)?.id ?? null,
            enduse_name: selectedEnduse.value,
            notes: notes.value, // now it saves
            status: 'Save',
            items: itemsData
        };

        const token = localStorage.getItem('token'); // 🔑 get token

        const res = await axios.post('/api/pr/save', payload, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        });

        if (res.data.success) {
            const prId = res.data.pr_id;

            // Redirect to printing page with PR ID as query or param
            router.push(`/print_pr/${prId}`);
        }
    } catch (err) {
        console.error(err);
        alert('Failed to save PR');
    }
};
</script>
<template> 
	<navigation>
		<pageCard>
			<div class="flex justify-start mb-4">
				<span class="w-2 bg-blue-700 mb-2 mt-0 mr-5 rounded"></span>
				<div>
					<h2 class="text-2xl font-bold mb-2">PR FORM</h2>
				</div>
				<!-- <div class="px-2 mt-1">
					<span class="text-emerald-500 text-white font-semibold">Revise</span>
				</div> -->
			</div>
			<div class="mb-3">
				<table class="w-full text-sm ">
					<thead>
						<tr>
							<td class="border px-1">Company</td>
							<td class="border w-[37.5%]">
							<select v-model="selectedCompany" class="outline-none w-full">
								<option value="">Select Company</option>
								<option v-for="comp in companies" :key="comp.id" :value="comp.company_name">
									{{ comp.company_name }}
								</option>
							</select>
							</td>
							<td class="border " colspan="2"></td>
						</tr>
						<tr>
						<td class="border px-1">Location</td>
						<td class="border w-[37.5%]">
							<select
								v-model="selectedLocation"
								class="outline-none w-full"
								:disabled="!selectedCompany"
							>
								<option value="">Select Location</option>
								<option
									v-for="loc in locations"
									:key="loc.id"
									:value="loc.location"
								>
									{{ loc.location }}
								</option>
							</select>
						</td>
							<td class="border px-1">Department</td>
							<td class="border w-[37.5%]">
							<select v-model="selectedDepartment" class="outline-none w-full">
								<option value="">Select Department</option>
								<option
									v-for="dept in departments"
									:key="dept.id"
									:value="dept.department_name"
								>
									{{ dept.department_name }}
								</option>
							</select>
							</td>
						</tr>
						<tr>
							<td class=" px-1">PR No</td>
							<td class=" w-[37.5%]">{{ prNumber }}</td>
							<td class="border px-1">Department Code</td>
							<td class="border w-[37.5%]">
								<input type="text" class="outline-none w-full px-1" v-model="departmentCode" readonly>
							</td>
						</tr>
						<tr>
							<td class="border px-1" width="">Date Prepared</td>
							<td class="border" width="37.5%"><input type="date" class="outline-none w-full px-1" v-model="datePrepared" readonly></td>
							<td class="border px-1">Requestor</td>
						<td class="border w-[37.5%]">
							<select
								v-model="selectedRequestor"
								class="outline-none w-full px-1 disabled:bg-gray-100 disabled:cursor-not-allowed"
							>
								<option value="">Select Requestor</option>
								<option
									v-for="emp in requestors"
									:key="emp.id"
									:value="emp.employee_name"
								>
									{{ emp.employee_name }}
								</option>
							</select>
						</td>
						</tr>
						<tr>
							<td class="border px-1" width=""></td>
							<td class="border" width="37.5%"></td>
							<td class="border px-1">Urgency</td>
						<td class="border w-[37.5%]">
							<select
								v-model="selectedUrgency"
								class="outline-none w-full px-1 disabled:bg-gray-100 disabled:cursor-not-allowed"
							>
								<option value="">Select Urgency</option>
								<option
									v-for="urgency in urgencies"
									:key="urgency.id"
									:value="urgency.qualifier_name"
								>
									{{ urgency.qualifier_name }}
								</option>
							</select>
						</td>
						</tr>
						<tr>
						<td class="border px-1">Purpose</td>
						<td class="border" colspan="3">
							<select v-model="selectedPurpose" class="outline-none w-full px-1">
								<option value="">Select Purpose</option>
								<option v-for="purpose in purposes" :key="purpose.id" :value="purpose.purpose_name">
									{{ purpose.purpose_name }}
								</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="border px-1">End-use</td>
						<td class="border" colspan="3">
							<select v-model="selectedEnduse" class="outline-none w-full px-1">
								<option value="">Select End-use</option>
								<option v-for="enduse in enduses" :key="enduse.id" :value="enduse.enduse_name">
									{{ enduse.enduse_name }}
								</option>
							</select>
						</td>
					</tr>
					</thead>
				</table>
			</div>
			<div class="border border-gray-200 rounded-xl overflow-hidden text-sm mb-3">
				<table class="w-full  text-sm">
					<thead class="bg-blue-50">
						<tr>
							<td class="!border-x !border-b font-semibold px-1 py-1 text-center" width="3%">#</td>
							<td class="!border-x !border-b font-semibold px-1 py-1 text-center" width="5%">Qty</td>
							<td class="!border-x !border-b font-semibold px-1 py-1" width="15%">Item Code</td>
							<td class="!border-x !border-b font-semibold px-1 py-1 text-center" width="5%">UOM</td>
							<td class="!border-x !border-b font-semibold px-1 py-1">Description</td>
							<td class="!border-x !border-b font-semibold px-1 py-1" width="8%">WH Stock</td>
							<td class="!border-x !border-b font-semibold px-1 py-1" width="10%">Date Needed</td>
							<td class="!border-x !border-b font-semibold px-1 py-1" width="2%" align="center">
								<button @click="addRow" class="bg-blue-600 p-1 rounded-full">
									<PlusIcon class="size-4 text-white" />
								</button>
							</td>
						</tr>
						</thead>

						<thead>
						<tr v-for="(row, index) in rows" :key="row.id">
							<td class="align-top !border-x !border-b px-1 text-center">
							{{ index + 1 }}
							</td>
							<td class="align-top !border-x !border-b">
								<input v-model="row.qty" class="outline-none w-full px-1 text-center" placeholder="00" type="number" />
							</td>
							<td class="align-top !border-x !border-b relative">
								<div class="relative">
									<input
									v-model="row.searchQuery"
									@input="onSearchInput(index)"
									@focus="showDropdownIndex = index"
									@blur="showDropdownIndex = null"
									class="outline-none focus:outline-none border-none focus:ring-0 w-full px-1"
									placeholder="Search Item Code / Name / Variant"
									type="text"
									/>
									<!-- Dropdown -->
									<ul v-if="showDropdownIndex === index" class="fixed z-50 bg-white border border-gray-300 w-64 max-h-40 overflow-y-auto shadow-lg rounded">
									<li
									v-for="(item, itemIndex) in filteredItems.filter(i =>
										i.item_variants?.length &&
										i.item_variants[0].variant_item_code?.trim()
									)"
									:key="item.id"
									@mousedown.prevent="selectItem(index, item)"
									class="px-2 py-1 hover:bg-blue-100 cursor-pointer text-sm"
									>
									<strong>{{ item.item_variants[0].variant_item_code }}</strong> —
									{{ item.item_description }}
									(PN: {{ item.item_variants[0].part_no }})
									</li>
									  <!-- NO RESULTS -->
										<li
											v-if="noResults"
											class="px-2 py-2 text-gray-400 italic text-sm"
										>
											No results found
										</li>
									</ul>
								</div>
							</td>
							<td class="align-top !border-x !border-b">
								<input v-model="row.uom" class="outline-none w-full text-center" readonly />
							</td>
							<td class="align-top !border-x !border-b">
						<div v-if="row.itemCode" class="space-y-1 px-2 text-sm">
						<!-- Item name + part number -->
						<div class="font-semibold">
							{{ row.itemName }}
							<span v-if="row.pn" class="text-gray-600">
							({{ row.pn }})
							</span>
						</div>
						<div v-if="row.brand?.trim()" class="flex">
							<span class="pr-2 w-20 text-gray-500">Brand:</span>
							<span>{{ row.brand }}</span>
						</div>

						<div v-if="row.model?.trim()" class="flex">
							<span class="pr-2 w-20 text-gray-500">Model:</span>
							<span>{{ row.model }}</span>
						</div>

						<div v-if="row.size?.trim()" class="flex">
							<span class="pr-2 w-20 text-gray-500">Size:</span>
							<span>{{ row.size }}</span>
						</div>

						<div v-if="row.color?.trim()" class="flex">
							<span class="pr-2 w-20 text-gray-500">Color:</span>
							<span>{{ row.color }}</span>
						</div>

						<div v-if="row.material?.trim()" class="flex">
							<span class="pr-2 w-20 text-gray-500">Material:</span>
							<span>{{ row.material }}</span>
						</div>

						<div v-if="row.unit?.trim()" class="flex">
							<span class="pr-2 w-20 text-gray-500">Unit:</span>
							<span>{{ row.unit }}</span>
						</div>

						<div v-if="row.type?.trim()" class="flex">
							<span class="pr-2 w-20 text-gray-500">Type:</span>
							<span>{{ row.type }}</span>
						</div>
						</div>
						<!-- Default blank state -->
						<div v-else class="text-gray-400 italic px-2 py-2">
						Select an Item Code to view specs
						</div>
              </td>
							<td class="align-top !border-x !border-b px-1">
							<input v-model="row.whStock" type="text" class="outline-none w-full" placeholder="" />
							</td>
							<td class="align-top !border-x !border-b px-1">
							<input v-model="row.dateNeeded" type="date" class="outline-none w-full" />
							</td>
							<td class="align-top !border-x !border-b px-1">
								<!-- DELETE BUTTON -->
								<button @click="removeRow(index)" class="bg-red-600 p-1 rounded-full">
									<XMarkIcon class="size-4 text-white" />
								</button>
							</td>
						</tr>
					</thead>
				</table>
			</div>
			<table class="w-full text-sm !border-b border-x mt-1">
				<thead>
					<tr>
						<td class="border p-0" colspan="2">
							<span class="px-1 align-top">Notes</span>
							<textarea v-model="notes" class="m-0 w-full p-1 outline-none" rows="1"></textarea>
						</td>
					</tr>
				</thead>
			</table>
			<div v-if="selectedCompany === 'CENPRI' && selectedPurchase === 'Bago'" class="border-b border-x px-16 py-5">
				<table class="w-full  text-sm">
					<thead>
						<tr>
							<td width="10%">Prepared By:</td>
							<td class="border-b">
								<select name="" class="outline-none w-full px-1 font-semibold" id="">
									<option value="">Select Employee</option>
									<option value=""></option>
									<option value=""></option>
								</select>
							</td>
							<td width="5%"></td>
							<td width="20%">Specifications Verified By:</td>
							<td class="border-b">
								<select name="" class="outline-none w-full px-1 font-semibold" id="">
									<option value="">Select Employee</option>
									<option value=""></option>
									<option value=""></option>
								</select>
							</td>
						</tr>
						<tr>
							<td width=""></td>
							<td><input type="text" class="outline-none w-full mb-3 px-2" value="Position"></td>
							<td></td>
							<td width=""></td>
							<td><input type="text" class="outline-none w-full mb-3 px-2" value="Position"></td>
						</tr>

						<tr>
							<td width="10%">WH Check By:</td>
							<td class="border-b">
								<select name="" class="outline-none w-full px-1 font-semibold" id="">
									<option value="">Select Employee</option>
									<option value=""></option>
									<option value=""></option>
								</select>
							</td>
							<td width="5%"></td>
							<td width="20%">Recommending Approval:</td>
							<td class="border-b">
								<select name="" class="outline-none w-full px-1 font-semibold" id="">
									<option value="">Select Employee</option>
									<option value=""></option>
									<option value=""></option>
								</select>
							</td>
						</tr>
						<tr>
							<td width=""></td>
							<td><input type="text" class="outline-none w-full mb-3 px-2" value="Position"></td>
							<td></td>
							<td width=""></td>
							<td><input type="text" class="outline-none w-full mb-3 px-2" value="Position"></td>
						</tr>

						<tr>
							<td width="10%"></td>
							<td class=""></td>
							<td></td>
							<td>Approved By:</td>
							<td class="border-b">
								<select name="" class="outline-none w-full px-1 font-semibold" id="">
									<option value="">Select Employee</option>
									<option value=""></option>
									<option value=""></option>
								</select>
							</td>
						</tr>
						<tr>
							<td width=""></td>
							<td></td>
							<td></td>
							<td width=""></td>
							<td><input type="text" class="outline-none w-full mb-3 px-2" value="Position"></td>
						</tr>
					</thead>
				</table>
			</div>
			<div v-if="selectedCompany === 'CENPRI' && selectedPurchase === 'Bacolod'" class="border-b border-x pl-4 pr-12 py-5">
				<div class="grid grid-cols-2 gap-x-6 gap-y-4 mt-0">
					<div class="flex items-start">
						<div class="w-40 text-sm font-medium text-gray-700 pt-1 pr-4 text-right">
						Prepared By:
						</div>
						<div class="flex-1">
						<select class="w-full text-sm border-b outline-none px-2 py-1">
							<option value="">Select Employee</option>
							<option>John Doe</option>
							<option>Jane Smith</option>
						</select>
						<input
							type="text"
							placeholder="Position"
							class="w-full mt-1 text-sm px-2 py-1 outline-none"
						/>
						</div>
					</div>

					<div class="flex items-start">
						<div class="w-40 text-sm font-medium text-gray-700 pt-1 pr-4 text-right">
						Reviewed By:
						</div>
						<div class="flex-1">
						<select class="w-full text-sm border-b outline-none px-2 py-1">
							<option value="">Select Employee</option>
						</select>
						<input
							type="text"
							placeholder="Position"
							class="w-full mt-1 text-sm px-2 py-1 outline-none"
						/>
						</div>
					</div>

					<div class="flex items-start">
						<div class="w-40 text-sm font-medium text-gray-700 pt-1 pr-4 text-right">
						Reviewed By:
						</div>
						<div class="flex-1">
						<select class="w-full text-sm border-b outline-none px-2 py-1">
							<option value="">Select Employee</option>
						</select>
						<input
							type="text"
							placeholder="Position"
							class="w-full mt-1 text-sm px-2 py-1 outline-none"
						/>
						</div>
					</div>

					<div class="flex items-start">
						<div class="w-40 text-sm font-medium text-gray-700 pt-1 pr-4 text-right">
						Reviewed By:
						</div>
						<div class="flex-1">
						<select class="w-full text-sm border-b outline-none px-2 py-1">
							<option value="">Select Employee</option>
						</select>
						<input
							type="text"
							placeholder="Position"
							class="w-full mt-1 text-sm px-2 py-1 outline-none"
						/>
						</div>
					</div>
					
					<div class="flex items-start">
						<div class="w-40 text-sm font-medium text-gray-700 pt-1 pr-4 text-right">
						Reviewed and Checked By:
						</div>
						<div class="flex-1">
						<select class="w-full text-sm border-b outline-none px-2 py-1">
							<option value="">Select Employee</option>
						</select>
						<input
							type="text"
							placeholder="Position"
							class="w-full mt-1 text-sm px-2 py-1 outline-none"
						/>
						</div>
					</div>

					<div class="flex items-start">
						<div class="w-40 text-sm font-medium text-gray-700 pt-1 pr-4 text-right">
						Approved By:
						</div>
						<div class="flex-1">
						<select class="w-full text-sm border-b outline-none px-2 py-1">
							<option value="">Select Employee</option>
						</select>
						<input
							type="text"
							placeholder="Position"
							class="w-full mt-1 text-sm px-2 py-1 outline-none"
						/>
						</div>
					</div>
				</div>
				
			</div>
			
			<div class="mt-4 flex justify-end flex-wrap gap-2">
				<!-- Instead of <a>, we use buttons that trigger modal -->
				<a class="inline-flex items-center rounded-lg px-4 py-3 text-sm font-medium text-lg border border-blue-300 text-blue-900 hover:bg-blue-100">
					Save as Draft
				</a>
				<button
					@click="saveAndProceed"
					:disabled="rows.length === 0 || rows.every(row => !row.itemCode)" 
					class="inline-flex items-center rounded-lg px-4 py-3 text-sm font-medium text-lg shadow-sm bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600/50"
				>Proceed</button>
			</div>
		</pageCard>
	</navigation>
</template>

<style scoped>
/* Add component-specific styles here if needed */
</style>
