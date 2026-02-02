<script setup>
import { computed } from 'vue'

const props = defineProps({
  page: {
    type: Number,
    required: true
  },
  perPage: {
    type: Number,
    required: true
  },
  lastPage: {
    type: Number,
    required: true
  },
  total: {
    type: Number,
    required: true
  }
})

const emit = defineEmits([
  'update:page',
  'update:perPage'
])

const pageCount = computed(() => props.lastPage)

const visiblePages = computed(() => {
  const total = pageCount.value
  const current = props.page
  const delta = 2

  if (total <= 7) {
    return Array.from({ length: total }, (_, i) => i + 1)
  }

  const pages = new Set([1, total])

  for (let i = current - delta; i <= current + delta; i++) {
    if (i > 1 && i < total) pages.add(i)
  }

  return [...pages].sort((a, b) => a - b)
})
</script>

<template>
	<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 px-6 py-4 border-t">

		<!-- LEFT: Rows per page -->
		<div class="flex items-center gap-2 text-sm text-gray-600">
		<span>Rows per page</span>
		<select
			:value="perPage"
			@change="emit('update:perPage', Number($event.target.value))"
			class="px-2 py-1 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-gray-500"
		>
			<option :value="5">5</option>
			<option :value="10">10</option>
			<option :value="25">25</option>
			<option :value="50">50</option>
		</select>

		<span class="hidden sm:inline text-gray-400">
			• Page {{ page }} of {{ pageCount }}
		</span>
		</div>

		<!-- RIGHT: Pagination buttons -->
		<div class="flex items-center gap-1">
		<!-- First -->
		<button
			@click="emit('update:page', 1)"
			:disabled="page === 1"
			class="pagination-btn"
		>
			«
		</button>

		<!-- Prev -->
		<button
			@click="emit('update:page', page - 1)"
			:disabled="page === 1"
			class="pagination-btn"
		>
			‹
		</button>

		<!-- Page numbers -->
		<button
			v-for="n in visiblePages"
			:key="n"
			@click="emit('update:page', n)"
			:class="[
			'pagination-btn',
			page === n
				? 'bg-gray-400 text-white border-gray-400'
				: 'bg-white text-gray-700 hover:bg-gray-100'
			]"
		>
			{{ n }}
		</button>

		<!-- Next -->
		<button
			@click="emit('update:page', page + 1)"
			:disabled="page === pageCount"
			class="pagination-btn"
		>
			›
		</button>

		<!-- Last -->
		<button
			@click="emit('update:page', pageCount)"
			:disabled="page === pageCount"
			class="pagination-btn"
		>
			»
		</button>
		</div>
	</div>
</template>
