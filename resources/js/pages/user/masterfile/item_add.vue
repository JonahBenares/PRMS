<script setup>
import { ref, onMounted, watch } from "vue";
import axios from "axios";
import { TrashIcon, PlusIcon } from "@heroicons/vue/24/solid";
import navigation from "@/components/layouts/navigation.vue";
import pageCard from "@/components/card.vue";
import { useRouter } from "vue-router";

const router = useRouter();

const goBack = () => {
  router.push("/masterfile/items"); 
};

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
			<!-- HEADER -->
			<div class="flex items-center justify-between mb-6">
				<div>
					<h2 class="text-2xl font-bold text-gray-800">Add Item</h2>
					<p class="text-sm text-gray-500">Create item and manage its variants</p>
				</div>

				<!-- Back Button -->
				<button
				@click="goBack"
				class="flex items-center px-4 py-2 text-sm border rounded-lg hover:bg-gray-100 transition"
				>
				← Back to List
				</button>
			</div>
			<!-- ================= ITEM INFO ================= -->
			<div class="bg-gray-50 border rounded-xl p-5 mb-6">
				<h3 class="font-semibold text-gray-700 mb-4">Item Information</h3>

				<div class="grid md:grid-cols-3 gap-4">
					<!-- Item Code -->
					<div>
					<label class="form-label">Item Code</label>
					<input
						type="text"
						:value="itemCode"
						readonly
						class="form-input bg-gray-100 font-semibold"
					/>
					</div>

					<!-- Sub Category -->
					<div>
					<label class="form-label">Sub Category</label>
					<select
						v-model="selectedSubCategory"
						@change="onSubCategoryChange"
						class="form-input"
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

					<!-- Category -->
					<div>
					<label class="form-label">Category</label>
					<input
						:value="categoryName"
						readonly
						class="form-input bg-gray-100"
					/>
					</div>
				</div>

				<!-- Description -->
				<div class="mt-4">
					<label class="form-label">Item Description</label>
					<input
					v-model="itemDescription"
					class="form-input"
					placeholder="Enter item description..."
					/>
				</div>
			</div>

			<!-- ================= VARIANTS ================= -->
			<div class="flex justify-between items-center mb-3">
				<h3 class="text-lg font-semibold text-gray-800">Variants</h3>

				<button
					@click="addVariant"
					class="btn-primary"
				>
					<PlusIcon class="w-4 h-4 mr-1"/>
					Add Variant
				</button>
				</div>

			<!-- Variant Cards -->
			<div class="space-y-5">
			<div
				v-for="(variant, index) in variants"
				:key="index"
				class="bg-white border rounded-xl shadow-sm"
			>
				<!-- Variant Header -->
				<div class="flex justify-between items-center bg-gray-100 px-4 py-3 rounded-t-xl">
				<div class="font-semibold text-gray-700">
					Variant {{ index + 1 }}
					<span class="text-xs text-gray-500 ml-2">
					{{ variant.variant_item_code }}
					</span>
				</div>

				<button
					v-if="canDeleteVariant(variant)"
					@click="deleteVariant(index)"
					class="text-red-600 hover:bg-red-50 p-2 rounded"
				>
					<TrashIcon class="w-5 h-5"/>
				</button>
				</div>

				<!-- Variant Body -->
				<div class="p-4 grid md:grid-cols-3 gap-4">

				

				<div>
					<label class="form-label">Part No</label>
					<input v-model="variant.partNo" class="form-input"/>
				</div>

				<div>
					<label class="form-label">Type</label>
					<input v-model="variant.type" class="form-input"/>
				</div>

				<div>
					<label class="form-label">Model</label>
					<input v-model="variant.model" class="form-input"/>
				</div>

				<div>
					<label class="form-label">Size</label>
					<input v-model="variant.size" class="form-input"/>
				</div>

				<div>
					<label class="form-label">Color</label>
					<input v-model="variant.color" class="form-input"/>
				</div>

				<div>
					<label class="form-label">Material</label>
					<input v-model="variant.material" class="form-input"/>
				</div>

				<div>
					<label class="form-label">UOM</label>
					<input v-model="variant.uom" class="form-input"/>
				</div>

				<div>
					<label class="form-label">Rating</label>
					<input  class="form-input"/>
				</div>

				
				</div>
				<div class="px-4 pb-5">
					<label class="form-label">Other Specs</label>
					<textarea  class="form-input"></textarea>
				</div>
				<!-- Images -->
				<div class="px-4 pb-5">
				<label class="form-label mb-2 block">Images</label>

				<div class="grid grid-cols-3 gap-4">
					<div
					v-for="(image, imgIndex) in variant.images"
					:key="imgIndex"
					class="upload-tile"
					>
					<img
						v-if="image.url"
						:src="image.url"
						class="upload-img"
					/>

					<span v-else class="text-gray-400 text-xs">
						Upload Image {{ imgIndex + 1 }}
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
						class="upload-remove"
					>
						✕
					</button>
					</div>
				</div>
				</div>
			</div>
			</div>

			<!-- ================= SAVE BAR ================= -->
			<div class="sticky bottom-0 bg-white border-t mt-8 pt-4 flex justify-end">
			<button
				@click="saveItem"
				class="btn-success"
			>
				Save Item
			</button>
			</div>

		</pageCard>
	</navigation>
</template>
