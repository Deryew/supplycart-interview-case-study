<script setup>
const props = defineProps({
    modelValue: { type: Number, required: true },
    min: { type: Number, default: 1 },
    max: { type: Number, default: 99 },
});

const emit = defineEmits(['update:modelValue']);

function decrement() {
    if (props.modelValue > props.min) {
        emit('update:modelValue', props.modelValue - 1);
    }
}

function increment() {
    if (props.modelValue < props.max) {
        emit('update:modelValue', props.modelValue + 1);
    }
}
</script>

<template>
    <div class="inline-flex items-center rounded-lg border border-gray-300">
        <button
            @click="decrement"
            :disabled="modelValue <= min"
            aria-label="Decrease quantity"
            class="min-w-[2.75rem] min-h-[2.75rem] flex items-center justify-center text-gray-600 hover:bg-gray-100 disabled:opacity-30 rounded-l-lg transition-colors"
        >
            -
        </button>
        <span class="px-3 py-1 text-sm font-medium min-w-[2.5rem] text-center">
            {{ modelValue }}
        </span>
        <button
            @click="increment"
            :disabled="modelValue >= max"
            aria-label="Increase quantity"
            class="min-w-[2.75rem] min-h-[2.75rem] flex items-center justify-center text-gray-600 hover:bg-gray-100 disabled:opacity-30 rounded-r-lg transition-colors"
        >
            +
        </button>
    </div>
</template>
