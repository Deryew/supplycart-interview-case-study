<script setup>
import { useCartStore } from '@/stores/cart';
import PriceDisplay from '@/Components/Shared/PriceDisplay.vue';
import QuantitySelector from '@/Components/Shared/QuantitySelector.vue';

const props = defineProps({
    item: { type: Object, required: true },
});

const cart = useCartStore();

function onQuantityChange(qty) {
    cart.updateQuantity(props.item.id, qty);
}
</script>

<template>
    <div class="flex items-center gap-4 py-4 border-b border-gray-100 last:border-0">
        <!-- Product image placeholder -->
        <div class="h-20 w-20 flex-shrink-0 rounded-lg bg-gray-100 flex items-center justify-center">
            <img
                v-if="item.product?.imageUrl"
                :src="item.product.imageUrl"
                :alt="item.product?.name"
                class="h-full w-full object-cover rounded-lg"
            />
            <svg v-else class="h-8 w-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
        </div>

        <!-- Details -->
        <div class="flex-1 min-w-0">
            <h3 class="text-sm font-medium text-gray-900 truncate">{{ item.product?.name }}</h3>
            <div class="mt-1 text-xs text-gray-500">
                <span v-if="item.product?.brand">{{ item.product.brand.name }}</span>
            </div>
            <PriceDisplay
                :price="item.product?.effectivePrice ?? item.product?.price"
                class="mt-1"
            />
        </div>

        <!-- Quantity -->
        <QuantitySelector
            :model-value="item.quantity"
            :max="item.product?.stock ?? 99"
            @update:model-value="onQuantityChange"
        />

        <!-- Line total -->
        <div class="text-right min-w-[5rem]">
            <PriceDisplay :price="(item.product?.effectivePrice ?? item.product?.price) * item.quantity" />
        </div>

        <!-- Remove -->
        <button
            @click="cart.removeItem(item.id)"
            class="text-gray-400 hover:text-red-500 transition-colors"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</template>
