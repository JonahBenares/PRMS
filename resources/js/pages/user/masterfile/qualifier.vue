<script setup>
    import { ref, reactive, onMounted } from "vue";
    import axios from "axios";
    import { Bars3Icon, PlusIcon, PencilSquareIcon, XMarkIcon } from '@heroicons/vue/24/solid';

    import { useTable } from '@/composables/useTable'
    import searchbox from '@/composables/searchbox.vue';
    import pagination from "@/composables/pagination.vue";
    import navigation from "@/components/layouts/navigation.vue";
    import modal from "@/components/modal.vue";

    const {
        items,
        search,
        page,
        perPage,
        total,
        lastPage,
        loading,
        fetchData
    } = useTable('/api/qualifiers')

    onMounted(fetchData)

    const isSaving = ref(false)

    const showModal = ref(false)
    const isEdit = ref(false)

    const modalItem = reactive({
        id: null,
        qualifier_name: ""
    })

    const errors = reactive({
        qualifier_name: ""
    })

    const openAddModal = () => {
        isEdit.value = false
        Object.assign(modalItem, { id: null, qualifier_name: "" })
        Object.assign(errors, { qualifier_name: "" })
        showModal.value = true
    }

    const openEditModal = (item) => {
        isEdit.value = true
        Object.assign(modalItem, item)
        Object.assign(errors, { qualifier_name: "" })
        showModal.value = true
    }

    const saveQualifier = async () => {
        if (isSaving.value) return

        Object.assign(errors, { qualifier_name: "" })

        if (!modalItem.qualifier_name) {
            errors.qualifier_name = "Qualifier name is required."
            return
        }

        try {
            isSaving.value = true

            if (isEdit.value) {
                await axios.put(`/api/qualifiers/${modalItem.id}`, modalItem)
            } else {
                await axios.post('/api/qualifiers', modalItem)
            }

            await fetchData()
            showModal.value = false

        } catch (err) {
           if (err.response?.data?.errors) {
               const backendErrors = err.response.data.errors

               Object.keys(backendErrors).forEach(key => {
                   errors[key] = backendErrors[key][0] // get FIRST message only
               })
           }
           console.error(err)
        } finally {
            isSaving.value = false
        }
    }
</script>
<template>
    <navigation>
        <section class="items-center justify-center py-8 fade-up">
            <div class="bg-white border border-white/20 rounded-2xl shadow-xl max-w-6xl mx-auto">
            <!-- Header -->
                <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                    <div class="flex flex-col gap-1">
                        <h2 class="text-2xl font-bold text-gray-600">Qualifiers</h2>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto">

                        <div class="flex-1 sm:flex-none w-full sm:w-96">
                            <searchbox v-model="search" />
                        </div>

                        <button
                        @click="openAddModal"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2
                            px-4 py-2 bg-blue-600 text-white text-sm font-medium
                            rounded-lg shadow hover:bg-blue-700 transition">
                            <PlusIcon class="w-4 h-4"/>
                            Add Qualifier
                        </button>

                    </div>
                </div>
                <div class="border-b">
                <pagination
                    :page="page"
                    :per-page="perPage"
                    :last-page="lastPage"
                    :total="total"
                    @update:page="page = $event"
                    @update:perPage="perPage = $event"
                />
                </div>
                <!-- Table -->
                <div class="overflow-hidden">
                <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-100 sticky top-0 z-10">
                <tr class="text-gray-600 uppercase text-xs tracking-wide">
                <th class="px-6 py-3">Qualifier Name</th>
                <th class="px-6 py-3 w-1">
                    <div class="flex justify-center">
                        <Bars3Icon class="size-4"/>
                    </div>
                </th>
                </tr>
                </thead>

                <tbody class="divide-y">
                <tr v-for="item in items" :key="item.id" class="hover:bg-gray-50 transition">
                <td class="px-6 py-2 font-medium text-gray-800">
                    {{ item.qualifier_name }}
                </td>

                <td class="px-6 py-2 text-right">
                    <button
                        @click="openEditModal(item)"
                        class="inline-flex items-center justify-center p-2 rounded-md text-blue-600 hover:bg-blue-50">
                        <PencilSquareIcon class="w-4 h-4"/>
                    </button>
                </td>
                </tr>

                <tr v-if="!loading && items.length === 0">
                <td colspan="2" class="py-10 text-center text-gray-500">
                No qualifiers found.
                </td>
                </tr>
                </tbody>
                </table>
                </div>
                <div class="border-t">
                <pagination
                    :page="page"
                    :per-page="perPage"
                    :last-page="lastPage"
                    :total="total"
                    @update:page="page = $event"
                    @update:perPage="perPage = $event"
                />
                </div>
            </div>
        </section>

        <!-- Modal -->
        <modal v-model="showModal">
            <template #title>
            {{ isEdit ? 'Edit Qualifier' : 'Add Qualifier' }}
            </template>

            <template #close-icon>
            <XMarkIcon class="w-5 h-5"/>
            </template>

            <div>
            <label class="text-sm font-medium">Qualifier Name</label>
            <input
                v-model="modalItem.qualifier_name"
                :class="['mt-1 w-full border rounded-lg px-3 py-2 text-sm', errors.qualifier_name ? 'input-error' : '']"
            />
            <span v-if="errors.qualifier_name" class="text-red-500 text-xs">
            {{ errors.qualifier_name }}
            </span>
            </div>

            <template #footer>
            <button
                @click="showModal=false"
                class="px-4 py-2 text-sm bg-gray-100 rounded-lg"
                :disabled="isSaving">
            Cancel
            </button>

            <button
                @click="saveQualifier"
                class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg"
                :disabled="isSaving">
            {{ isSaving ? 'Saving...' : 'Save' }}
            </button>
            </template>
        </modal>
    </navigation>
</template>
