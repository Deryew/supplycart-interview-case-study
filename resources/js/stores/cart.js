import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

export const useCartStore = defineStore('cart', () => {
    const items = ref([]);
    const itemCount = computed(() => items.value.reduce((sum, item) => sum + item.quantity, 0));
    const total = computed(() => items.value.reduce((sum, item) => sum + (item.product?.effectivePrice ?? item.product?.price ?? 0) * item.quantity, 0));

    function setItems(cartItems) {
        items.value = cartItems || [];
    }

    function addItem(productId, quantity = 1) {
        router.post(route('cart.addItem'), {
            product_id: productId,
            quantity,
        }, {
            preserveScroll: true,
        });
    }

    function updateQuantity(cartItemId, quantity) {
        router.patch(route('cart.updateItem', cartItemId), {
            quantity,
        }, {
            preserveScroll: true,
        });
    }

    function removeItem(cartItemId) {
        router.delete(route('cart.removeItem', cartItemId), {
            preserveScroll: true,
        });
    }

    function clearCart() {
        router.delete(route('cart.clear'));
    }

    return {
        items,
        itemCount,
        total,
        setItems,
        addItem,
        updateQuantity,
        removeItem,
        clearCart,
    };
});
