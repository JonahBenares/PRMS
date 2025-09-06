<template> 
	<navigation>
		<section class="items-center justify-center py-8">
			<div class="w-full bg-white shadow-lg rounded-lg p-8 max-w-6xl mx-auto">
				<h2 class="text-xl font-semibold mb-2 ">PR FORM</h2>
				<table class="w-full text-sm !border-b border-x">
					<tbody>
						<tr>
							<td class="border px-1">Company</td>
							<td class="border w-[37.5%]">
								<select v-model="selectedCompany" class="outline-none w-full">
								<option value="">Select Company</option>
								<option value="ENERGREEN">ENERGREEN</option>
								<option value="CENPRI">CENPRI</option>
								<option value="CPGC">CPGC</option>
								</select>
							</td>
							<td class="border " colspan="2"></td>
						</tr>
						<tr>
							<td class="border px-1">Location</td>
							<td class="border w-[37.5%]">
								<select v-model="selectedPurchase" class="outline-none w-full">
									<option value="">Select Location</option>
									<option v-for="req in purchaseRequests" :key="req" :value="req">
									{{ req }}
									</option>
								</select>
							</td>
							<td class="border px-1">Department</td>
							<td class="border w-[37.5%]">
								<select v-model="selectedDepartment" class="outline-none w-full">
								<option value="">Select Department</option>
								<option v-for="dept in departments" :key="dept" :value="dept">
									{{ dept }}
								</option>
								</select>
							</td>
						</tr>
						<tr>
							<td class="border px-1">PR Number</td>
							<td class="border w-[37.5%]">
								<input type="text" class="outline-none w-full px-1">
							</td>
							<td class="border px-1">Department Code</td>
							<td class="border w-[37.5%]">
								<input type="text" class="outline-none w-full px-1" v-model="departmentCode">
							</td>
						</tr>
						<tr>
							<td class="border px-1" width="">Date Prepared</td>
							<td class="border" width="37.5%"><input type="date" class="outline-none w-full px-1"></td>
							<td class="border px-1" width="">Requestor</td>
							<td class="border" width="37.5%">
								<select name="" class="outline-none w-full px-1" id="">
									<option value=""></option>
									<option value=""></option>
									<option value=""></option>
								</select>
							</td>
						</tr>
						<tr>
							<td class="border px-1" width="">Date Issued</td>
							<td class="border" width="37.5%"><input type="date" class="outline-none w-full px-1"></td>
							<td class="border px-1" width="">Urgency</td>
							<td class="border" width="35%">
								<select name="" class="outline-none w-full px-1" id="">
									<option value="">1-Week</option>
									<option value="">2-Month</option>
									<option value="">3-Quarter</option>
									<option value="">4-Year</option>
								</select>
							</td>
						</tr>
						<tr>
							<td class="border px-1" width="">Purpose</td>
							<td class="border" colspan="3">
								<select name="" class="outline-none w-full" id="">
									<option value=""></option>
									<option value=""></option>
									<option value=""></option>
								</select>
							</td>
						</tr>
						<tr>
							<td class="border px-1" width="">End-use</td>
							<td class="border" colspan="3">
								<select name="" class="outline-none w-full" id="">
									<option value=""></option>
									<option value=""></option>
									<option value=""></option>
								</select>
							</td>
						</tr>
					</tbody>
				</table>
				<table class="w-full border-y border-x text-sm mt-1">
					<thead class="bg-blue-50">
						<tr>
							<td class="!border-x !border-b font-semibold px-1 text-center" width="3%">#</td>
							<td class="!border-x !border-b font-semibold px-1 text-center" width="5%">Qty</td>
							<td class="!border-x !border-b font-semibold px-1 text-center" width="5%">UOM</td>
							<td class="!border-x !border-b font-semibold px-1" width="15%">Item Code</td>
							<td class="!border-x !border-b font-semibold px-1">Description</td>
							<td class="!border-x !border-b font-semibold px-1" width="8%">WH Stock</td>
							<td class="!border-x !border-b font-semibold px-1" width="10%">Date Needed</td>
							<td class="!border-x !border-b font-semibold px-1" width="2%" align="center">
								<button @click="addRow" class="bg-blue-600 p-1 rounded-full">
									<PlusIcon class="size-4 text-white" />
								</button>
							</td>
						</tr>
						</thead>

						<tbody>
						<tr v-for="(row, index) in rows" :key="row.id">
							<td class="align-top !border-x !border-b px-1 text-center">
							{{ index + 1 }}
							</td>
							<td class="align-top !border-x !border-b">
								<input v-model="row.qty" class="outline-none w-full px-1 text-center" placeholder="00" type="number" />
							</td>
							<td class="align-top !border-x !border-b">
								<input v-model="row.uom" class="outline-none w-full px-1 text-center" placeholder="pc/s" type="text" />
							</td>
							<td class="align-top !border-x !border-b">
								<div class="relative w-full">
									<input
									v-model="row2.itemCodeSearch"
									@focus="showItemCodeList = true"
									class="outline-none w-full px-2 py-1"
									placeholder="Enter or select item code"
									type="text"
									/>

									<!-- Dropdown -->
									<ul
									v-if="showItemCodeList && filteredItemCodes.length"
									class="absolute z-10 bg-white border rounded shadow mt-1 w-full max-h-40 overflow-auto"
									>
									<li
										v-for="item2 in filteredItemCodes"
										:key="item2.code"
										@mousedown.prevent="selectItemCode(item2)"
										class="px-2 py-1 hover:bg-blue-100 cursor-pointer"
									>
										{{ item2.code }}
									</li>
									</ul>
								</div>
							</td>
							<td class="align-top !border-x !border-b">
								<div class="relative w-full">
									<!-- Searchable Input -->
									<input
									v-model="search"
									@focus="showList = true"
									class="outline-none w-full px-2 py-1 font-semibold"
									placeholder="Enter or select part number"
									type="text"
									/>

									<!-- Dropdown -->
									<ul
									v-if="showList && filteredItems.length"
									class="absolute z-10 bg-white border rounded shadow mt-1 w-full max-h-40 overflow-auto"
									>
									<li
										v-for="item in filteredItems"
										:key="item.code"
										@mousedown.prevent="selectItem(item)" 
										class="px-2 py-1 hover:bg-blue-100 cursor-pointer"
									>
										{{ item.code }} - {{ item.name }}
									</li>
									</ul>
								</div>
								<div class="flex justify-start px-2">
									<span class="pr-2">Category:</span>
									<input v-model="row.category" type="text" class="outline-none w-full" />
								</div>
								<div class="flex justify-start px-2">
									<span class="pr-2">Brand:</span>
									<input v-model="row.brand" type="text" class="outline-none w-full" />
								</div>
								<div class="flex justify-start px-2">
									<span class="pr-2">Model:</span>
									<input v-model="row.model" type="text" class="outline-none w-full" />
								</div>
								<div class="flex justify-start px-2">
									<span class="pr-2">Size:</span>
									<input v-model="row.size" type="text" class="outline-none w-full" />
								</div>
								<div class="flex justify-start px-2">
									<span class="pr-2">Color:</span>
									<input v-model="row.color" type="text" class="outline-none w-full" />
								</div>
								<div class="flex justify-start px-2">
									<span class="pr-2">Material:</span>
									<input v-model="row.material" type="text" class="outline-none w-full" />
								</div>
								<div class="flex justify-start px-2">
									<span class="pr-2">Unit:</span>
									<input v-model="row.unit" type="text" class="outline-none w-full" />
								</div>
								<div class="flex justify-start px-2">
									<span class="pr-2">Serial:</span>
									<input v-model="row.serial" type="text" class="outline-none w-full" />
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
					</tbody>
				</table>
				<div v-if="selectedCompany === 'CENPRI' && selectedPurchase === 'Bago'" class="border-b border-x px-20 py-5">
					<table class="w-full  text-sm">
						<tbody>
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
								<td width="20%">Specifications Verified by:</td>
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
								<td width="10%">Planned By:</td>
								<td class="border-b">
									<select name="" class="outline-none w-full px-1 font-semibold" id="">
										<option value="">Select Employee</option>
										<option value=""></option>
										<option value=""></option>
									</select>
								</td>
								<td width="5%"></td>
								<td width="20%">Request Verified By:</td>
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
								<td width="10%">Checked By:</td>
								<td class="border-b">
									<select name="" class="outline-none w-full px-1 font-semibold" id="">
										<option value="">Select Employee</option>
										<option value=""></option>
										<option value=""></option>
									</select>
								</td>
								<td width="5%"></td>
								<td width="20%">Processed By:</td>
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
								<td width="10%">Verified By:</td>
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
								<td></td>
								<td></td>
								<td></td>
								<td>Approved by:</td>
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
						</tbody>
					</table>
				</div>
				<div v-if="selectedCompany === 'CENPRI' && selectedPurchase === 'Bacolod'" class="border-b border-x px-20 py-5">
					<table class="w-full  text-sm">
						<tbody>
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
								<td width="20%">Reviewed by:</td>
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
								<td width="10%">Recommending Approval By:</td>
								<td class="border-b">
									<select name="" class="outline-none w-full px-1 font-semibold" id="">
										<option value="">Select Employee</option>
										<option value=""></option>
										<option value=""></option>
									</select>
								</td>
								<td width="5%"></td>
								<td width="20%">Approved By:</td>
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
						</tbody>
					</table>
				</div>
				<div class="mt-4 flex justify-end flex-wrap gap-2">
					<!-- Instead of <a>, we use buttons that trigger modal -->
					<a class="inline-flex items-center rounded-lg px-4 py-3 text-sm font-medium text-lg border border-blue-300 text-blue-900 hover:bg-blue-100">
						Save as Draft
					</a>
					<a href="print_pr" class="inline-flex items-center rounded-lg px-4 py-3 text-sm font-medium text-lg shadow-sm bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600/50">
						Proceed
					</a>
				</div>
			</div>
		</section>
	</navigation>
</template>

<script setup>
import { reactive } from "vue";
import { PlusIcon, XMarkIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/solid'
import navigation from "@/components/layouts/navigation.vue";
import { ref, computed , onMounted } from "vue";

// State
const selectedCompany = ref("");
const selectedDepartment = ref("");
const selectedPurchase = ref("");

// Sample data mapping
const purchaseOptions = {
  ENERGREEN: ["Iloilo", "Cebu"],
  CENPRI: ["Bacolod", "Bago"],
  CPGC: ["Manila", "Quezon"],
};

// Departments per company (example)
const departmentOptions = {
  ENERGREEN: ["Finance", "Operations"],
  CENPRI: ["HR", "Logistics"],
  CPGC: ["Admin", "Engineering"],
};

// Reactive computed lists
const purchaseRequests = computed(() => {
  return purchaseOptions[selectedCompany.value] || [];
});

const departments = computed(() => {
  return departmentOptions[selectedCompany.value] || [];
});

// Auto department code (example)
const departmentCode = computed(() => {
  if (selectedDepartment.value === "Finance") return "FIN";
  if (selectedDepartment.value === "Operations") return "OPS";
  if (selectedDepartment.value === "HR") return "HR";
  if (selectedDepartment.value === "Logistics") return "LOG";
  if (selectedDepartment.value === "Admin") return "ADM";
  if (selectedDepartment.value === "Engineering") return "ENG";
  return "";
});

const rows = ref([
  {
    id: Date.now(),
    qty: "",
    uom: "",
    partNo: "",
    description: "",
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
  },
]);

// Add row
const addRow = () => {
  rows.value.push({
    id: Date.now() + Math.random(),
    qty: "",
    uom: "",
    partNo: "",
    description: "",
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
};

// Remove row
const removeRow = (index) => {
  rows.value.splice(index, 1);
};


/**
 * If you’re NOT using Inertia:
 * - Pass :is-logged-in="true/false" from parent and remove the Inertia bits below.
 */
const props = defineProps({
  isLoggedIn: { type: Boolean, default: null }, // null means "auto-detect via Inertia"
  rememberKey: { type: String, default: 'hide_auth_reminder' } // localStorage key
})

const dismissed = ref(false)

// Auto-detect (Inertia) or fallback to prop
let inertiaUser = null
try {
  const page = usePage?.()
  inertiaUser = computed(() => page?.props?.auth?.user ?? null)
} catch (e) {
  inertiaUser = computed(() => null)
}

const loggedIn = computed(() => {
  // If prop provided, use it; else use Inertia user presence
  return props.isLoggedIn !== null ? props.isLoggedIn : !!inertiaUser.value
})

const shouldShow = computed(() => !loggedIn.value && !dismissed.value)

const dismiss = () => {
  dismissed.value = true
  try {
    localStorage.setItem(props.rememberKey, '1')
  } catch {}
}

onMounted(() => {
  try {
    dismissed.value = localStorage.getItem(props.rememberKey) === '1'
  } catch {}
})


// Simulate your row object
const row = ref({
  partNo: "",
});

// Input model (separate from row so filtering works)
const search = ref("");

// Item list
const items = ref([
  { code: "00001-AX", name: "Hydraulic Pump" },
  { code: "00002-BP", name: "Brake Pad Set" },
  { code: "00003-AF", name: "Air Filter" },
  { code: "00004-SP", name: "Spark Plug" },
  { code: "00005-AL", name: "Alternator Assembly" },
]);

const showList = ref(false);

// Filter items by search
const filteredItems = computed(() => {
  return items.value.filter((item) =>
    (item.code + " " + item.name)
      .toLowerCase()
      .includes(search.value.toLowerCase())
  );
});

// Select item from dropdown
function selectItem(item) {
  row.value.partNo = `${item.code} - ${item.name}`;
  search.value = row.value.partNo;
  showList.value = false;
}


const row2 = ref({
  itemCode: "",
  itemCodeSearch: "",
});

// Example item codes
const items2 = ref([
  { code: "IC-1001", name: "Hydraulic Pump" },
  { code: "IC-2045", name: "Brake Pad Set" },
  { code: "IC-3050", name: "Air Filter" },
  { code: "IC-4102", name: "Spark Plug" },
  { code: "IC-5120", name: "Alternator Assembly" },
]);

// Dropdown state
const showItemCodeList = ref(false);

const filteredItemCodes = computed(() => {
  return items2.value.filter((item) =>
    item.code.toLowerCase().includes(row2.value.itemCodeSearch.toLowerCase())
  );
});

function selectItemCode(item) {
  row2.value.itemCode = item.code;          // keep raw code
  row2.value.itemCodeSearch = item.code;    // show only code in input
  showItemCodeList.value = false;
}
</script>

<style scoped>
/* Add component-specific styles here if needed */
</style>
