<script setup>
import { useFormatPrice } from '@/Composables/useFormatPrice';

const props = defineProps({
    price: { type: Number, required: true },
    originalPrice: { type: Number, default: null },
    size: { type: String, default: 'md' },
});

const { formatPrice } = useFormatPrice();
const hasDiscount = props.originalPrice && props.originalPrice !== props.price;
</script>

<template>
    <div class="flex items-center gap-2">
        <span
            :class="[
                'font-semibold text-gray-900',
                size === 'lg' ? 'text-xl' : 'text-base',
                hasDiscount ? 'text-red-600' : '',
            ]"
        >
            {{ formatPrice(price) }}
        </span>
        <span
            v-if="hasDiscount"
            class="text-sm text-gray-400 line-through"
        >
            {{ formatPrice(originalPrice) }}
        </span>
    </div>
</template>
