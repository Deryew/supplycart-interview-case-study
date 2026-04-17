<script setup>
defineProps({
    title: { type: String, required: true },
    message: { type: String, default: '' },
});
</script>

<template>
    <div class="flex flex-col items-center justify-center py-16 text-center">
        <svg class="h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
        <h3 class="text-lg font-medium text-gray-900">{{ title }}</h3>
        <p v-if="message" class="mt-1 text-sm text-gray-500">{{ message }}</p>
        <div class="mt-4">
            <slot />
        </div>
    </div>
</template>
