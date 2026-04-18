<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import OrderCard from '@/Components/Orders/OrderCard.vue';
import Pagination from '@/Components/Shared/Pagination.vue';
import EmptyState from '@/Components/Shared/EmptyState.vue';
import AlertMessage from '@/Components/Shared/AlertMessage.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

defineProps({
    orders: Object,
});

const flash = usePage().props.flash;
</script>

<template>
    <Head title="Order History" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Order History
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <AlertMessage :message="flash?.success" type="success" class="mb-6" />

                <div v-if="orders.data.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <OrderCard
                        v-for="order in orders.data"
                        :key="order.id"
                        :order="order"
                    />
                </div>

                <EmptyState
                    v-else
                    title="No orders yet"
                    message="Your order history will appear here after you place an order."
                >
                    <Link
                        :href="route('products.index')"
                        class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                    >
                        Browse Products
                    </Link>
                </EmptyState>

                <div class="mt-8">
                    <Pagination :links="orders.links" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
