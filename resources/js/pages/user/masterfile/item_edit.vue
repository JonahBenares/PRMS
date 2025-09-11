<template>
    <navigation>
		<section class="items-center justify-center py-8">
			<div class="bg-white rounded-lg shadow-lg max-w-6xl mx-auto">
				<!-- Header -->
				<div class="px-6 py-4 flex justify-between items-center mb-4 bg-white border-b rounded-t-lg">
                    <h2 class="text-lg font-semibold">Edit Item</h2>
                    <a href="/masterfile/items"
                        class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg shadow hover:bg-blue-700 transition">
                        Show List
                    </a>
                </div>
				<!-- Table -->
				<div class="px-6 pt-2 pb-6">
                    <!-- Sub Category / Category -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-500 uppercase mb-1">Sub Category</label>
                            <select class="w-full border rounded-md px-2 py-2 text-sm">
                                <option>Select Sub Category</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 uppercase mb-1">Category</label>
                            <input type="text" class="w-full border rounded-md px-2 py-2 text-sm" placeholder="Category" readonly>
                        </div>
                    </div>

                    <!-- Item Description / Location / Warehouse -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs text-gray-500 uppercase mb-1">Item Description</label>
                            <input type="text" class="w-full border rounded-md px-2 py-2 text-sm" placeholder="Item Description">
                        </div>
                    </div>

                    <!-- Rack / Group / Reorder / Beginning -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                         <div>
                            <label class="block text-xs text-gray-500 uppercase mb-1">Location</label>
                            <select class="w-full border rounded-md px-2 py-2 text-sm">
                                <option>Select Location</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 uppercase mb-1">Warehouse</label>
                            <select class="w-full border rounded-md px-2 py-2 text-sm">
                                <option>Select Warehouse</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 uppercase mb-1">Rack</label>
                            <select class="w-full border rounded-md px-2 py-2 text-sm">
                                <option>Select Rack</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 uppercase mb-1">Group</label>
                            <select class="w-full border rounded-md px-2 py-2 text-sm">
                                <option>Select Group</option>
                            </select>
                        </div>
                    </div>

                    <div class="w-full">
                        <!-- Header -->
                        <div class="flex justify-between items-center mt-6">
                            <h2 class="font-semibold text-gray-700">Variant Items</h2>
                            <button
                                @click="addVariant"
                                class="flex items-center bg-blue-600 hover:bg-blue-700 text-white text-sm px-3 py-1.5 rounded"
                            >
                                <PlusIcon class="w-4 h-4 mr-1" />
                                Add Variant
                            </button>
                        </div>

                        <!-- Variants List -->
                        <div
                        v-for="(variant, index) in variants"
                        :key="index"
                        class="mt-3 border border-gray-200 rounded-lg overflow-hidden text-sm"
                        >
                            <table class="w-full border-collapse">
                                <tbody>
                                    <tr class="border-b">
                                        <td class="bg-gray-100 font-medium px-2">Brand</td>
                                        <td colspan="5" class="p-0">
                                            <div class="flex items-center space-x-2 pr-2">
                                                <input
                                                v-model="variant.brand"
                                                type="text"
                                                class="w-full rounded px-2 py-1 focus:ring focus:ring-blue-200"
                                                />
                                                <div class="border-l"> &nbsp;</div>
                                                <button
                                                @click="deleteVariant(index)"
                                                class="bg-red-500 hover:bg-red-600 text-white rounded p-1 mx-2"
                                                >
                                                <TrashIcon class="w-4 h-4" />
                                                </button>
                                            </div>
                                        </td>

                                    </tr>

                                    <tr class="border-b">
                                        <td class="bg-gray-100 font-medium w-24 px-2">Item Code</td>
                                        <td class="w-1/4">
                                        <input v-model="variant.itemCode" type="text" class="w-full rounded px-2 py-1" />
                                        </td>
                                        <td class="bg-gray-100 font-medium w-24 px-2">Part No.</td>
                                        <td>
                                            <input v-model="variant.type" type="text" class="w-full rounded px-2 py-1" />
                                        </td>
                                        <td class="bg-gray-100 font-medium w-24 px-2">Type</td>
                                        <td>
                                            <input v-model="variant.type" type="text" class="w-full rounded px-2 py-1" />
                                        </td>
                                        
                                    </tr>

                                    <tr class="border-b">
                                        <td class="bg-gray-100 font-medium w-24 px-2">Model</td>
                                        <td>
                                            <input v-model="variant.model" type="text" class="w-full rounded px-2 py-1" />
                                        </td>
                                        <td class="bg-gray-100 font-medium px-2">Size</td>
                                        <td>
                                            <input v-model="variant.size" type="text" class="w-full rounded px-2 py-1" />
                                        </td>
                                        <td class="bg-gray-100 font-medium px-2">Color</td>
                                        <td>
                                            <input v-model="variant.color" type="text" class="w-full rounded px-2 py-1" />
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="bg-gray-100 font-medium px-2">Material</td>
                                        <td>
                                            <input v-model="variant.material" type="text" class="w-full rounded px-2 py-1" />
                                        </td>
                                        <td class="bg-gray-100 font-medium px-2">Serial</td>
                                        <td>
                                            <input v-model="variant.serial" type="text" class="w-full rounded px-2 py-1" />
                                        </td>
                                        <td class="bg-gray-100 font-medium px-2">UOM</td>
                                        <td class="">
                                            <input v-model="variant.uom" type="text" class="w-full rounded px-2 py-1" />
                                        
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="border-t">
                                            

                                            <!-- Images -->
                                            <div class="mt-2 flex flex-wrap -mx-1">
                                                <!-- Image 1 -->
                                                <div class="w-full sm:w-1/3 px-1 mb-4">
                                                <div class="flex flex-col">
                                                    <span class="text-xs text-gray-500 uppercase mb-1">Image 1</span>
                                                    <input type="file" accept="image/*" id="image1" @change="upload_image1" class="border rounded px-2 py-1 text-sm">
                                                    <div class="mt-2 w-24 h-24 rounded-full overflow-hidden">
                                                    <img :src="imageUrl1" class="w-full h-full object-cover">
                                                    </div>
                                                </div>
                                                </div>

                                                <!-- Image 2 -->
                                                <div class="w-full sm:w-1/3 px-1 mb-4">
                                                <div class="flex flex-col">
                                                    <span class="text-xs text-gray-500 uppercase mb-1">Image 2</span>
                                                    <input type="file" accept="image/*" id="image2" @change="upload_image2" class="border rounded px-2 py-1 text-sm">
                                                    <div class="mt-2 w-24 h-24 rounded-full overflow-hidden">
                                                    <img :src="imageUrl2" class="w-full h-full object-cover">
                                                    </div>
                                                </div>
                                                </div>

                                                <!-- Image 3 -->
                                                <div class="w-full sm:w-1/3 px-1 mb-4">
                                                <div class="flex flex-col">
                                                    <span class="text-xs text-gray-500 uppercase mb-1">Image 3</span>
                                                    <input type="file" accept="image/*" id="image3" @change="upload_image3" class="border rounded px-2 py-1 text-sm">
                                                    <div class="mt-2 w-24 h-24 rounded-full overflow-hidden">
                                                    <img :src="imageUrl3" class="w-full h-full object-cover">
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>


                    <!-- Save Buttons -->
                    <div class="mt-8 flex justify-end space-x-2 border-t pt-4">
                        <button class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm px-4 py-2 rounded-md shadow">
                        Save As Draft
                        </button>
                        <button class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-6 py-2 rounded-md shadow">
                        Save Changes
                        </button>
                    </div>

				</div>
			</div>

				
		</section>
    </navigation>
</template>

<script setup>
    import { ref, onMounted, reactive } from "vue";
    import { TrashIcon, PencilSquareIcon, PlusIcon } from '@heroicons/vue/24/solid';
    import navigation from "@/components/layouts/navigation.vue";

    
// Variant structure
const variants = ref([
  {
    itemCode: "",
    brand: "",
    type: "",
    model: "",
    size: "",
    color: "",
    material: "",
    serial: "",
    expiration: "",
    barcode: "",
    uom: "",
  },
]);

// Add new variant
const addVariant = () => {
  variants.value.push({
    itemCode: "",
    brand: "",
    type: "",
    model: "",
    size: "",
    color: "",
    material: "",
    serial: "",
    expiration: "",
    barcode: "",
    uom: "",
  });
};

// Delete variant
const deleteVariant = (index) => {
  variants.value.splice(index, 1);
};
</script>
