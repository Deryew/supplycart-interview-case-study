<script setup>
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useFormatPrice } from '@/Composables/useFormatPrice';

const props = defineProps({
    itemCount: { type: Number, required: true },
    subtotal: { type: Number, required: true },
});

const { formatPrice } = useFormatPrice();
const notes = ref('');
const processing = ref(false);

function placeOrder() {
    if (processing.value) return;
    processing.value = true;
    router.post(route('orders.store'), {
        notes: notes.value || null,
    }, {
        onError: () => { processing.value = false; },
    });
}
</script>

<template>
    <div class="rounded-xl border border-gray-200 bg-white p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h3>

        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Items ({{ itemCount }})</span>
                <span class="font-medium">{{ formatPrice(subtotal) }}</span>
            </div>
            <div class="border-t border-gray-200 pt-2 flex justify-between">
                <span class="font-semibold text-gray-900">Total</span>
                <span class="font-semibold text-gray-900">{{ formatPrice(subtotal) }}</span>
            </div>
        </div>

        <div class="mt-4">
            <label class="block text-sm text-gray-600 mb-1">Order notes (optional)</label>
            <textarea
                v-model="notes"
                rows="2"
                class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Any special instructions..."
            />
        </div>

        <button
            @click="placeOrder"
            :disabled="processing || itemCount === 0"
            class="mt-4 w-full rounded-lg bg-indigo-600 px-4 py-3 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50 transition-colors"
        >
            {{ processing ? 'Placing Order...' : 'Place Order' }}
        </button>
    </div>
</template>
