<template>
  <div
    :class="[
      'backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/5 rounded-2xl sm:rounded-3xl shadow-[0_5px_16px_0_rgba(31,38,135,0.2)] border border-white/10 relative',
      containerClass
    ]"
    style="backdrop-filter: blur(20px) saturate(180%);"
  >
    <div :class="['p-4 sm:p-6 lg:p-8', paddingClass]">
      <slot />
    </div>
  </div>
</template>

<script setup>
// Props
defineProps({
  containerClass: {
    type: String,
    default: ''
  },
  paddingClass: {
    type: String,
    default: ''
  }
})
</script>