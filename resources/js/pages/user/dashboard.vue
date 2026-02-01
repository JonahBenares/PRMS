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
							<td class=" px-1">Company</td>
							<td class=" w-[37.5%]">
								<select v-model="selectedCompany" class="outline-none w-full py-1 border rounded-lg px-2">
								<option value="">Select Company</option>
								<option value="ENERGREEN">ENERGREEN</option>
								<option value="CENPRI">CENPRI</option>
								<option value="CPGC">CPGC</option>
								</select>
							</td>
							<td class=" px-1">Department</td>
							<td class=" w-[37.5%]">
								<select v-model="selectedDepartment" class="outline-none w-full py-1 border rounded-lg px-2">
								<option value="">Select Department</option>
								<option v-for="dept in departments" :key="dept" :value="dept">
									{{ dept }}
								</option>
								</select>
							</td>
						</tr>
						<tr>
							<td class=" px-1">Location</td>
							<td class=" w-[37.5%]">
								<select v-model="selectedPurchase" class="outline-none w-full py-1 border rounded-lg px-2">
									<option value="">Select Location</option>
									<option v-for="req in purchaseRequests" :key="req" :value="req">
									{{ req }}
									</option>
								</select>
							</td>
							<td class=" px-1">Department Code</td>
							<td class=" w-[37.5%]">
								<input type="text" class="outline-none w-full py-1 border rounded-lg px-2 px-1" v-model="departmentCode">
							</td>
						</tr>
						<tr>
							<td class=" px-1">PR Number</td>
							<td class=" w-[37.5%]">
								<input type="text" class="outline-none w-full py-1 border rounded-lg px-2 px-1" :value="prNumber" readonly>
							</td>
							<td class=" px-1" width="">Requestor</td>
							<td class="" width="37.5%">
								<select name="" class="outline-none w-full py-1 border rounded-lg px-2 px-1" id="">
									<option value=""></option>
									<option value=""></option>
									<option value=""></option>
								</select>
							</td>
						</tr>
						<tr>
							<td class=" px-1" width="">Date Prepared</td>
							<td class="" width="37.5%"><input type="date" class="outline-none w-full py-1 border rounded-lg px-2 px-1"></td>
							<td class=" px-1" width="">Urgency</td>
							<td class="" width="35%">
								<select name="" class="outline-none w-full py-1 border rounded-lg px-2 px-1" id="">
									<option value="">A Week</option>
									<option value="">Month</option>
									<option value="">Quarter</option>
									<option value="">Year</option>
								</select>
							</td>
						</tr>
						<tr>
							<td class=" px-1" width=""></td>
							<td class="" width="37.5%"></td>
							
						</tr>
						<tr>
							<td class=" px-1" width="">Purpose</td>
							<td class="" colspan="3">
								<select name="" class="outline-none w-full py-1 border rounded-lg px-2" id="">
									<option value=""></option>
									<option value=""></option>
									<option value=""></option>
								</select>
							</td>
						</tr>
						<tr>
							<td class=" px-1" width="">End-use</td>
							<td class="" colspan="3">
								<select name="" class="outline-none w-full py-1 border rounded-lg px-2" id="">
									<option value=""></option>
									<option value=""></option>
									<option value=""></option>
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
									@input="filterItems(index)"
									@focus="showDropdown = index"
									@blur="hideDropdown"
									class="outline-none w-full px-1"
									placeholder="Search Item Code / Name / PN"
									type="text"
									ref="searchInput"
									/>
									<!-- Dropdown -->
									<ul
									v-if="showDropdown === index && filteredItems.length"
									:style="dropdownStyle"
									class="fixed z-50 bg-white border border-gray-300 w-64 max-h-40 overflow-y-auto shadow-lg rounded"
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
								<div v-if="row.itemCode" class="space-y-1 px-2">
									<div>
										<input
											:value="`${row.itemName || ''}${row.pn ? ' ' + row.pn : ''}`"
											class="outline-none w-full font-semibold"
											readonly
										/>
									</div>
									<div v-if="row.category && row.category.trim() !== ''" class="flex justify-start">
										<span class="pr-2 w-20">Category:</span>
										<input v-model="row.category" type="text" class="outline-none w-full" />
									</div>

									<div v-if="row.brand && row.brand.trim() !== ''" class="flex justify-start">
										<span class="pr-2 w-20">Brand:</span>
										<input v-model="row.brand" type="text" class="outline-none w-full" />
									</div>

									<div v-if="row.model && row.model.trim() !== ''" class="flex justify-start">
										<span class="pr-2 w-20">Model:</span>
										<input v-model="row.model" type="text" class="outline-none w-full" />
									</div>

									<div v-if="row.size && row.size.trim() !== ''" class="flex justify-start">
										<span class="pr-2 w-20">Size:</span>
										<input v-model="row.size" type="text" class="outline-none w-full" />
									</div>

									<div v-if="row.color && row.color.trim() !== ''" class="flex justify-start">
										<span class="pr-2 w-20">Color:</span>
										<input v-model="row.color" type="text" class="outline-none w-full" />
									</div>

									<div v-if="row.material && row.material.trim() !== ''" class="flex justify-start">
										<span class="pr-2 w-20">Material:</span>
										<input v-model="row.material" type="text" class="outline-none w-full" />
									</div>

									<div v-if="row.unit && row.unit.trim() !== ''" class="flex justify-start">
										<span class="pr-2 w-20">Unit:</span>
										<input v-model="row.unit" type="text" class="outline-none w-full" />
									</div>

									<div v-if="row.serial && row.serial.trim() !== ''" class="flex justify-start">
										<span class="pr-2 w-20">Serial:</span>
										<input v-model="row.serial" type="text" class="outline-none w-full" />
									</div>

								</div>

								<!-- Default blank state (optional) -->
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
							<textarea name="" id="" class="m-0 w-full p-1 outline-none" rows="1"></textarea>
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
				<!-- <a class="inline-flex items-center rounded-lg px-4 py-3 text-sm font-medium text-lg border border-blue-300 text-blue-900 hover:bg-blue-100">
					Save as Draft
				</a> -->
				<a href="print_pr" class="inline-flex items-center rounded-lg px-4 py-3 text-sm font-medium text-lg shadow-sm bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600/50">
					Proceed
				</a>
			</div>
		</pageCard>
	</navigation>
</template>

<script setup>
	import { ref, computed, onMounted } from "vue";
	import { PlusIcon, XMarkIcon } from "@heroicons/vue/24/solid";
	import navigation from "@/components/layouts/navigation.vue";
	import pageCard from "@/components/card.vue";

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
		{ code: "10023", name: "Bolt", pn: "PN-233", uom: "pcs", size: "M6x40mm", color: "Silver", material: "Steel", unit: "Box", serial: "BOLT-10023" },
		{ code: "10024", name: "Cotton Rug", pn: "PN-253", uom: "pcs", category: "Household", brand: "HomeTex", model: "R253", unit: "Pack", serial: "RUG-10024" },
		{ code: "10025", name: "Hydraulic Pump", pn: "PN-345", uom: "set", category: "Mechanical", brand: "HydraPro", model: "H345", size: "M12", color: "Gray" },
		{ code: "10026", name: "LED Bulb", pn: "PN-412", uom: "pcs", wattage: "9W", color: "Warm White", brand: "Philips", voltage: "220V", unit: "Box", serial: "LED-10026" },
		{ code: "10027", name: "Laptop Stand", pn: "PN-578", uom: "pcs", category: "Office", brand: "ErgoLift", material: "Aluminum", color: "Black", unit: "Piece" },
		{ code: "10028", name: "PVC Pipe", pn: "PN-612", uom: "mtr", size: "2-inch", color: "White", material: "PVC", category: "Plumbing", unit: "Bundle" },
		{ code: "10029", name: "Safety Helmet", pn: "PN-789", uom: "pcs", color: "Yellow", brand: "3M", category: "Safety", unit: "Piece", serial: "HELM-10029" },
		{ code: "10030", name: "Printer Ink", pn: "PN-845", uom: "bottle", color: "Black", brand: "Epson", capacity: "100ml", unit: "Box" },
		{ code: "10031", name: "Wrench Set", pn: "PN-903", uom: "set", category: "Tools", brand: "Stanley", pieces: "12", material: "Chrome Vanadium", unit: "Case" },
		{ code: "10032", name: "Air Filter", pn: "PN-950", uom: "pcs", category: "Automotive", brand: "Bosch", model: "AF950", size: "Medium", color: "White", unit: "Box" },
		{ code: "10033", name: "Extension Cord", pn: "PN-1001", uom: "pcs", length: "5m", color: "White", voltage: "220V", brand: "Omni", unit: "Piece" },
		{ code: "10034", name: "Ceramic Tile", pn: "PN-1102", uom: "box", size: "60x60cm", color: "Beige", brand: "Mariwasa", category: "Construction", unit: "Box" },
		{ code: "10035", name: "Drill Machine", pn: "PN-1204", uom: "pcs", power: "600W", brand: "Makita", model: "D600", color: "Blue", unit: "Box" },
		{ code: "10036", name: "Office Chair", pn: "PN-1345", uom: "pcs", category: "Furniture", brand: "ErgoFlex", color: "Black", material: "Mesh", unit: "Piece" },
		{ code: "10037", name: "Copper Wire", pn: "PN-1452", uom: "roll", length: "100m", material: "Copper", gauge: "2.5mm²", color: "Red", unit: "Roll" },
		{ code: "10038", name: "Battery Pack", pn: "PN-1560", uom: "pcs", capacity: "12V 7Ah", brand: "Panasonic", type: "Sealed Lead Acid", unit: "Piece" },
		{ code: "10039", name: "Paint Thinner", pn: "PN-1661", uom: "ltr", brand: "Boysen", category: "Chemical", container: "Gallon", unit: "Can" },
		{ code: "10040", name: "HDMI Cable", pn: "PN-1753", uom: "pcs", length: "2m", brand: "Belkin", color: "Black", category: "Electronics", unit: "Piece" },
		{ code: "10041", name: "Electric Fan", pn: "PN-1855", uom: "pcs", brand: "Asahi", model: "EF16", size: "16-inch", color: "Gray", unit: "Box" },
		{ code: "10042", name: "Paper Ream (A4)", pn: "PN-1900", uom: "ream", brand: "Double A", category: "Office", gsm: "80gsm", unit: "Box" },
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

	const signatories = ref([
  {
    id: 1,
    name: "Prepared By",
    employees: [
      { id: 1, name: "John Doe" },
      { id: 2, name: "Jane Smith" }
    ],
    selectedEmployee: null,
    position: ""
  },
  {
    id: 2,
    name: "Reviewed By",
    employees: [
      { id: 3, name: "Mark Lee" },
      { id: 4, name: "Anna Cruz" }
    ],
    selectedEmployee: null,
    position: ""
  },
  {
    id: 3,
    name: "Approved By",
    employees: [
      { id: 5, name: "Paul Tan" },
      { id: 6, name: "Lara Lim" }
    ],
    selectedEmployee: null,
    position: ""
  }
])
</script>

<style scoped>
/* Add component-specific styles here if needed */
</style>
