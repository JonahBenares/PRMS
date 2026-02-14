<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";

const route = useRoute();
const prId = route.params.id;

const prData = ref({
    location_name: '',
    department_name: '',
    department_code: '',
    pr_no: '',
    date_prepared: '',
    requestor_name: '',
    urgency_name: '',
    purpose_name: '',
    enduse_name: '',
    notes: '',
    pr_items: []
});

const company_addresses = ref([]);  // Array of addresses
const first_telefax = ref('');      // First telefax

const fetchPR = async () => {
    try {
        const res = await axios.get(`/api/pr/${prId}`);
        console.log('API RESPONSE:', res.data);

        if (res.data.success) {
            // Assign main PR data
            prData.value = res.data.data;

            // Extract company addresses array
            company_addresses.value = prData.value.company?.companylocations?.map(loc => loc.address) || [];

            // Extract first telefax (if exists)
            first_telefax.value = prData.value.company?.companylocations?.[0]?.telefax || '';
        }
    } catch (err) {
        console.error('Failed to fetch PR:', err);
    }
};


onMounted(fetchPR);

const printPR = () => {
    window.print();
};
</script>

<style scoped>
/* Add component-specific styles here if needed */
</style>
<template> 
	<navigation>
		<!-- Form Section -->
		<section class="items-center justify-center py-8">
			<!-- this is for the form -->
			<!-- <div class="w-full pb-4 max-w-6xl mx-auto">
				<div class="rounded-lg border border-amber-200 bg-amber-50/80 p-4 sm:p-5 shadow-lg flex items-start gap-3">
					<div class="flex-1 min-w-0">
						<div>
							<p class="font-semibold text-amber-900">Almost Done!</p>
							<p class="text-sm text-amber-800 mt-1">
								Please review all details before saving or exporting to an Excel file.
							</p>
						</div>
					</div>
					<button @click="dismiss" class="p-1 rounded-lg text-amber-700 hover:bg-amber-100" aria-label="Dismiss">
						<XMarkIcon class="w-5 h-5" />
					</button>
				</div>
			</div> -->
			<div class="w-full bg-white shadow-lg rounded-xl p-8 max-w-6xl mx-auto">
				<table class="border w-full">
					<tbody>
						 <tr>
							<td class="p-2">
							<img v-if="prData.company?.company_logo" :src="`/storage/${prData.company.company_logo}`" width="80">
							</td>
							<td class="p-2">
							<span class="text-lg mt-1 font-bold">{{ prData.company?.company_name }}</span>

							<!-- Company addresses loop -->
							<div v-for="(address, index) in company_addresses" :key="index" class="leading-tight text-sm m-0">
								{{ address }}
							</div>

							<div v-if="first_telefax" class="leading-tight text-sm mb-0">
								Tel/Fax: {{ first_telefax }}
							</div>
							</td>
							<td class="p-2 font-bold uppercase">Purchase Request</td>
						</tr>
					</tbody>
				</table>
				<table class="w-full text-sm !border-b border-x">
					<tbody>
						<tr>
							<td class="border-x px-1" width="">Location</td>
							<td class="border-x px-1" width="37.5%">{{ prData.location_name }}</td>
							<td class="border-x px-1" width="">Department</td>
							<td class="border-x px-1" width="37.5%">{{ prData.department_name }}</td>
						</tr>
						<tr>
							<td class="border px-1" width="">Purchase Number</td>
							<td class="border px-1" width="37.5%">{{ prData.pr_no }}</td>
							<td class="border px-1" width="">Department Code</td>
							<td class="border px-1" width="37.5%">{{ prData.department_code }}</td>
						</tr>
						<tr>
							<td class="border px-1" width="">Date Prepared</td>
							<td class="border px-1" width="37.5%">{{ prData.date_prepared }}</td>
							<td class="border px-1" width="">Requestor</td>
							<td class="border px-1" width="37.5%">{{ prData.requestor_name }}</td>
						</tr>
						<tr>
							<td class="border px-1" width=""></td>
							<td class="border px-1" width="37.5%"></td>
							<td class="border px-1" width="">Urgency</td>
							<td class="border px-1" width="35%">{{ prData.urgency_name }}</td>
						</tr>
						<tr>
							<td class="border px-1" width="">Purpose</td>
							<td class="border px-1" colspan="3">{{ prData.purpose_name }}</td>
						</tr>
						<tr>
							<td class="border px-1" width="">End-use</td>
							<td class="border px-1" colspan="3">{{ prData.enduse_name }}</td>
						</tr>
					</tbody>
				</table>
				<table class="w-full border-b border-x text-sm">
					<thead  class="bg-blue-50">
						<tr>
							<td class="!border-x !border-b font-semibold px-1 text-center" width="3%">#</td>
							<td class="!border-x !border-b font-semibold px-1 text-center" width="5%">Qty</td>
							<td class="!border-x !border-b font-semibold px-1 text-center" width="5%">UOM</td>
							<td class="!border-x !border-b font-semibold px-1" width="15%">Item Code</td>
							<td class="!border-x !border-b font-semibold px-1">Description</td>
							<td class="!border-x !border-b font-semibold px-1" width="8%">WH Stock</td>
							<td class="!border-x !border-b font-semibold px-1" width="10%">Date Needed</td>
						</tr>
					</thead>
					<tbody>
						 <tr v-for="(item, index) in prData.pr_items" :key="item.id">
							<td class="align-top !border-x !border-b px-1 text-center">{{ index + 1 }}</td>
							<td class="align-top !border-x !border-b px-1 text-center">{{ item.qty }}</td>
							<td class="align-top !border-x !border-b px-1 text-center"> {{ item.uom }}</td>
							<td class="align-top !border-x !border-b px-1">{{ item.item_variant_code }}</td>
							<td class="align-top !border-x !border-b px-1">
								<span class="">{{ item.description }}</span>
							</td>
							<td class="align-top !border-x !border-b px-1 text-center">{{ item.wh_stocks }}</td>
							<td class="align-top !border-x !border-b px-1 text-center">{{ item.date_needed }}</td>
						</tr>
					</tbody>
				</table>
				<table class="w-full text-sm !border-b border-x">
					<tbody>
						<tr>
							<td class="border px-1 align-top" colspan="3">{{ prData.notes }}</td>
							<!-- <td class="border pxs-1" colspan="2">
								
							</td> -->
						</tr>
						<tr>
							<td class="border px-1 align-top bg-gray-100 font-semibold" colspan="3" align="center">--- Nothing Follows ---</td>
						</tr>
					</tbody>
				</table>
				<div class="border-b border-x px-20 py-5">
					<table class="w-full  text-sm">
						<tbody>
							<tr>
								<td width="10%">Prepared By:</td>
								<td class="border-b text-center">Employee Name</td>
								<td width="5%"></td>
								<td width="20%">Specifications Verified By:</td>
								<td class="border-b text-center" width="30%">Employee Name</td>
							</tr>
							<tr>
								<td width=""></td>
								<td class="p-0 text-center pb-7">Position</td>
								<td></td>
								<td width=""></td>
								<td class="p-0 text-center pb-7">Position</td>
							</tr>

							<tr>
								<td width="10%">WH Check By:</td>
								<td class="border-b text-center">Employee Name</td>
								<td width="5%"></td>
								<td width="20%">Recommending Approval:</td>
								<td class="border-b text-center">Employee Name</td>
							</tr>
							<tr>
								<td width=""></td>
								<td class="p-0 text-center pb-7"></td>
								<td></td>
								<td width=""></td>
								<td class="p-0 text-center pb-7">Position</td>
							</tr>

							<tr>
								<td></td>
								<td class=" text-center"></td>
								<td></td>
								<td>Approved By:</td>
								<td class="border-b text-center">Employee Name</td>
							</tr>
							<tr>
								<td width=""></td>
								<td class="p-0 text-center pb-7"></td>
								<td></td>
								<td width=""></td>
								<td class="p-0 text-center pb-7">Position</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="mt-4 flex justify-between flex-wrap gap-2">
				<!-- Left side -->
				<div class="flex gap-2">
					<a href="/create_pr"
					class="inline-flex items-center rounded-lg px-4 py-3 text-sm font-medium shadow-sm bg-emerald-600 text-white hover:bg-emerald-700">
						Revise
					</a>

					<a href="/create_pr"
					class="inline-flex items-center rounded-lg px-4 py-3 text-sm font-medium border border-blue-300 text-blue-900 hover:bg-blue-100">
						Back
					</a>
				</div>

				<!-- Right side -->
				<div class="flex gap-2">
					<button
						@click="printPR"
						class="inline-flex items-center rounded-lg px-4 py-3 text-sm font-medium shadow-sm bg-blue-600 text-white hover:bg-blue-700">
						Print
					</button>

					<a
						:href="`/export_pr/${pr_id}`"
						class="inline-flex items-center rounded-lg px-4 py-3 text-sm font-medium shadow-sm bg-indigo-600 text-white hover:bg-indigo-700">
						Export
					</a>
				</div>
			</div>
			</div>
		</section>
	</navigation>
</template>
