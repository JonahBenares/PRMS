import { ref, watch } from 'vue'
import axios from 'axios'
import { debounce } from 'lodash-es'

export function useTable(url) {
	const items = ref([])
	const loading = ref(false)

	const search = ref('')
	const page = ref(1)
	const perPage = ref(10)

	const total = ref(0)
	const lastPage = ref(1)

	// --- FETCH DATA ---
	const fetchData = async () => {
		loading.value = true
		try {
		const { data } = await axios.get(url, {
			params: {
			search: search.value,
			page: page.value,
			per_page: perPage.value
			}
		})

		items.value = data.data
		total.value = data.total
		lastPage.value = data.last_page
		} finally {
		loading.value = false
		}
	}

	// --- DEBOUNCED FETCH (for search only) ---
	const debouncedFetch = debounce(() => {
		fetchData()
	}, 400)

	// 🔍 When search changes → reset page + debounce
	watch(search, () => {
		page.value = 1
		debouncedFetch()
	})

	// 📄 When page or perPage changes → fetch immediately
	watch(
		() => [page.value, perPage.value],
		() => {
		fetchData()
		},
		{ immediate: true }
	)

	return {
		items,
		search,
		page,
		perPage,
		total,
		lastPage,
		loading,
		fetchData
	}
}
