<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    message: { type: String, default: '' },
    type: { type: String, default: 'success' },
});

const visible = ref(!!props.message);

watch(() => props.message, (val) => {
    visible.value = !!val;
    if (val) {
        setTimeout(() => { visible.value = false; }, 4000);
    }
}, { immediate: true });
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 translate-y-1"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-1"
    >
        <div
            v-if="visible && message"
            :class="[
                'rounded-lg px-4 py-3 text-sm font-medium',
                type === 'success' ? 'bg-green-50 text-green-800 border border-green-200' : '',
                type === 'error' ? 'bg-red-50 text-red-800 border border-red-200' : '',
            ]"
        >
            {{ message }}
        </div>
    </Transition>
</template>
