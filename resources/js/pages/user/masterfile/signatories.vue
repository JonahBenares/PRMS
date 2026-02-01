<script setup>
    import { ref, reactive } from "vue";
    import { Bars3Icon, PlusIcon, PencilSquareIcon, XMarkIcon , Cog6ToothIcon} from '@heroicons/vue/24/solid';
    import navigation from "@/components/layouts/navigation.vue";
    import modal from "@/components/modal.vue";

    // Company / Location state
    const companies = ref(['CENPRI', 'ENERGREEN']);
    const locations = ref({
    'CENPRI': ['Bacolod', 'Bago'],
    'ENERGREEN': ['Manila']
    });
    const selectedCompany = ref(companies.value[0]);
    const selectedLocation = ref(locations.value[selectedCompany.value][0]);

    // Sample employees (for UI-only)
    const employees = [
    { id: 1, name: 'John Doe' },
    { id: 2, name: 'Mary Smith' },
    { id: 3, name: 'Alice F.' },
    { id: 4, name: 'Mike L.' },
    ]

    // Signatory table
    const signatories = ref([
    { id: 1, name: 'Prepared By', order: 1, defaultEmployee: 1, assigned: [1,2] },
    { id: 2, name: 'Reviewed By', order: 2, defaultEmployee: 3, assigned: [3] },
    ])

    // Modal state
    const showModal = ref(false)
    const isEdit = ref(false)
    const modalItem = reactive({
    id: null,
    name: '',
    order: 1,
    assigned: [],
    defaultEmployee: null
    })

    const openAddModal = () => {
    isEdit.value = false
    modalItem.id = null
    modalItem.name = ''
    modalItem.order = signatories.value.length + 1
    modalItem.assigned = []
    modalItem.defaultEmployee = null
    showModal.value = true
    }

    const openEditModal = (item) => {
    isEdit.value = true
    modalItem.id = item.id
    modalItem.name = item.name
    modalItem.order = item.order
    modalItem.assigned = [...item.assigned]
    modalItem.defaultEmployee = item.defaultEmployee
    showModal.value = true
    }

    const saveSignatory = () => {
    if(isEdit.value){
        const idx = signatories.value.findIndex(s => s.id === modalItem.id)
        signatories.value[idx] = { ...modalItem }
    } else {
        modalItem.id = Date.now()
        signatories.value.push({ ...modalItem })
    }
    showModal.value = false
    }

    const newEmployeeName = ref('')

    // Add new employee function
    const addNewEmployee = () => {
    const name = newEmployeeName.value.trim()
    if(!name) return
    const newEmp = { id: Date.now(), name }
    employees.push(newEmp)               // add to master list
    modalItem.assigned.push(newEmp.id)   // assign automatically
    modalItem.defaultEmployee = newEmp.id // set as default
    newEmployeeName.value = ''           // clear input
    }

    // Remove employee function
    const removeEmployee = (id) => {
    // Remove from master list
    const idx = employees.findIndex(e => e.id === id)
    if(idx !== -1) employees.splice(idx, 1)

    // Remove from assigned list if exists
    const assignedIdx = modalItem.assigned.indexOf(id)
    if(assignedIdx !== -1) modalItem.assigned.splice(assignedIdx, 1)

    // Clear default if it was this employee
    if(modalItem.defaultEmployee === id) modalItem.defaultEmployee = null
    }
</script>

<template>
    <navigation>
        <section class="py-8 fade-up">
            <div class="max-w-6xl mx-auto bg-white rounded-2xl shadow-xl border">

                <!-- Company / Location selectors -->
                <div class="px-6 pt-6 pb-3 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                    <label class="text-xs font-medium text-gray-500">Company</label>
                    <select class="mt-1 w-full border rounded-lg px-3 py-2 text-sm"
                            v-model="selectedCompany"
                            @change="selectedLocation = locations[selectedCompany][0]">
                        <option v-for="comp in companies" :key="comp">{{ comp }}</option>
                    </select>
                    </div>
                    <div>
                    <label class="text-xs font-medium text-gray-500">Location</label>
                    <select class="mt-1 w-full border rounded-lg px-3 py-2 text-sm" v-model="selectedLocation">
                        <option v-for="loc in locations[selectedCompany]" :key="loc">{{ loc }}</option>
                    </select>
                    </div>
                </div>

                <!-- Header -->
                <div class="px-6 py-4 flex items-center justify-between border-b">
                    <div>
                    <h2 class="text-2xl font-bold text-gray-700">Signatories</h2>
                    <p class="text-sm text-gray-500">
                        Define approval labels, order, and assign employees for {{ selectedCompany }} / {{ selectedLocation }}
                    </p>
                    </div>
                    <button @click="openAddModal"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm
                            rounded-lg shadow hover:bg-blue-700 transition">
                    <PlusIcon class="w-4 h-4"/>
                    Add Signatory
                    </button>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto border-t border-b">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100 text-xs uppercase text-gray-500">
                            <tr>
                            <th class="px-6 py-3 text-left w-16">Order</th>
                            <th class="px-6 py-3 text-left">Signatory Name</th>
                            <th class="px-6 py-3 text-left">Employees Assigned</th>
                            <th class="px-6 py-3 w-16 text-center">
                                <Bars3Icon class="w-4 h-4 mx-auto"/>
                            </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="item in signatories" :key="item.id" class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-medium text-gray-700">{{ item.order }}</td>
                            <td class="px-6 py-3 font-medium text-gray-700">{{ item.name }}</td>
                            <td class="px-6 py-3">
                                <span v-for="eid in item.assigned" :key="eid"
                                    class="inline-block px-2 py-1 mr-1 text-xs rounded-full"
                                    :class="eid === item.defaultEmployee ? 'bg-yellow-200 font-semibold' : 'bg-gray-200'">
                                {{ employees.find(e => e.id === eid)?.name }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-center">
                                <button @click="openEditModal(item)"
                                class="p-2 rounded-md text-blue-600 hover:bg-blue-50">
                                <PencilSquareIcon class="w-4 h-4"/>
                                </button>
                            </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="py-4 px-4">
                    <div class="text-xs text-gray-500 text-right mb-1">*This configuration will be used as the default for new documents created under this location</div>
                    <div class="flex justify-end ">
                        <button 
                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm
                                rounded-lg shadow hover:bg-green-700 transition">
                        <Cog6ToothIcon class="w-4 h-4"/>
                        Save Configuration
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </navigation>

    <!-- Modal -->
    <modal v-model="showModal">
        <template #title>{{ isEdit ? 'Edit Signatory' : 'Add Signatory' }}</template>
        <template #close-icon>
            <XMarkIcon class="w-5 h-5 cursor-pointer" @click="showModal=false"/>
        </template>

        <div class="space-y-4">
            <!-- Signatory Name -->
            <div>
            <label class="text-sm font-medium text-gray-600">Signatory Name</label>
            <input v-model="modalItem.name" placeholder="e.g. Prepared By"
                    class="mt-1 w-full border rounded-lg px-3 py-2 text-sm"/>
            </div>

            <!-- Order -->
            <div>
            <label class="text-sm font-medium text-gray-600">Order / Default Position</label>
            <input type="number" min="1" v-model="modalItem.order"
                    class="mt-1 w-24 border rounded-lg px-3 py-2 text-sm"/>
            <p class="text-xs text-gray-400 mt-1">
                Determines default order when used in a form (1 = first, 2 = second, etc.)
            </p>
            </div>

            <!-- Employees -->
            <div>
                <label class="text-sm font-medium text-gray-600">Assign Employees</label>

                <!-- Employee List -->
                <div class="space-y-1 mt-1">
                    <div v-for="emp in employees" :key="emp.id" class="flex items-center gap-2">
                    <!-- Assign checkbox -->
                    <input type="checkbox" :value="emp.id" v-model="modalItem.assigned"/>
                    <span>{{ emp.name }}</span>

                    <!-- Default radio -->
                    <input type="radio" name="defaultEmployee"
                            :value="emp.id" v-model="modalItem.defaultEmployee"
                            :disabled="!modalItem.assigned.includes(emp.id)"
                            class="ml-auto"/>
                    <span class="text-xs text-gray-500">Default</span>

                    <!-- Remove employee button -->
                    <button v-if="!modalItem.assigned.includes(emp.id)" 
                            @click="removeEmployee(emp.id)" 
                            class="ml-2 text-red-500 hover:text-red-700 text-xs">
                        Remove
                    </button>
                    </div>
                </div>

                <!-- Add new employee inline -->
                <div class="mt-2 flex gap-2">
                    <select v-model="newEmployeeName"
                        placeholder="Add new employee"
                        class="flex-1 border rounded-lg px-3 py-2 text-sm">
                        <option value="" disabled="">Add More Employees</option>
                        <option value="">Glenn Paul Toledo</option>
                        <option value="">Beverly Esparilla</option>
                    </select>
                    <button @click="addNewEmployee"
                            class="px-3 py-2 bg-green-600 text-white rounded-lg text-sm">
                    Add
                    </button>
                </div>

                <p class="text-xs text-gray-400 mt-1">
                    Only one employee can be default for this role
                </p>
            </div>
        </div>

        <template #footer>
            <div class="flex justify-between w-full">
            <button v-if="isEdit" class="text-sm text-red-600 hover:underline">Delete</button>
            <div class="ml-auto flex gap-2">
                <button @click="showModal=false" class="px-4 py-2 bg-gray-100 rounded-lg">Cancel</button>
                <button @click="saveSignatory" class="px-4 py-2 bg-blue-600 text-white rounded-lg">{{ isEdit ? 'Update' : 'Add' }}</button>
            </div>
            </div>
        </template>
    </modal>
</template>
