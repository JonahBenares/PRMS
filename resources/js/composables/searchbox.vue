<script setup>
defineProps({
  modelValue: String,
  placeholder: {
    type: String,
    default: 'Search...'
  }
})

const emit = defineEmits(['update:modelValue'])

const clear = () => {
  emit('update:modelValue', '')
}
</script>

<template>
	<div class="relative w-full">
		<input
		:value="modelValue"
		@input="emit('update:modelValue', $event.target.value)"
		:placeholder="placeholder"
		class="border px-3 py-2 pr-9 rounded-lg w-full text-sm"
		/>

		<!-- Clear button -->
		<button
		v-if="modelValue"
		@click="clear"
		type="button"
		class="absolute right-4 top-1/2 -translate-y-1/2
				text-gray-400 hover:text-gray-600 text-sm"
		>
		✕
		</button>
	</div>
</template>
