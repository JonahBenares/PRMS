<script setup>
defineProps({
  modelValue: String,
  placeholder: {
    type: String,
    default: 'Search...'
  }
})

defineEmits(['update:modelValue'])
</script>

<template>
  <input
    :value="modelValue"
    @input="$emit('update:modelValue', $event.target.value)"
    :placeholder="placeholder"
    class="border px-3 py-2 rounded w-full text-sm"
  />
</template>
