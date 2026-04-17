<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import OrderItemRow from '@/Components/Orders/OrderItemRow.vue';
import Badge from '@/Components/Shared/Badge.vue';
import AlertMessage from '@/Components/Shared/AlertMessage.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

defineProps({
    order: Object,
});

const flash = usePage().props.flash;

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
    <Head :title="`Order ${order.orderNumber}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link :href="route('orders.index')" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Order {{ order.orderNumber }}
                </h2>
                <Badge :value="order.status" :variant="statusVariant(order.status)" />
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <AlertMessage :message="flash?.success" type="success" class="mb-6" />

                <div class="rounded-xl border border-gray-200 bg-white">
                    <!-- Order info -->
                    <div class="p-6 border-b border-gray-100">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Date</span>
                                <p class="font-medium text-gray-900">{{ order.formattedDate }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Total</span>
                                <p class="font-medium text-gray-900">{{ order.formattedTotal }}</p>
                            </div>
                        </div>
                        <div v-if="order.notes" class="mt-4 text-sm">
                            <span class="text-gray-500">Notes</span>
                            <p class="text-gray-700 mt-1">{{ order.notes }}</p>
                        </div>
                    </div>

                    <!-- Items -->
                    <div class="p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Items</h3>
                        <OrderItemRow
                            v-for="item in order.items"
                            :key="item.id"
                            :item="item"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
