<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'group relative inline-flex items-center px-4 py-2 rounded-full font-medium transition-all duration-500 transform-gpu text-sm bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent text-blue-200 scale-105 shadow-[0_0_8px_rgba(59,130,246,0.2)] focus:outline-none'
        : 'group relative inline-flex items-center px-4 py-2 rounded-full font-medium transition-all duration-500 transform-gpu text-sm text-gray-300 hover:text-white hover:scale-105 hover:shadow-[0_0_6px_rgba(255,255,255,0.1)] focus:outline-none',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
