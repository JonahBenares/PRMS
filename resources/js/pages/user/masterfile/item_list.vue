<script setup>
	import { ref, onMounted } from 'vue'
	import { useTable } from '@/composables/useTable'
	import searchbox from '@/composables/searchbox.vue'
	import pagination from "@/composables/pagination.vue"
	import navigation from "@/components/layouts/navigation.vue"

	import { PlusIcon, EyeIcon, ChevronDownIcon, ChevronUpIcon, PencilSquareIcon, Bars3Icon } from '@heroicons/vue/24/solid'

	// -------------------
	// TABLE (useTable)
	// -------------------
	const {
		items,
		search,
		page,
		perPage,
		total,
		lastPage,
		loading,
		fetchData
	} = useTable('/api/items')

	// track expanded rows
	const expanded = ref({})

	const toggleVariants = (id) => {
		expanded.value[id] = !expanded.value[id]
	}

	onMounted(fetchData)
</script>

<template>
	<navigation>
		<section class="items-center justify-center py-8 fade-up">
			<div class="bg-white border border-white/20 rounded-2xl shadow-xl max-w-6xl mx-auto">

			<!-- Header -->
			<div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
				<div>
					<h2 class="text-2xl font-bold text-gray-600">Item List</h2>
				</div>
				<div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto">
					<div class="flex-1 sm:flex-none w-full sm:w-96">
						<searchbox v-model="search" />
					</div>
					<!-- keep same link -->
					<a href="/masterfile/items/add"
					class="w-full sm:w-auto inline-flex items-center justify-center gap-2
					px-4 py-2 bg-blue-600 text-white text-sm font-medium
					rounded-lg shadow hover:bg-blue-700 transition">
						<PlusIcon class="w-4 h-4"/>
						Add Item
					</a>
				</div>
			</div>

			<!-- Top Pagination -->
			<div class="border-b">
				<pagination
					:page="page"
					:per-page="perPage"
					:last-page="lastPage"
					:total="total"
					@update:page="page = $event"
					@update:perPage="perPage = $event"
				/>
			</div>
			<!-- Table -->
			<div class="overflow-hidden">
				<table class="min-w-full text-sm text-left">
					<thead class="bg-gray-100 sticky top-0 z-10">
						<tr class="text-gray-600 uppercase text-xs tracking-wide">
							<th class="px-6 py-3">Item Code</th>
							<th class="px-6 py-3">Description</th>
							<th class="px-6 py-3">Category</th>
							<th class="px-6 py-3">Subcategory</th>
							<th class="px-6 py-3 w-28">
								<div class="flex justify-center">
									<Bars3Icon class="size-4"></Bars3Icon>
								</div>
							</th>
						</tr>
					</thead>
					<tbody class="divide-y">

						<template v-for="item in items" :key="item.id">

						<!-- MAIN ITEM ROW -->
						<tr class="hover:bg-yellow-50 font-medium text-sm">
							<td class="px-6 py-2 text-gray-800">
								{{ item.item_code }}
							</td>

							<td class="px-6 py-2">
								{{ item.item_description }}
							</td>

							<td class="px-6 py-2">
								{{ item.category_name }}
							</td>

							<td class="px-6 py-2">
								{{ item.sub_cat_name }}
							</td>
							<td class="px-6 py-2 text-right">
								<template v-if="item.item_Variants?.length">
									<button
										@click="toggleVariants(item.id)"
										class="p-1 rounded hover:bg-gray-200">
										<ChevronDownIcon v-if="!expanded[item.id]" class="w-4 h-4"/>
										<ChevronUpIcon v-else class="w-4 h-4"/>
									</button>
								</template>
								<!-- keep same edit link -->
								<a :href="`/masterfile/items/edit/${item.id}`"
								class="p-2 rounded-md text-blue-600 hover:bg-blue-50 inline-flex">
									<PencilSquareIcon class="w-4 h-4"/>
								</a>
							</td>
						</tr>

						<!-- VARIANTS (NESTED like subcategories) -->
						<tr v-if="expanded[item.id]">
							<td colspan="5" class="bg-gray-50">
								<div class="inset-shadow-lg px-6 py-2">
									<!-- <span class="px-2">Variants</span> -->
									<table class="w-full text-sm ">
										<thead class="bg-gray-100 text-xs text-gray-800 font-medium">
											<tr>
												<td class="px-2 py-2">Variant Code</td>
												<td class="px-2 py-2">Brand</td>
												<td class="px-2 py-2">Model</td>
												<td class="px-2 py-2">Size</td>
												<td class="px-2 py-2">Color</td>
												<td class="px-2 py-2">UOM</td>
												<td class="px-2 py-2">Images</td>
											</tr>
										</thead>
										<tbody>
											<tr v-for="v in item.item_Variants" :key="v.id"
												class="hover:bg-gray-100 text-xs text-gray-600 border-t">
												<td class="px-2 py-2 w-32">{{ v.variant_item_code }}</td>
												<td class="px-2 py-2">{{ v.brand }}asdas sadasd</td>
												<td class="px-2 py-2">{{ v.model }}</td>
												<td class="px-2 py-2">{{ v.size }}</td>
												<td class="px-2 py-2">{{ v.color }}</td>
												<td class="px-2 py-2">{{ v.uom }}</td>
												<td class="px-2 py-2 flex gap-1 w-10">
													<img v-if="v.img1" :src="`/storage/items/${v.img1}`" class="w-6 h-6 rounded object-cover"/>
													<img v-if="v.img2" :src="`/storage/items/${v.img2}`" class="w-6 h-6 rounded object-cover"/>
													<img v-if="v.img3" :src="`/storage/items/${v.img3}`" class="w-6 h-6 rounded object-cover"/>
												</td>
											</tr>
											<tr v-if="!item.item_Variants?.length">
												<td colspan="7" class="px-6 py-3 text-gray-400">
													No variants
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</td>
						</tr>

						</template>

						<tr v-if="!loading && items.length === 0">
							<td colspan="6" class="py-10 text-center text-gray-500">
								No items found.
							</td>
						</tr>

					</tbody>
				</table>
			</div>

			<!-- Bottom Pagination -->
			<div class="border-t">
			<pagination
				:page="page"
				:per-page="perPage"
				:last-page="lastPage"
				:total="total"
				@update:page="page = $event"
				@update:perPage="perPage = $event"
			/>
			</div>

			</div>
		</section>
	</navigation>
</template>
