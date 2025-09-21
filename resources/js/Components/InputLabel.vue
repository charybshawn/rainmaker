<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <label class="block text-sm font-medium text-white/90 transition-colors duration-200">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
