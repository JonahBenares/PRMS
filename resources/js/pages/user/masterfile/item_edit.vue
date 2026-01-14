<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";
import { TrashIcon, PlusIcon } from "@heroicons/vue/24/solid";
import navigation from "@/components/layouts/navigation.vue";
import pageCard from "@/components/card.vue";

/* ------------------- STATE ------------------- */
const route = useRoute();
const itemId = ref(route.params.id || null);

const itemCode = ref("");
const itemDescription = ref("");
const sub_cat_name = ref("");
const selectedSubCategory = ref(null);
const categoryName = ref("");

const variants = ref([]); // all variants (existing + new)
const isSaved = ref(false);

/* ------------------- LOAD ITEM ---------------- */
onMounted(async () => {
  if (itemId.value) {
    await loadItem(itemId.value);
  }
});

const loadItem = async (id) => {
  try {
    const res = await axios.get(`/api/items/${id}`);
    const item = res.data;

    itemCode.value = item.item_code;
    itemDescription.value = item.item_description;
    sub_cat_name.value = item.sub_cat_name;
    categoryName.value = item.category_name;

    // Map existing variants (readonly)
    variants.value = item.item_variants.map(v => ({
      id: v.id,
      variant_item_code: v.variant_item_code,
      brand: v.brand,
      partNo: v.part_no,
      type: v.type,
      model: v.model,
      size: v.size,
      color: v.color,
      material: v.material,
      uom: v.uom,
      images: [
        { url: v.img1 ? `/storage/items/${v.img1}` : "" },
        { url: v.img2 ? `/storage/items/${v.img2}` : "" },
        { url: v.img3 ? `/storage/items/${v.img3}` : "" }
      ],
      isNew: false,
      readonly: true
    }));

    isSaved.value = true;
  } catch (err) {
    console.error("Failed to load item:", err);
  }
};

/* ------------------- VARIANT FUNCTIONS ---------------- */
const addVariant = () => {
  const nextIndex = variants.value.length + 1;
  variants.value.push({
    id: null,
    variant_item_code: `${itemCode.value}-${String(nextIndex).padStart(3,"0")}`,
    brand: "",
    partNo: "",
    type: "",
    model: "",
    size: "",
    color: "",
    material: "",
    uom: "",
    images: [{ url: "" }, { url: "" }, { url: "" }],
    isNew: true,
    readonly: false
  });
};

const canDeleteVariant = (v) => v.isNew;

const deleteVariant = (index) => {
  if (!variants.value[index].isNew) return;
  variants.value.splice(index, 1);

  // Re-sequence variant codes for new variants
  variants.value.forEach((v, i) => {
    if (v.isNew) v.variant_item_code = `${itemCode.value}-${String(i+1).padStart(3,"0")}`;
  });
};

/* ------------------- IMAGE FUNCTIONS ---------------- */
const handleFileUpload = (e, variant, imgIndex) => {
  if (variant.readonly) return;
  const file = e.target.files[0];
  if (!file) return;

  // Assign file object for FormData
  variant.images[imgIndex].file = file;

  // Preview image
  variant.images[imgIndex].url = URL.createObjectURL(file);
};

const removeImage = (variant, imgIndex) => {
  if (variant.readonly) return;
  variant.images[imgIndex].url = "";
  delete variant.images[imgIndex].file;
};

/* ------------------- SAVE NEW VARIANTS ---------------- */
const saveNewVariants = async () => {
  try {
    const newVariants = variants.value.filter(v => v.isNew);
    if (!newVariants.length) return alert("No new variants to save");

    const formData = new FormData();

    newVariants.forEach((v, i) => {
      formData.append(`variants[${i}][variant_item_code]`, v.variant_item_code);
      formData.append(`variants[${i}][brand]`, v.brand || "");
      formData.append(`variants[${i}][partNo]`, v.partNo || "");
      formData.append(`variants[${i}][type]`, v.type || "");
      formData.append(`variants[${i}][model]`, v.model || "");
      formData.append(`variants[${i}][size]`, v.size || "");
      formData.append(`variants[${i}][color]`, v.color || "");
      formData.append(`variants[${i}][material]`, v.material || "");
      formData.append(`variants[${i}][uom]`, v.uom || "");
      formData.append(`variants[${i}][isNew]`, 1);

      v.images.forEach((img, idx) => {
        if (img.file) formData.append(`variants[${i}][images][${idx}]`, img.file);
      });
    });

    const res = await axios.post(`/api/items/${itemId.value}/variants`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });

    alert(res.data.message);

    // Reload item to include newly saved variants
    await loadItem(itemId.value);
  } catch (err) {
    console.error("Failed to save variants:", err);
    alert("Failed to save variants.");
  }
};
</script>


<template>
<navigation>
  <pageCard>
    <h2 class="text-lg font-semibold border-b pb-3 mb-4">View Item / Add Variants</h2>

    <!-- ITEM INFO -->
    <div class="grid md:grid-cols-2 gap-4 mb-4">
      <div>
        <label class="text-sm text-gray-500">Item Code</label>
        <input type="text" class="w-full border rounded px-2 py-2 bg-gray-100" :value="itemCode" readonly />
      </div>
      <div>
        <label class="text-sm text-gray-500">Sub Category</label>
        <input type="text" class="w-full border rounded px-2 py-2 bg-gray-50" :value="sub_cat_name" readonly />
      </div>
      <div>
        <label class="text-sm text-gray-500">Category</label>
        <input type="text" class="w-full border rounded px-2 py-2 bg-gray-50" :value="categoryName" readonly />
      </div>
      <div>
        <label class="text-sm text-gray-500">Description</label>
        <input type="text" class="w-full border rounded px-2 py-2 bg-gray-50" :value="itemDescription" readonly />
      </div>
    </div>

<!-- VARIANTS -->
<div class="mt-6">
  <div class="flex justify-between items-center mb-2">
    <h3 class="font-semibold">Variants</h3>
    <button
      @click="addVariant"
      class="flex items-center bg-blue-600 text-white text-sm px-3 py-1.5 rounded"
    >
      <PlusIcon class="w-4 h-4 mr-1" /> Add Variant
    </button>
  </div>

  <div
    v-for="(variant, index) in variants"
    :key="index"
    class="border rounded-lg mb-4 p-4 bg-white"
  >
    <div class="grid md:grid-cols-3 gap-3 text-sm">

      <!-- Variant Item Code -->
      <div>
        <label class="text-xs text-gray-500">Variant Item Code</label>
        <input
          type="text"
          class="w-full border rounded px-2 py-1 bg-gray-100"
          :value="variant.variant_item_code"
          readonly
        />
      </div>

      <!-- Brand -->
      <div>
        <label class="text-xs text-gray-500">Brand</label>
        <input
          v-model="variant.brand"
          class="w-full border rounded px-2 py-1"
          :readonly="variant.readonly"
          :class="variant.readonly ? 'bg-gray-100' : ''"
          type="text"
        />
      </div>

      <!-- Part No -->
      <div>
        <label class="text-xs text-gray-500">Part No</label>
        <input
          v-model="variant.partNo"
          class="w-full border rounded px-2 py-1"
          :readonly="variant.readonly"
          :class="variant.readonly ? 'bg-gray-100' : ''"
          type="text"
        />
      </div>

      <!-- Type -->
      <div>
        <label class="text-xs text-gray-500">Type</label>
        <input
          v-model="variant.type"
          class="w-full border rounded px-2 py-1"
          :readonly="variant.readonly"
          :class="variant.readonly ? 'bg-gray-100' : ''"
          type="text"
        />
      </div>

      <!-- Model -->
      <div>
        <label class="text-xs text-gray-500">Model</label>
        <input
          v-model="variant.model"
          class="w-full border rounded px-2 py-1"
          :readonly="variant.readonly"
          :class="variant.readonly ? 'bg-gray-100' : ''"
          type="text"
        />
      </div>

      <!-- Size -->
      <div>
        <label class="text-xs text-gray-500">Size</label>
        <input
          v-model="variant.size"
          class="w-full border rounded px-2 py-1"
          :readonly="variant.readonly"
          :class="variant.readonly ? 'bg-gray-100' : ''"
          type="text"
        />
      </div>

      <!-- Color -->
      <div>
        <label class="text-xs text-gray-500">Color</label>
        <input
          v-model="variant.color"
          class="w-full border rounded px-2 py-1"
          :readonly="variant.readonly"
          :class="variant.readonly ? 'bg-gray-100' : ''"
          type="text"
        />
      </div>

      <!-- Material -->
      <div>
        <label class="text-xs text-gray-500">Material</label>
        <input
          v-model="variant.material"
          class="w-full border rounded px-2 py-1"
          :readonly="variant.readonly"
          :class="variant.readonly ? 'bg-gray-100' : ''"
          type="text"
        />
      </div>

      <!-- UOM -->
      <div>
        <label class="text-xs text-gray-500">Unit of Measure (UOM)</label>
        <input
          v-model="variant.uom"
          class="w-full border rounded px-2 py-1"
          :readonly="variant.readonly"
          :class="variant.readonly ? 'bg-gray-100' : ''"
          type="text"
        />
      </div>
    </div>

    <!-- Images -->
    <div class="grid grid-cols-3 gap-3 mt-4">
      <div
        v-for="(image, imgIndex) in variant.images"
        :key="imgIndex"
        class="border-2 border-dashed rounded-lg p-2 relative text-center"
      >
        <img
          v-if="image.url"
          :src="image.url"
          class="w-full h-28 object-cover rounded"
        />
        <span v-else class="text-xs text-gray-400 block mt-6">
          Image {{ imgIndex + 1 }}
        </span>

        <input
          type="file"
          accept="image/*"
          class="absolute inset-0 opacity-0 cursor-pointer"
          :disabled="variant.readonly"
          @change="handleFileUpload($event, variant, imgIndex)"
        />

        <button
          v-if="image.url && !variant.readonly"
          @click.stop="removeImage(variant, imgIndex)"
          class="absolute top-1 right-1 bg-black/60 text-white rounded-full px-2"
        >
          ✕
        </button>
      </div>
    </div>

    <!-- Delete -->
    <div class="flex justify-end mt-3">
      <button
        v-if="canDeleteVariant(variant)"
        @click="deleteVariant(index)"
        class="bg-red-500 text-white rounded p-1"
      >
        <TrashIcon class="w-4 h-4" />
      </button>
    </div>
  </div>
</div>

    <div class="flex justify-end border-t pt-4">
      <button @click="saveNewVariants" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">
        Save New Variants
      </button>
    </div>
  </pageCard>
</navigation>
</template>