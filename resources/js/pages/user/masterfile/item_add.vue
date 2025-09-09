<template>
    <navigation>
		<section class="items-center justify-center py-8">
			<div class="bg-white rounded-lg shadow-lg max-w-6xl mx-auto">
				<!-- Header -->
				<div class="px-6 py-4 flex justify-between items-center mb-4 bg-white border-b rounded-t-lg">
					<h2 class="text-lg font-semibold">Add Item</h2>
				</div>

				<!-- Table -->
				<div class="px-6 pt-2 pb-6">
                            
                            <!-- Sub Category / Category -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                <label class="block text-xs text-gray-500 uppercase mb-1">Sub Category</label>
                                <select class="w-full border rounded-md px-2 py-2 text-sm shadow-sm">
                                    <option>Select Sub Category</option>
                                </select>
                                </div>
                                <div>
                                <label class="block text-xs text-gray-500 uppercase mb-1">Category</label>
                                <input type="text" class="w-full border rounded-md px-2 py-2 text-sm" placeholder="Category" readonly>
                                </div>
                            </div>

                            <!-- Item Description / Location / Warehouse -->
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                                <div class="md:col-span-2">
                                <label class="block text-xs text-gray-500 uppercase mb-1">Item Description</label>
                                <input type="text" class="w-full border rounded-md px-2 py-2 text-sm" placeholder="Item Description">
                                </div>
                                <div>
                                <label class="block text-xs text-gray-500 uppercase mb-1">Location</label>
                                <select class="w-full border rounded-md px-2 py-2 text-sm shadow-sm">
                                    <option>Select Location</option>
                                </select>
                                </div>
                                <div>
                                <label class="block text-xs text-gray-500 uppercase mb-1">Warehouse</label>
                                <select class="w-full border rounded-md px-2 py-2 text-sm shadow-sm">
                                    <option>Select Warehouse</option>
                                </select>
                                </div>
                            </div>

                            <!-- Rack / Group / Reorder / Beginning -->
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                                <div>
                                <label class="block text-xs text-gray-500 uppercase mb-1">Rack</label>
                                <select class="w-full border rounded-md px-2 py-2 text-sm shadow-sm">
                                    <option>Select Rack</option>
                                </select>
                                </div>
                                <div>
                                <label class="block text-xs text-gray-500 uppercase mb-1">Group</label>
                                <select class="w-full border rounded-md px-2 py-2 text-sm shadow-sm">
                                    <option>Select Group</option>
                                </select>
                                </div>
                                <div>
                                <label class="block text-xs text-gray-500 uppercase mb-1">Reorder Point</label>
                                <input type="text" class="w-full border rounded-md px-2 py-2 text-sm" placeholder="Reorder Point">
                                </div>
                                <div>
                                <label class="block text-xs text-gray-500 uppercase mb-1">Beginning Balance</label>
                                <input type="text" class="w-full border rounded-md px-2 py-2 text-sm" placeholder="Beginning Balance">
                                </div>
                            </div>

                            <!-- Variant Items -->
                            <div class="mt-8">
                                <div class="flex justify-between items-center">
                                <p class="font-bold">Variant Items</p>
                                <button class="flex items-center bg-blue-600 text-white text-sm px-3 py-1 rounded-md shadow hover:bg-blue-700">
                                    + Add Variant
                                </button>
                                </div>
                                <hr class="my-2 border-gray-300">

                                <div class="border border-gray-200 rounded-md overflow-hidden">
                                <table class="w-full text-sm">
                                    <tbody>
                                    <tr class="bg-gray-100 text-gray-600 font-semibold text-xs">
                                    <td class="px-2 py-2 w-32">Supplier</td>
                                    <td colspan="9">
                                        <select class="w-full border px-2 py-1 rounded-md">
                                        <option>Select Supplier</option>
                                        </select>
                                    </td>
                                    <td class="px-2 py-2 w-20">PN No</td>
                                    <td><input class="w-full border px-2 py-1 rounded-md"></td>
                                    </tr>
                                    <tr>
                                    <td class="px-2 py-2 bg-gray-100 text-gray-600 font-semibold text-xs">Brand</td>
                                    <td colspan="7"><input type="text" class="w-full border px-2 py-1 rounded-md"></td>
                                    <td class="px-2 py-2 bg-gray-100 text-gray-600 font-semibold text-xs">Cat No</td>
                                    <td><input class="w-full border px-2 py-1 rounded-md"></td>
                                    <td class="px-2 py-2 bg-gray-100 text-gray-600 font-semibold text-xs">Serial No</td>
                                    <td colspan="2"><input class="w-full border px-2 py-1 rounded-md"></td>
                                    </tr>
                                    </tbody>
                                </table>
                                </div>
                            </div>

                            <!-- Upload Images -->
                            <div class="mt-8">
                                <div class="flex items-center space-x-2 border-b pb-2 mb-4">
                                <h6 class="font-semibold">Upload Images</h6>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 uppercase mb-1">Image 1</label>
                                    <input type="file" class="w-full border rounded-md px-2 py-2 text-sm">
                                    <div class="mt-2 w-24 h-24 border rounded-md overflow-hidden">
                                    <img src="#" alt="Preview" class="object-cover w-full h-full">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 uppercase mb-1">Image 2</label>
                                    <input type="file" class="w-full border rounded-md px-2 py-2 text-sm">
                                    <div class="mt-2 w-24 h-24 border rounded-md overflow-hidden">
                                    <img src="#" alt="Preview" class="object-cover w-full h-full">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 uppercase mb-1">Image 3</label>
                                    <input type="file" class="w-full border rounded-md px-2 py-2 text-sm">
                                    <div class="mt-2 w-24 h-24 border rounded-md overflow-hidden">
                                    <img src="#" alt="Preview" class="object-cover w-full h-full">
                                    </div>
                                </div>
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
			</div>

				
		</section>
    </navigation>
</template>

<script setup>
    import { ref, onMounted, reactive } from "vue";
    import { TrashIcon, PencilSquareIcon, PlusIcon } from '@heroicons/vue/24/solid';
    import navigation from "@/components/layouts/navigation.vue";
</script>
