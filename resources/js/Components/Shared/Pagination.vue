<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: { type: Array, required: true },
});
</script>

<template>
    <nav v-if="links.length > 3" class="flex items-center justify-center gap-1">
        <template v-for="link in links" :key="link.label">
            <Link
                v-if="link.url"
                :href="link.url"
                v-html="link.label"
                :class="[
                    'px-3 py-2 text-sm rounded-md',
                    link.active
                        ? 'bg-indigo-600 text-white'
                        : 'text-gray-700 hover:bg-gray-100',
                ]"
                preserve-scroll
            />
            <span
                v-else
                v-html="link.label"
                class="px-3 py-2 text-sm text-gray-400"
            />
        </template>
    </nav>
</template>
