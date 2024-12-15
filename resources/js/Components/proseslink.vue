<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
});
</script>

<template>
    <Link :href="href"
        class="bg-white dark:bg-gray-800 p-4 rounded-lg flex items-center w-full text-xl cursor-pointer transition-shadow shadow-md hover:shadow-lg">
    <slot />
    </Link>
</template>
