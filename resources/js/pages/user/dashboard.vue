<template> 
	<navigation>
		<section class="items-center justify-center py-8">
			<div class="w-full bg-white shadow-lg rounded-lg p-8 max-w-6xl mx-auto">
				<div class="flex justify-start">
					<div>
						<h2 class="text-xl font-semibold mb-2 ">PR FORM</h2>
					</div>
					<div class="px-2 mt-1">
						<span class="text-emerald-500 text-white  font-semibold">Revise</span>
					</div>
				</div>
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
								<input type="text" class="outline-none w-full px-1" :value="prNumber" readonly>
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
									<option value="">A Week</option>
									<option value="">Month</option>
									<option value="">Quarter</option>
									<option value="">Year</option>
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
							<td class="!border-x !border-b font-semibold px-1" width="15%">Item Code</td>
							<td class="!border-x !border-b font-semibold px-1 text-center" width="5%">UOM</td>
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
								<!-- Searchable Item Code -->
								<div class="relative">
									<input
									v-model="row.searchQuery"
									@input="filterItems(index)"
									@focus="showDropdown = index"
									@blur="hideDropdown"
									class="outline-none w-full px-1"
									placeholder="Search Item Code / Name / PN"
									type="text"
									/>
									<!-- Dropdown Results -->
									<ul
									v-if="showDropdown === index && filteredItems.length"
									class="absolute z-10 bg-white border border-gray-300 w-full max-h-40 overflow-y-auto shadow-lg rounded"
									>
									<li
										v-for="item in filteredItems"
										:key="item.code"
										@mousedown.prevent="selectItem(index, item)"
										class="px-2 py-1 hover:bg-blue-100 cursor-pointer text-sm"
									>
										<strong>{{ item.code }}</strong> — {{ item.name }} (PN: {{ item.pn }})
									</li>
									</ul>
								</div>
							</td>
							<td class="align-top !border-x !border-b">
								<input v-model="row.uom" class="outline-none w-full text-center" readonly />
							</td>
							<!-- <td class="align-top !border-x !border-b">
								<div class="flex flex-col px-1">
									<div class="flex justify-start">
									<span class="pr-2 w-20 text-gray-600">Item Name:</span>
									<input v-model="row.itemName" class="outline-none w-full font-semibold" readonly />
									</div>
									<div class="flex justify-start mt-1">
									<span class="pr-2 w-20 text-gray-600">PN:</span>
									<input v-model="row.pn" class="outline-none w-full font-semibold" readonly />
									</div>
								</div>
							</td> -->


							<td class="align-top !border-x !border-b">
								<div class="relative w-full px-2">
									<input
									:value="`${row.itemName || ''}${row.pn ? ' ' + row.pn : ''}`"
									class="outline-none w-full font-semibold"
									readonly
									/>
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
				<table class="w-full text-sm !border-b border-x mt-1">
					<tbody>
						<tr>
							<td class="border px-1 align-top" width="11%">Notes</td>
							<td class="border p-0" colspan="2">
								<textarea name="" id="" class="m-0 w-full p-1 outline-none"></textarea>
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
	import { ref, computed, onMounted } from "vue";
	import { PlusIcon, XMarkIcon } from "@heroicons/vue/24/solid";
	import navigation from "@/components/layouts/navigation.vue";

	// ===== COMPANY / DEPARTMENT LOGIC =====
	const selectedCompany = ref("");
	const selectedDepartment = ref("");
	const selectedPurchase = ref("");

	const purchaseOptions = {
	ENERGREEN: ["Iloilo", "Cebu"],
	CENPRI: ["Bacolod", "Bago"],
	CPGC: ["Manila", "Quezon"],
	};

	const departmentOptions = {
	ENERGREEN: ["Finance", "Operations"],
	CENPRI: ["HR", "Logistics", "IT"],
	CPGC: ["Admin", "Engineering"],
	};

	const departmentCodes = {
	Finance: "FIN",
	Operations: "OPS",
	HR: "HR",
	Logistics: "LOG",
	IT: "IT",
	Admin: "ADM",
	Engineering: "ENG",
	};

	const companyCodes = {
	ENERGREEN: "ENGR",
	CENPRI: "CNPR",
	CPGC: "CPGC",
	};

	const purchaseRequests = computed(() => purchaseOptions[selectedCompany.value] || []);
	const departments = computed(() => departmentOptions[selectedCompany.value] || []);
	const departmentCode = computed(() => departmentCodes[selectedDepartment.value] || "");

	const runningNumber = ref(1001);
	const year = new Date().getFullYear().toString().slice(-2);

	const prNumber = computed(() => {
	if (!selectedCompany.value || !selectedDepartment.value) return "";
	const deptCode = departmentCode.value;
	const companyCode = companyCodes[selectedCompany.value] || "XXXX";
	return `PR${deptCode}${year}-${runningNumber.value}-${companyCode}`;
	});

	// ===== ITEM TABLE LOGIC =====
	const showDropdown = ref(null);
	const filteredItems = ref([]);

	// Example dataset (can come from API)
	const items = ref([
	{ code: "10023", name: "Bolt", pn: "PN-233", uom: "pcs", category: "Hardware", brand: "FastenPro", model: "B233", size: "M6x40mm", color: "Silver", material: "Steel", unit: "Box", serial: "BOLT-10023" },
	{ code: "10024", name: "Cotton Rug", pn: "PN-253", uom: "pcs", category: "Household", brand: "HomeTex", model: "R253", size: "2x3ft", color: "White", material: "Cotton", unit: "Pack", serial: "RUG-10024" },
	{ code: "10025", name: "Hydraulic Pump", pn: "PN-345", uom: "set", category: "Mechanical", brand: "HydraPro", model: "H345", size: "M12", color: "Gray", material: "Aluminum", unit: "Piece", serial: "HP-10025" },
	]);

	const rows = ref([
	newRow()
	]);

	function newRow() {
	return {
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
	};
	}

	const addRow = () => rows.value.push(newRow());
	const removeRow = (index) => rows.value.splice(index, 1);

	// Filter item list based on search input
	const filterItems = (index) => {
	const query = rows.value[index].searchQuery?.toLowerCase() || "";
	filteredItems.value = items.value.filter(
		(i) =>
		i.code.toLowerCase().includes(query) ||
		i.name.toLowerCase().includes(query) ||
		i.pn.toLowerCase().includes(query)
	);
	};

	// When item is selected from dropdown
	const selectItem = (index, item) => {
	const row = rows.value[index];
	row.searchQuery = item.code; // show only item code
	row.itemCode = item.code;
	row.itemName = item.name;
	row.pn = item.pn;
	row.uom = item.uom;
	row.category = item.category;
	row.brand = item.brand;
	row.model = item.model;
	row.size = item.size;
	row.color = item.color;
	row.material = item.material;
	row.unit = item.unit;
	row.serial = item.serial;
	showDropdown.value = null;
	};

	const hideDropdown = () => {
	setTimeout(() => (showDropdown.value = null), 150);
	};
</script>

<style scoped>
/* Add component-specific styles here if needed */
</style>
