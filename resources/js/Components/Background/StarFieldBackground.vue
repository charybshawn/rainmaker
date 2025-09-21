<template>
  <canvas
    ref="starsCanvas"
    class="fixed inset-0 w-full h-full pointer-events-none"
    style="z-index: 1;"
  />
</template>

<script setup>
import { useStarField } from '@/composables/useStarField'

const { starsCanvas } = useStarField()
</script>