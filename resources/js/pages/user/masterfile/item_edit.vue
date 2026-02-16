<script setup>
import { ref, onMounted, computed } from "vue";
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
const categoryName = ref("");

const variants = ref([]);

/* ------------------- COMPUTED ------------------- */
const hasNewVariants = computed(() =>
  variants.value.some(v => v.isNew)
);

/* ------------------- LOAD ITEM ------------------- */
onMounted(async () => {
  if (itemId.value) await loadItem(itemId.value);
});

const loadItem = async (id) => {
  const res = await axios.get(`/api/items/${id}`);
  const item = res.data;

  itemCode.value = item.item_code;
  itemDescription.value = item.item_description;
  sub_cat_name.value = item.sub_cat_name;
  categoryName.value = item.category_name;

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
};

/* ------------------- VARIANT FUNCTIONS ------------------- */
const addVariant = () => {
    // Get max index from all variants
    let maxIndex = 0;
    variants.value.forEach(v => {
      const parts = v.variant_item_code.split('-');
      const num = parseInt(parts[parts.length - 1], 10);
      if (!isNaN(num) && num > maxIndex) maxIndex = num;
    });

    const nextIndex = maxIndex + 1;

  variants.value.push({
    id: null,
    variant_item_code: `${itemCode.value}-${String(nextIndex).padStart(3, "0")}`,
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

const deleteVariant = (index) => {
  if (!variants.value[index].isNew) return;

  // Remove the variant
  variants.value.splice(index, 1);

  // Re-sequence remaining NEW variants only
  // Start numbering after last existing variant
  let existingMax = 0;
  variants.value.forEach(v => {
    if (!v.isNew) {
      const parts = v.variant_item_code.split('-');
      const num = parseInt(parts[parts.length - 1], 10);
      if (!isNaN(num) && num > existingMax) existingMax = num;
    }
  });

  let seq = existingMax + 1;
  variants.value.forEach(v => {
    if (v.isNew) {
      v.variant_item_code = `${itemCode.value}-${String(seq).padStart(4, "0")}`;
      seq++;
    }
  });
};

/* ------------------- IMAGE FUNCTIONS ------------------- */
const handleFileUpload = (e, variant, imgIndex) => {
  if (variant.readonly) return;

  const file = e.target.files[0];
  if (!file) return;

  variant.images[imgIndex].file = file;
  variant.images[imgIndex].url = URL.createObjectURL(file);
};

const removeImage = (variant, imgIndex) => {
  if (variant.readonly) return;

  variant.images[imgIndex].url = "";
  delete variant.images[imgIndex].file;
};

/* ------------------- SAVE NEW VARIANTS ------------------- */
const saveNewVariants = async () => {
  const newVariants = variants.value.filter(v => v.isNew);
  if (!newVariants.length) return;

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

    v.images.forEach((img, idx) => {
      if (img.file) {
        formData.append(`variants[${i}][images][${idx}]`, img.file);
      }
    });
  });

  const res = await axios.post(
    `/api/items/${itemId.value}/variants`,
    formData,
    { headers: { "Content-Type": "multipart/form-data" } }
  );

  // ✅ IMPORTANT PART
  newVariants.forEach(v => {
    v.isNew = false;
    v.readonly = true;
  });
};
</script>

<template>
	<navigation>
		<pageCard>

		<!-- HEADER -->
		<div class="flex items-center justify-between mb-6">
			<div>
			<h2 class="text-2xl font-bold text-gray-800">
				View Item / Add Variants
			</h2>
			<p class="text-sm text-gray-500">
				Manage item details and add new variants
			</p>
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

			<div>
				<label class="form-label">Item Code</label>
				<input class="form-input bg-gray-100" :value="itemCode" readonly />
			</div>

			<div>
				<label class="form-label">Sub Category</label>
				<input class="form-input bg-gray-100" :value="sub_cat_name" readonly />
			</div>

			<div>
				<label class="form-label">Category</label>
				<input class="form-input bg-gray-100" :value="categoryName" readonly />
			</div>

			</div>

			<div class="mt-4">
			<label class="form-label">Item Description</label>
			<input class="form-input bg-gray-100" :value="itemDescription" readonly />
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
				v-if="variant.isNew"
				@click="deleteVariant(index)"
				class="text-red-600 hover:bg-red-50 p-2 rounded"
				>
				<TrashIcon class="w-5 h-5"/>
				</button>
			</div>

			<!-- Variant Body -->
			<div class="p-4 grid md:grid-cols-3 gap-4">

				<div>
				<label class="form-label">Brand</label>
				<input v-model="variant.brand" class="form-input"
					:readonly="variant.readonly"/>
				</div>

				<div>
				<label class="form-label">Part No</label>
				<input v-model="variant.partNo" class="form-input"
					:readonly="variant.readonly"/>
				</div>

				<div>
				<label class="form-label">Type</label>
				<input v-model="variant.type" class="form-input"
					:readonly="variant.readonly"/>
				</div>

				<div>
				<label class="form-label">Model</label>
				<input v-model="variant.model" class="form-input"
					:readonly="variant.readonly"/>
				</div>

				<div>
				<label class="form-label">Size</label>
				<input v-model="variant.size" class="form-input"
					:readonly="variant.readonly"/>
				</div>

				<div>
				<label class="form-label">Color</label>
				<input v-model="variant.color" class="form-input"
					:readonly="variant.readonly"/>
				</div>

				<div>
				<label class="form-label">Material</label>
				<input v-model="variant.material" class="form-input"
					:readonly="variant.readonly"/>
				</div>

				<div>
				<label class="form-label">UOM</label>
				<input v-model="variant.uom" class="form-input"
					:readonly="variant.readonly"/>
				</div>

				<!-- NEW FIELDS -->
				<div>
				<label class="form-label">Rating</label>
				<input class="form-input"
					:readonly="variant.readonly"/>
				</div>

			</div>

			<!-- Other Specs -->
			<div class="px-4 pb-4">
				<label class="form-label">Other Specs</label>
				<textarea class="form-input"
				rows="3"
				:readonly="variant.readonly">
				</textarea>
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
					:disabled="variant.readonly"
					@change="handleFileUpload($event, variant, imgIndex)"
					/>

					<button
					v-if="image.url && !variant.readonly"
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

		<!-- SAVE BAR -->
		<div
			v-if="hasNewVariants"
			class="sticky bottom-0 bg-white border-t mt-8 pt-4 flex justify-end"
		>
			<button
			@click="saveNewVariants"
			class="btn-success"
			>
			Save New Variants
			</button>
		</div>

		</pageCard>
	</navigation>
</template>

