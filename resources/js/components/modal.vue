<template>
    <Transition name="backdrop">
        <div
        v-if="modelValue"
        class="fixed inset-0 z-50 flex items-center justify-center"
        >
        <!-- Backdrop -->
        <div
            class="absolute inset-0 bg-black/40 backdrop-blur-xs"
            @click="emitClose"
        ></div>

        <!-- Modal -->
        <Transition name="modal">
            <div
            class="relative bg-white rounded-2xl w-[600px] max-w-[90%] shadow-xl"
            >
            <!-- Header -->
            <div class="flex justify-between items-center px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-600">
                <slot name="title" />
                </h3>

                <button
                @click="emitClose"
                class="text-gray-500 hover:text-gray-700"
                >
                <slot name="close-icon">
                    ✕
                </slot>
                </button>
            </div>

            <!-- Body -->
            <div class="px-6 pt-4 pb-8">
                <slot />
            </div>

            <!-- Footer -->
            <div class="flex justify-end gap-2 px-6 py-4 border-t">
                <slot name="footer" />
            </div>
            </div>
        </Transition>
        </div>
    </Transition>
</template>

<script setup>
defineProps({
  modelValue: {
    type: Boolean,
    required: true,
  },
})

const emit = defineEmits(['update:modelValue'])

const emitClose = () => {
  emit('update:modelValue', false)
}
</script>
