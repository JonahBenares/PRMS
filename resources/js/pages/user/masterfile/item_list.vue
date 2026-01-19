<script setup>
import { reactive, ref, onMounted } from "vue";
import axios from "axios";
import { PlusIcon, EyeIcon, ChevronDownIcon, ChevronUpIcon } from '@heroicons/vue/24/solid';
import navigation from "@/components/layouts/navigation.vue";

// Item list
const items = reactive([]);

// Toggle variants
const toggleVariants = (item) => {
  item.showVariants = !item.showVariants;
};

// Fetch items from API
const fetchItems = async () => {
  try {
    const res = await axios.get("/api/items");
    items.splice(0); // clear
    res.data.forEach(i => items.push({ ...i, showVariants: false }));
  } catch (error) {
    console.error("Failed to fetch items:", error);
  }
};

onMounted(() => {
  fetchItems();
});
</script>

<template>
  <navigation>
    <section class="py-8">
      <div class="bg-white rounded-lg shadow-lg max-w-6xl mx-auto">

        <!-- Header -->
        <div class="px-6 py-4 flex justify-between items-center mb-4 border-b rounded-t-lg">
          <h2 class="text-lg font-semibold">Item List</h2>
          <a href="/masterfile/items/add" class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <PlusIcon class="w-5 h-5 mr-1" />
            Add Item
          </a>
        </div>

        <!-- Table -->
        <div class="px-6 pb-6">
          <table class="min-w-full text-sm text-left text-gray-700 border border-gray-200 rounded-lg">
            <thead class="bg-gray-100 text-gray-900 font-semibold">
              <tr>
                <th class="px-4 py-2 cursor-pointer">Item Code</th>
                <th class="px-4 py-2 cursor-pointer">Item Description</th>
                <th class="px-4 py-2 cursor-pointer">Category</th>
                <th class="px-4 py-2 cursor-pointer">Sub Category</th>
                <th class="px-4 py-2 cursor-pointer w-28">Actions</th>
              </tr>
            </thead>
            <tbody>
              <template v-for="item in items" :key="item.id">
                <!-- Main item row -->
                <tr class="hover:bg-gray-50">
                  <td class="px-4 py-2">{{ item.item_code }}</td>
                  <td class="px-4 py-2">{{ item.item_description }}</td>
                  <td class="px-4 py-2">{{ item.category_name }}</td>
                  <td class="px-4 py-2">{{ item.sub_cat_name }}</td>
                  <td class="px-4 py-2 flex space-x-1">
                  <!-- Toggle Variants -->
                  <button @click="toggleVariants(item)" class="p-2 bg-gray-300 rounded hover:bg-gray-400">
                  <ChevronDownIcon v-if="!item.showVariants" class="w-4 h-4" />
                  <ChevronUpIcon v-else class="w-4 h-4" />
                  </button>

                  <!-- View / Edit -->
                  <a :href="`/masterfile/items/edit/${item.id}`" class="p-2 bg-blue-500 text-white rounded hover:bg-blue-700">
                    <EyeIcon class="w-4 h-4" />
                  </a>
                </td>
                </tr>

                <!-- Collapsible variants row -->
                <tr v-if="item.showVariants">
                  <td colspan="5" class="bg-gray-50 px-4 py-2">
                    <table class="w-full text-sm border rounded">
                      <thead class="bg-gray-200 font-semibold">
                        <tr>
                          <th class="px-2 py-1">Variant Code</th>
                          <th class="px-2 py-1">Brand</th>
                          <th class="px-2 py-1">Model</th>
                          <th class="px-2 py-1">Size</th>
                          <th class="px-2 py-1">Color</th>
                          <th class="px-2 py-1">UOM</th>
                          <th class="px-2 py-1">Images</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="v in item.item_Variants" :key="v.id">
                          <td class="px-2 py-1">{{ v.variant_item_code }}</td>
                          <td class="px-2 py-1">{{ v.brand }}</td>
                          <td class="px-2 py-1">{{ v.model }}</td>
                          <td class="px-2 py-1">{{ v.size }}</td>
                          <td class="px-2 py-1">{{ v.color }}</td>
                          <td class="px-2 py-1">{{ v.uom }}</td>
                          <td class="px-2 py-1 flex gap-1">
                            <template v-if="v.img1">
                              <img :src="`/storage/items/${v.img1}`" class="w-6 h-6 object-cover rounded" />
                            </template>
                            <template v-if="v.img2">
                              <img :src="`/storage/items/${v.img2}`" class="w-6 h-6 object-cover rounded" />
                            </template>
                            <template v-if="v.img3">
                              <img :src="`/storage/items/${v.img3}`" class="w-6 h-6 object-cover rounded" />
                            </template>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>

      </div>
    </section>
  </navigation>
</template>
