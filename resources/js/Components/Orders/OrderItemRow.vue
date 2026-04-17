<script setup>
defineProps({
    item: { type: Object, required: true },
});
</script>

<template>
    <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
        <div class="flex-1">
            <p class="text-sm font-medium text-gray-900">{{ item.productName }}</p>
            <p class="text-xs text-gray-500">
                {{ item.formattedUnitPrice }} x {{ item.quantity }}
            </p>
        </div>
        <span class="text-sm font-medium text-gray-900">{{ item.formattedTotalPrice }}</span>
    </div>
</template>
