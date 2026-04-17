<script setup>
defineProps({
    value: { type: [String, Number], required: true },
    variant: { type: String, default: 'primary' },
});
</script>

<template>
    <span
        :class="[
            'inline-flex items-center justify-center rounded-full px-2 py-0.5 text-xs font-medium',
            variant === 'primary' ? 'bg-indigo-100 text-indigo-700' : '',
            variant === 'success' ? 'bg-green-100 text-green-700' : '',
            variant === 'warning' ? 'bg-yellow-100 text-yellow-700' : '',
            variant === 'danger' ? 'bg-red-100 text-red-700' : '',
            variant === 'gray' ? 'bg-gray-100 text-gray-700' : '',
        ]"
    >
        {{ value }}
    </span>
</template>
