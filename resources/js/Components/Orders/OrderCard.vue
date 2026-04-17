<script setup>
import { Link } from '@inertiajs/vue3';
import Badge from '@/Components/Shared/Badge.vue';

defineProps({
    order: { type: Object, required: true },
});

const statusVariant = (status) => {
    const map = {
        pending: 'warning',
        processing: 'primary',
        completed: 'success',
        cancelled: 'danger',
    };
    return map[status] || 'gray';
};
</script>

<template>
    <Link
        :href="route('orders.show', order.id)"
        class="block rounded-xl border border-gray-200 bg-white p-5 hover:shadow-md transition-shadow"
    >
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-semibold text-gray-900">{{ order.orderNumber }}</span>
            <Badge :value="order.status" :variant="statusVariant(order.status)" />
        </div>

        <div class="text-sm text-gray-500 mb-2">
            {{ order.formattedDate }}
        </div>

        <div class="flex items-center justify-between">
            <span class="text-xs text-gray-500">
                {{ order.items?.length ?? 0 }} item(s)
            </span>
            <span class="font-semibold text-gray-900">{{ order.formattedTotal }}</span>
        </div>
    </Link>
</template>
