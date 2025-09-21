<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="w-full rounded-xl bg-white/10 backdrop-blur-xl border border-white/20 text-white placeholder-white/60 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-200"
        style="backdrop-filter: blur(20px) saturate(150%);"
        v-model="model"
        ref="input"
    />
</template>
