<template> 
	<navigation>
		<!-- Form Section -->
		<section class="items-center justify-center py-12">
			<!-- AuthReminderCard.vue -->

			<!-- <div class="w-full pb-4 max-w-6xl mx-auto">
				<div class="rounded-lg border border-amber-200 bg-amber-50/80 p-4 sm:p-5 shadow-sm flex items-start gap-3">
					<div class="flex-1 min-w-0">
						<div>
							<p class="font-semibold text-amber-900">You’re not logged in</p>
							<p class="text-sm text-amber-800 mt-1">
								Log in to save your work, track requests, and access restricted pages.
							</p>
						</div>

						<div class="mt-3 flex flex-wrap gap-2">
							<a href="/login"
							class="inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium shadow-sm bg-amber-600 text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-600/50">
							Log in
							</a>
							<a href="/register"
							class="inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium border border-amber-300 text-amber-900 hover:bg-amber-100">
							Create account
							</a>
							<button @click="dismiss"
									class="inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium border border-transparent hover:bg-amber-100 text-amber-900">
							Remind me later
							</button>
						</div>
					</div>
					<button @click="dismiss" class="p-1 rounded-lg text-amber-700 hover:bg-amber-100" aria-label="Dismiss">
						<XMarkIcon class="w-5 h-5" />
					</button>
				</div>
			</div> -->
			<div class="w-full bg-white shadow-sm rounded-lg p-8 max-w-6xl mx-auto">
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
							<td class="border px-1">Purchase Request</td>
							<td class="border w-[37.5%]">
								<select v-model="selectedPurchase" class="outline-none w-full">
									<option value="">Select Location</option>
									<option v-for="req in purchaseRequests" :key="req" :value="req">
									{{ req }}
									</option>
								</select>
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
									<option value=""></option>
									<option value=""></option>
									<option value=""></option>
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
				<table class="w-full border-b border-x text-sm">
					<thead class="bg-blue-50">
						<tr>
							<td class="!border-x !border-b font-semibold px-1 text-center" width="3%">#</td>
							<td class="!border-x !border-b font-semibold px-1 text-center" width="5%">Qty</td>
							<td class="!border-x !border-b font-semibold px-1 text-center" width="5%">UOM</td>
							<td class="!border-x !border-b font-semibold px-1" width="15%">Part No.</td>
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
							<input v-model="row.partNo" class="outline-none w-full px-1" placeholder="00000-00" type="text" />
							</td>
							<td class="align-top !border-x !border-b">
							<select v-model="row.description" class="outline-none w-full px-1 font-semibold">
								<option value="">Select Item</option>
							</select>

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
					<button
					@click="openModal('draft')"
					class="inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium border border-blue-300 text-blue-900 hover:bg-blue-100"
					>
					Save as Draft
					</button>
					<button
					@click="openModal('changes')"
					class="inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium shadow-sm bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600/50"
					>
					Save Changes
					</button>
				</div>
			</div>
		</section>
	</navigation>
	<transition name="fade">
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
    >
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 relative">
        <!-- Close Button -->
        <button
          @click="closeModal"
          class="absolute top-3 right-3 text-gray-500 hover:text-gray-700"
        >
          ✕
        </button>

        <h2 class="text-xl font-semibold text-gray-800 mb-4 text-center">
          Please log in to continue
        </h2>

        <form @submit.prevent="handleLogin">
          <div class="space-y-3">
            <div>
              <label class="block text-sm font-medium text-gray-700">Email</label>
              <input
                v-model="email"
                type="email"
                required
                class="mt-1 w-full rounded-lg border px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Password</label>
              <input
                v-model="password"
                type="password"
                required
                class="mt-1 w-full rounded-lg border px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
          </div>

          <button
            type="submit"
            class="mt-4 w-full rounded-lg bg-blue-600 text-white py-2 font-medium hover:bg-blue-700"
          >
            Log In
          </button>
        </form>

        <p class="text-sm text-gray-600 mt-4 text-center">
          Don’t have an account?
          <a href="/register" class="text-blue-600 hover:underline">Register</a>
        </p>
      </div>
    </div>
  </transition>
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


const showModal = ref(false);
const actionType = ref(""); // either "draft" or "changes"

const email = ref("");
const password = ref("");

// Open modal
const openModal = (type) => {
  actionType.value = type;
  showModal.value = true;
};

// Close modal
const closeModal = () => {
  showModal.value = false;
};

// Handle login
const handleLogin = () => {
  // Example: send to backend with axios/fetch
  console.log("Logging in with", email.value, password.value);

  // You can also check `actionType.value` to know what button triggered it
  if (actionType.value === "draft") {
    console.log("User wants to save as draft after login");
  } else if (actionType.value === "changes") {
    console.log("User wants to save changes after login");
  }

  closeModal();
};

</script>

<style scoped>
/* Add component-specific styles here if needed */
</style>
