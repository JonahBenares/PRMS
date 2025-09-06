<template> 
	<navigation>
		<section class="items-center justify-center py-8">
			<div class="w-full bg-white shadow-lg rounded-lg p-8 max-w-6xl mx-auto">
				<h2 class="text-xl font-semibold mb-2 ">PR List</h2>
				
			</div>
		</section>
	</navigation>
</template>

<script setup>
import { reactive } from "vue";
import { PlusIcon, XMarkIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/solid'
import navigation from "@/components/layouts/navigation.vue";
import { ref, computed , onMounted } from "vue";


</script>
