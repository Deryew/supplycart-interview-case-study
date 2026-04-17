<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ProductGrid from '@/Components/Products/ProductGrid.vue';
import ProductFilter from '@/Components/Products/ProductFilter.vue';
import Pagination from '@/Components/Shared/Pagination.vue';
import AlertMessage from '@/Components/Shared/AlertMessage.vue';
import EmptyState from '@/Components/Shared/EmptyState.vue';
import { Head, usePage } from '@inertiajs/vue3';

defineProps({
    products: Object,
    brands: Array,
    categories: Array,
    filters: Object,
});

const flash = usePage().props.flash;
</script>

<template>
    <Head title="Products" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Products
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <AlertMessage :message="flash?.success" type="success" class="mb-6" />

                <div class="mb-6">
                    <ProductFilter
                        :brands="brands"
                        :categories="categories"
                        :filters="filters"
                    />
                </div>

                <ProductGrid
                    v-if="products.data.length"
                    :products="products.data"
                />

                <EmptyState
                    v-else
                    title="No products found"
                    message="Try adjusting your filters or search terms."
                />

                <div class="mt-8">
                    <Pagination :links="products.meta.links" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
