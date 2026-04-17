<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';

const props = defineProps({
    brands: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
});

const search = ref(props.filters.search || '');
const brandId = ref(props.filters.brand_id || '');
const categoryId = ref(props.filters.category_id || '');

function applyFilters() {
    router.get(route('products.index'), {
        search: search.value || undefined,
        brand_id: brandId.value || undefined,
        category_id: categoryId.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    });
}

const debouncedSearch = useDebounceFn(applyFilters, 300);

watch(search, debouncedSearch);
watch(brandId, applyFilters);
watch(categoryId, applyFilters);

function clearFilters() {
    search.value = '';
    brandId.value = '';
    categoryId.value = '';
    applyFilters();
}
</script>

<template>
    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
        <!-- Search -->
        <div class="relative flex-1 w-full sm:w-auto">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input
                v-model="search"
                type="text"
                placeholder="Search products..."
                class="w-full rounded-lg border-gray-300 pl-10 pr-4 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
            />
        </div>

        <!-- Brand filter -->
        <select
            v-model="brandId"
            class="rounded-lg border-gray-300 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
        >
            <option value="">All Brands</option>
            <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                {{ brand.name }}
            </option>
        </select>

        <!-- Category filter -->
        <select
            v-model="categoryId"
            class="rounded-lg border-gray-300 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
        >
            <option value="">All Categories</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
            </option>
        </select>

        <!-- Clear -->
        <button
            v-if="search || brandId || categoryId"
            @click="clearFilters"
            class="text-sm text-gray-500 hover:text-gray-700 whitespace-nowrap"
        >
            Clear filters
        </button>
    </div>
</template>
