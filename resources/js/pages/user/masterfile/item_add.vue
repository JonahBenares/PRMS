<script setup>
import { ref, onMounted, watch } from "vue";
import axios from "axios";
import { TrashIcon, PlusIcon } from "@heroicons/vue/24/solid";
import navigation from "@/components/layouts/navigation.vue";
import pageCard from "@/components/card.vue";

/* ------------------- STATE ------------------- */
const subCategories = ref([]);
const selectedSubCategory = ref(null);
const categoryName = ref("");
const itemDescription = ref("");
const isSaved = ref(false);

const itemCode = ref(""); // main item code
const variants = ref([]); // variants list

/* ---------------- FETCH INITIAL CODES ---------------- */
const fetchNextCodes = async () => {
  try {
    const res = await axios.get("/api/items/last_series");

    // Use what backend returns directly
    itemCode.value = res.data.item_code;
    variants.value = [];
    
    // variants.value = [
    //   {
    //     variant_item_code: res.data.first_variant_code,
    //     brand: "",
    //     partNo: "",
    //     type: "",
    //     model: "",
    //     size: "",
    //     color: "",
    //     material: "",
    //     uom: "",
    //     isNew: true,
    //     images: [{ url: "" }, { url: "" }, { url: "" }]
    //   }
    // ];
  } catch (error) {
    console.error("Error fetching next codes:", error);
  }
};


/* ------------------- LOAD SUBCATEGORIES ---------------- */
onMounted(async () => {
  try {
    const res = await axios.get("/api/subcategories");
    subCategories.value = res.data;
    await fetchNextCodes();
  } catch (error) {
    console.error("Error loading subcategories:", error);
  }
});

/* ---------------- SUBCATEGORY → CATEGORY ---------------- */
const onSubCategoryChange = () => {
  categoryName.value = selectedSubCategory.value
    ? selectedSubCategory.value.category_name
    : "";
};

/* ------------------- VARIANT FUNCTIONS ---------------- */
const addVariant = () => {
  const lastIndex = variants.value.length;
  const nextVariantNumber = lastIndex + 1;
  const nextVariantCode =
    itemCode.value + "-" + String(nextVariantNumber).padStart(3, "0");

  variants.value.push({
    variant_item_code: nextVariantCode,
    brand: "",
    partNo: "",
    type: "",
    model: "",
    size: "",
    color: "",
    material: "",
    uom: "",
    isNew: true,
    images: [{ url: "" }, { url: "" }, { url: "" }]
  });
};

const deleteVariant = (index) => {
  variants.value.splice(index, 1);

  // Re-sequence variant codes
  variants.value.forEach((v, i) => {
    v.variant_item_code = itemCode.value + "-" + String(i + 1).padStart(3, "0");
  });
};

const canDeleteVariant = (variant) => !isSaved.value || variant.isNew;

/* ------------------- IMAGE FUNCTIONS ---------------- */
const handleFileUpload = (e, variant, imgIndex) => {
  const file = e.target.files[0];
  if (file) {
    variant.images[imgIndex].file = file;  // include file object
    variant.images[imgIndex].url = URL.createObjectURL(file);
  }
};

const removeImage = (variant, imgIndex) => {
  variant.images[imgIndex].url = "";
};

/* ------------------- SAVE ITEM ---------------- */
const saveItem = async () => {
  try {
    const formData = new FormData();

    formData.append("item_code", itemCode.value);
    formData.append("sub_category_id", selectedSubCategory.value.id);
    formData.append("item_description", itemDescription.value);

    variants.value.forEach((variant, vIndex) => {
      formData.append(`variants[${vIndex}][variant_item_code]`, variant.variant_item_code);
      formData.append(`variants[${vIndex}][brand]`, variant.brand);
      formData.append(`variants[${vIndex}][partNo]`, variant.partNo);
      formData.append(`variants[${vIndex}][type]`, variant.type);
      formData.append(`variants[${vIndex}][model]`, variant.model);
      formData.append(`variants[${vIndex}][size]`, variant.size);
      formData.append(`variants[${vIndex}][color]`, variant.color);
      formData.append(`variants[${vIndex}][material]`, variant.material);
      formData.append(`variants[${vIndex}][uom]`, variant.uom);

      variant.images.forEach((img, imgIndex) => {
        if (img.file) {
          formData.append(
            `variants[${vIndex}][images][${imgIndex}]`,
            img.file
          );
        }
      });
    });

    const res = await axios.post("/api/items", formData, {
      headers: { "Content-Type": "multipart/form-data" }
    });

    if (res.data.status === "success") {
      window.location.href = `/masterfile/items/edit/${res.data.item_id}`;
    }
  } catch (error) {
    console.error(error);
    alert("Failed to save item");
  }
};

</script>

<template>
<navigation>
  <pageCard>
    <div class="flex justify-between items-center border-b pb-3 mb-4">
      <h2 class="text-lg font-semibold">Add Item</h2>
    </div>

    <!-- ITEM CODE -->
    <div class="mb-4">
      <label class="text-sm text-gray-500">Item Code</label>
      <input
        type="text"
        class="w-full border rounded px-2 py-2 text-sm bg-gray-100"
        :value="itemCode"
        readonly
      />
    </div>

    <!-- SUB CATEGORY / CATEGORY -->
    <div class="grid md:grid-cols-2 gap-4">
      <div>
        <label class="text-sm text-gray-500">Sub Category</label>
           <select
            v-model="selectedSubCategory"
            @change="onSubCategoryChange"
            class="w-full border rounded px-2 py-2 text-sm"
          >
            <option :value="null">Select Sub Category</option>
            <option
              v-for="sub in subCategories"
              :key="sub.id"
              :value="sub"
            >
              {{ sub.sub_cat_name }}
            </option>
          </select>
      </div>

      <div>
        <label class="text-sm text-gray-500">Category</label>
        <input
            type="text"
            class="w-full border rounded px-2 py-2 text-sm bg-gray-50"
            :value="categoryName"
            readonly
            />
      </div>
    </div>

    <!-- ITEM DESCRIPTION -->
    <div class="mt-4">
      <label class="text-sm text-gray-500">Item Description</label>
      <input
        v-model="itemDescription"
        type="text"
        class="w-full border rounded px-2 py-2 text-sm"
      />
    </div>

    <!-- VARIANTS -->
    <div class="mt-6">
      <div class="flex justify-between items-center mb-2">
        <h3 class="font-semibold">Variant Items</h3>
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

  <div>
    <label class="text-xs text-gray-500">Brand</label>
    <input
      v-model="variant.brand"
      class="w-full border rounded px-2 py-1"
      type="text"
    />
  </div>

  <div>
    <label class="text-xs text-gray-500">Part No</label>
    <input
      v-model="variant.partNo"
      class="w-full border rounded px-2 py-1"
      type="text"
    />
  </div>

  <div>
    <label class="text-xs text-gray-500">Type</label>
    <input
      v-model="variant.type"
      class="w-full border rounded px-2 py-1"
      type="text"
    />
  </div>

  <div>
    <label class="text-xs text-gray-500">Model</label>
    <input
      v-model="variant.model"
      class="w-full border rounded px-2 py-1"
      type="text"
    />
  </div>

  <div>
    <label class="text-xs text-gray-500">Size</label>
    <input
      v-model="variant.size"
      class="w-full border rounded px-2 py-1"
      type="text"
    />
  </div>

  <div>
    <label class="text-xs text-gray-500">Color</label>
    <input
      v-model="variant.color"
      class="w-full border rounded px-2 py-1"
      type="text"
    />
  </div>

  <div>
    <label class="text-xs text-gray-500">Material</label>
    <input
      v-model="variant.material"
      class="w-full border rounded px-2 py-1"
      type="text"
    />
  </div>

  <div>
    <label class="text-xs text-gray-500">Unit of Measure (UOM)</label>
    <input
      v-model="variant.uom"
      class="w-full border rounded px-2 py-1"
      type="text"
    />
  </div>
</div>
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
              @change="handleFileUpload($event, variant, imgIndex)"
            />

            <button
              v-if="image.url"
              @click.stop="removeImage(variant, imgIndex)"
              class="absolute top-1 right-1 bg-black/60 text-white rounded-full px-2"
            >
              ✕
            </button>
          </div>
        </div>

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
      <button
        @click="saveItem"
        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded"
      >
        Save Item
      </button>
    </div>
  </pageCard>
</navigation>
</template>