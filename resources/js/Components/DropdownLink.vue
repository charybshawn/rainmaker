<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Link
        :href="href"
        class="block w-full px-4 py-3 text-start text-sm leading-5 text-white font-medium rounded-xl mx-2 my-1 transition-all duration-300 ease-in-out hover:bg-white/10 hover:scale-105 hover:shadow-[0_2px_8px_0_rgba(139,69,197,0.2)] focus:bg-white/10 focus:outline-none"
    >
        <slot />
    </Link>
</template>
