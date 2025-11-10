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
							<td class="p-2">Logo</td>
							<td class="p-2">
								<span class="text-lg mt-1 font-bold" >CENTRAL NEGROS POWER RELIABILITY INC.</span>
								<p class="leading-tight text-sm m-0 ">Office: 88 Corner Rizal-Mabini Sts., Bacolod City</p>
								<p class="leading-tight text-sm m-0 ">Plant Site: Purok San Jose, Barangay Calumangan, Bago City</p>
								<p class="leading-tight text-sm mb-0">Telefax: (034) 435-1932</p>
							</td>
							<td class="p-2 font-bold uppercase">Purchase Request</td>
						</tr>
					</tbody>
				</table>
				<table class="w-full text-sm !border-b border-x">
					<tbody>
						<tr>
							<td class="border-x px-1" width="">Location</td>
							<td class="border-x px-1" width="37.5%">Plant Site</td>
							<td class="border-x px-1" width="">Department</td>
							<td class="border-x px-1" width="37.5%">Purchasing Department</td>
						</tr>
						<tr>
							<td class="border px-1" width="">Purchase Number</td>
							<td class="border px-1" width="37.5%">PR-9892-CNPR-SITE</td>
							<td class="border px-1" width="">Department Code</td>
							<td class="border px-1" width="37.5%">PUR-1200</td>
						</tr>
						<tr>
							<td class="border px-1" width="">Date Prepared</td>
							<td class="border px-1" width="37.5%">10/10/25</td>
							<td class="border px-1" width="">Requestor</td>
							<td class="border px-1" width="37.5%">Hennelen Tantanan</td>
						</tr>
						<tr>
							<td class="border px-1" width="">Date Issued</td>
							<td class="border px-1" width="37.5%">10/10/25</td>
							<td class="border px-1" width="">Urgency</td>
							<td class="border px-1" width="35%">1 - Week</td>
						</tr>
						<tr>
							<td class="border px-1" width="">Purpose</td>
							<td class="border px-1" colspan="3">IT Requirements for the renovation on Guard House Post 1</td>
						</tr>
						<tr>
							<td class="border px-1" width="">End-use</td>
							<td class="border px-1" colspan="3">IT Site Department</td>
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
						<tr>
							<td class="align-top !border-x !border-b px-1 text-center">01</td>
							<td class="align-top !border-x !border-b px-1 text-center">25</td>
							<td class="align-top !border-x !border-b px-1 text-center"> pc/s</td>
							<td class="align-top !border-x !border-b px-1">55844-28823</td>
							<td class="align-top !border-x !border-b px-1">
								<span class="font-semibold">Sample item - PRT03399;</span>
								<span class="">Category;Brand;Model;Size;Color;Material;Unit;Serial</span>
							</td>
							<td class="align-top !border-x !border-b px-1"></td>
							<td class="align-top !border-x !border-b px-1 text-center">10/10/25</td>
						</tr>
					</tbody>
				</table>
				<table class="w-full text-sm !border-b border-x">
					<tbody>
						<tr>
							<td class="border px-1 align-top" colspan="3">Notes : Sample Notes Here</td>
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
								<td width="10%">Planned By:</td>
								<td class="border-b text-center">Employee Name</td>
								<td width="5%"></td>
								<td width="20%">Request Verified By:</td>
								<td class="border-b text-center">Employee Name</td>
							</tr>
							<tr>
								<td width=""></td>
								<td class="p-0 text-center pb-7">Position</td>
								<td></td>
								<td width=""></td>
								<td class="p-0 text-center pb-7">Position</td>
							</tr>

							<tr>
								<td width="10%">Checked By:</td>
								<td class="border-b text-center">Employee Name</td>
								<td width="5%"></td>
								<td width="20%">Processed By:</td>
								<td class="border-b text-center">Employee Name</td>
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
								<td class="p-0 text-center pb-7">Position</td>
								<td></td>
								<td width=""></td>
								<td class="p-0 text-center pb-7">Position</td>
							</tr>

							<tr>
								<td></td>
								<td class="border-b text-center">Employee Name</td>
								<td></td>
								<td>Approved By:</td>
								<td class="border-b text-center">Employee Name</td>
							</tr>
							<tr>
								<td width=""></td>
								<td class="p-0 text-center pb-7">Position</td>
								<td></td>
								<td width=""></td>
								<td class="p-0 text-center pb-7">Position</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="mt-4 flex justify-between flex-wrap gap-2">
					<div>
						<a href="/create_pr" class="inline-flex items-center rounded-lg px-4 py-3 text-sm font-medium text-lg shadow-sm bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-blue-600/50/">
							Revise
						</a>
					</div>
					<div class="flex justify-between flex-wrap gap-2">
						<a href="/create_pr" class="inline-flex items-center rounded-lg px-4 py-3 text-sm font-medium text-lg border border-blue-300 text-blue-900 hover:bg-blue-100">
							Back
						</a>
						<a href="/print_pr" class="inline-flex items-center rounded-lg px-4 py-3 text-sm font-medium text-lg shadow-sm bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600/50/">
							Save & Export
						</a>
					</div>
				</div>
			</div>
		</section>
	</navigation>
</template>

<script setup>
import { reactive } from "vue";
import { PlusIcon, XMarkIcon } from '@heroicons/vue/24/solid'
import navigation from "@/components/layouts/navigation.vue";

const form = reactive({
	name: "",
	email: "",
	message: "",
});

const handleSubmit = () => {
	console.log("Form submitted:", form);
	alert(`Thanks ${form.name}, we received your message!`);
};
</script>

<style scoped>
/* Add component-specific styles here if needed */
</style>
