<script setup>
import { ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { useCartStore } from '@/stores/cart';
import PriceDisplay from '@/Components/Shared/PriceDisplay.vue';
import Badge from '@/Components/Shared/Badge.vue';

const props = defineProps({
    product: { type: Object, required: true },
});

const page = usePage();
const cart = useCartStore();
const quantity = ref(1);
const adding = ref(false);

function addToCart() {
    if (!page.props.auth?.user) {
        router.visit(route('login'));
        return;
    }
    adding.value = true;
    cart.addItem(props.product.id, quantity.value);
    setTimeout(() => {
        adding.value = false;
        quantity.value = 1;
    }, 500);
}
</script>

<template>
    <div class="group flex flex-col rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-shadow">
        <!-- Image placeholder -->
        <div class="relative aspect-square w-full overflow-hidden rounded-t-xl bg-gray-100 flex items-center justify-center">
            <img
                v-if="product.imageUrl"
                :src="product.imageUrl"
                :alt="product.name"
                class="h-full w-full object-cover"
            />
            <svg v-else class="h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <Badge
                v-if="product.hasDiscount"
                value="Sale"
                variant="danger"
                class="absolute top-2 right-2"
            />
        </div>

        <!-- Content -->
        <div class="flex flex-1 flex-col p-4">
            <div class="mb-1 flex items-center gap-2 text-xs text-gray-500">
                <span v-if="product.brand">{{ product.brand.name }}</span>
                <span v-if="product.brand && product.category">|</span>
                <span v-if="product.category">{{ product.category.name }}</span>
            </div>

            <h3 class="text-sm font-medium text-gray-900 mb-2 line-clamp-2">
                {{ product.name }}
            </h3>

            <div class="mt-auto">
                <PriceDisplay
                    :price="product.effectivePrice"
                    :original-price="product.hasDiscount ? product.price : null"
                />

                <div class="mt-3 flex items-center gap-2">
                    <button
                        @click="addToCart"
                        :disabled="adding || product.stock === 0"
                        class="flex-1 rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50 transition-colors"
                    >
                        {{ product.stock === 0 ? 'Out of Stock' : adding ? 'Added!' : 'Add to Cart' }}
                    </button>
                </div>

                <p v-if="product.stock > 0 && product.stock <= 5" class="mt-1 text-xs text-orange-600">
                    Only {{ product.stock }} left
                </p>
            </div>
        </div>
    </div>
</template>
