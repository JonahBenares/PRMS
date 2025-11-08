<template>
    <navigation>
		<pageCard>
            <!-- Header -->
            <div class=" flex justify-between items-center mb-4 bg-white border-b rounded-t-lg">
                <h2 class="text-lg font-semibold">Add Item</h2>
                <a href="/masterfile/items"
                    class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg shadow hover:bg-blue-700 transition">
                    Show List
                </a>
            </div>
            <!-- Table -->
            <div class="">
                <!-- Sub Category / Category -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-500 mb-1">Sub Category</label>
                        <select class="w-full border rounded-md px-2 py-2 text-sm">
                            <option>Select Sub Category</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-500 mb-1">Category</label>
                        <input type="text" class="w-full border rounded-md px-2 py-2 text-sm" placeholder="Category" readonly>
                    </div>
                </div>

                <!-- Item Description / Location / Warehouse -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm text-gray-500 mb-1">Item Description</label>
                        <input type="text" class="w-full border rounded-md px-2 py-2 text-sm" placeholder="Item Description">
                    </div>
                </div>

                <!-- Rack / Group / Reorder / Beginning -->
                <!-- <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                        <div>
                        <label class="block text-sm text-gray-500 mb-1">Location</label>
                        <select class="w-full border rounded-md px-2 py-2 text-sm">
                            <option>Select Location</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-500 mb-1">Warehouse</label>
                        <select class="w-full border rounded-md px-2 py-2 text-sm">
                            <option>Select Warehouse</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-500 mb-1">Rack</label>
                        <select class="w-full border rounded-md px-2 py-2 text-sm">
                            <option>Select Rack</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-500 mb-1">Group</label>
                        <select class="w-full border rounded-md px-2 py-2 text-sm">
                            <option>Select Group</option>
                        </select>
                    </div>
                </div> -->

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
                        class="mt-3 border border-gray-200 rounded-lg overflow-hidden text-sm bg-white shadow-sm"
                    >
                        <table class="w-full border-collapse">
                        <tbody>
                            <tr class="border-b">
                                <td class="bg-gray-100 font-medium px-2 w-24">Brand</td>
                                <td  class="p-0">
                                    <input
                                    v-model="variant.brand"
                                    type="text"
                                    class="w-full rounded px-2 py-1 focus:ring focus:ring-blue-200"
                                    />
                                </td>
                                <td class="bg-gray-100 font-medium w-24 px-2">Item Code</td>
                                <td class="w-1/4">
                                    <input v-model="variant.itemCode" type="text" class="w-full rounded px-2 py-1" />
                                </td>
                                <td class="bg-gray-100 font-medium w-24 px-2">Part No.</td>
                                <td>
                                    <input v-model="variant.partNo" type="text" class="w-full rounded px-2 py-1" />
                                </td>
                            </tr>
                            <tr class="border-b">
                                <td class="bg-gray-100 font-medium w-24 px-2">Type</td>
                                <td>
                                    <input v-model="variant.type" type="text" class="w-full rounded px-2 py-1" />
                                </td>
                                <td class="bg-gray-100 font-medium w-24 px-2">Model</td>
                                <td>
                                    <input v-model="variant.model" type="text" class="w-full rounded px-2 py-1" />
                                </td>
                                <td class="bg-gray-100 font-medium px-2">Size</td>
                                <td>
                                    <input v-model="variant.size" type="text" class="w-full rounded px-2 py-1" />
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-gray-100 font-medium px-2">Color</td>
                                <td>
                                    <input v-model="variant.color" type="text" class="w-full rounded px-2 py-1" />
                                </td>
                                <td class="bg-gray-100 font-medium px-2">Material</td>
                                <td>
                                    <input v-model="variant.material" type="text" class="w-full rounded px-2 py-1" />
                                </td>
                                <td class="bg-gray-100 font-medium px-2">UOM</td>
                                <td colspan="3">
                                    <input v-model="variant.uom" type="text" class="w-full rounded px-2 py-1" />
                                </td>
                            </tr>

                            <!-- Image Upload Grid -->
                            <tr>
                            <td colspan="6" class="border-t p-4">
                                <div>
                                <h3 class="text-gray-600 text-sm font-semibold mb-2">Upload Images</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div
                                    v-for="(image, imgIndex) in variant.images"
                                    :key="imgIndex"
                                    class="relative flex flex-col items-center border-2 border-dashed rounded-lg p-3 hover:border-blue-400 transition cursor-pointer"
                                    @dragover.prevent
                                    @drop.prevent="handleDrop($event, variant, imgIndex)"
                                    >
                                    <!-- If image exists -->
                                    <div v-if="image.url" class="relative">
                                        <img
                                        :src="image.url"
                                        class="w-32 h-32 object-cover rounded-lg shadow"
                                        />
                                        <button
                                        @click.stop="removeImage(variant, imgIndex)"
                                        class="absolute top-1 right-1 bg-black/60 text-white rounded-full p-1 hover:bg-red-600 transition"
                                        >
                                        ✕
                                        </button>
                                    </div>

                                    <!-- If no image -->
                                    <div v-else class="flex flex-col items-center text-gray-400">
                                        <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-10 w-10 mb-1 text-gray-400"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 16l5-5 4 4 5-5 4 4M3 7h18" />
                                        </svg>
                                        <p class="text-xs mb-2">Click or drag to upload</p>
                                        <input
                                        type="file"
                                        accept="image/*"
                                        class="absolute inset-0 opacity-0 cursor-pointer"
                                        @change="handleFileUpload($event, variant, imgIndex)"
                                        />
                                    </div>
                                    </div>
                                </div>
                                </div>
                            </td>
                            </tr>

                            <!-- Delete Button -->
                            <tr>
                            <td colspan="6" class="border-t py-2">
                                <div class="flex justify-end">
                                <button
                                    @click="deleteVariant(index)"
                                    class="bg-red-500 hover:bg-red-600 text-white rounded p-1 mx-2"
                                >
                                    <TrashIcon class="w-4 h-4" />
                                </button>
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
                    Save
                    </button>
                </div>

            </div>
        </pageCard>
    </navigation>
</template>

<script setup>
    import { ref, onMounted, reactive } from "vue";
    import { TrashIcon, PencilSquareIcon, PlusIcon } from '@heroicons/vue/24/solid';
    import navigation from "@/components/layouts/navigation.vue";
    import pageCard from "@/components/card.vue";

    
const variants = ref([
  {
    brand: "",
    itemCode: "",
    partNo: "",
    type: "",
    model: "",
    size: "",
    color: "",
    material: "",
    uom: "",
    images: [{ url: "" }, { url: "" }, { url: "" }], // 3x3
  },
]);

const addVariant = () => {
  variants.value.push({
    brand: "",
    itemCode: "",
    partNo: "",
    type: "",
    model: "",
    size: "",
    color: "",
    material: "",
    uom: "",
    images: Array(3).fill({ url: "" }),
  });
};

const deleteVariant = (index) => {
  variants.value.splice(index, 1);
};

const handleFileUpload = (e, variant, imgIndex) => {
  const file = e.target.files[0];
  if (file) {
    variant.images[imgIndex] = { url: URL.createObjectURL(file) };
  }
};

const handleDrop = (e, variant, imgIndex) => {
  const file = e.dataTransfer.files[0];
  if (file) {
    variant.images[imgIndex] = { url: URL.createObjectURL(file) };
  }
};

const removeImage = (variant, imgIndex) => {
  variant.images[imgIndex] = { url: "" };
};
</script>
