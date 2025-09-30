<template>
  <DashboardLayout active-tab="analytics">
    <template #title>
      <h2 class="text-3xl lg:text-4xl font-bold text-white flex items-center drop-shadow-lg">
        <svg class="w-10 h-10 mr-4 text-purple-400 drop-shadow-[0_0_8px_rgba(168,85,247,0.5)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
        </svg>
        Analytics
      </h2>
    </template>

    <!-- Analytics Content -->
    <div class="bg-white/5 rounded-2xl p-6 border border-white/10">
      <p class="text-gray-300">Analytics and charts will go here</p>
    </div>
  </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

defineProps({
  auth: Object
})
</script>