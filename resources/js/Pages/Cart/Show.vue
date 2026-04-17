<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CartItemRow from '@/Components/Cart/CartItemRow.vue';
import CartSummary from '@/Components/Cart/CartSummary.vue';
import EmptyState from '@/Components/Shared/EmptyState.vue';
import AlertMessage from '@/Components/Shared/AlertMessage.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    cart: Object,
});

const flash = usePage().props.flash;
const items = computed(() => props.cart?.items ?? []);
const isEmpty = computed(() => items.value.length === 0);
</script>

<template>
    <Head title="Cart" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Shopping Cart
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <AlertMessage :message="flash?.success" type="success" class="mb-6" />
                <AlertMessage :message="flash?.error" type="error" class="mb-6" />

                <div v-if="!isEmpty" class="flex flex-col lg:flex-row gap-8">
                    <!-- Cart items -->
                    <div class="flex-1">
                        <div class="rounded-xl border border-gray-200 bg-white p-6">
                            <CartItemRow
                                v-for="item in items"
                                :key="item.id"
                                :item="item"
                            />
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="lg:w-80">
                        <CartSummary
                            :item-count="cart.itemCount"
                            :subtotal="cart.subtotal"
                        />
                    </div>
                </div>

                <EmptyState
                    v-else
                    title="Your cart is empty"
                    message="Browse our products and add items to your cart."
                >
                    <Link
                        :href="route('products.index')"
                        class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                    >
                        Browse Products
                    </Link>
                </EmptyState>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
