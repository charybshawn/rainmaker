<template>
  <nav class="mb-6 sm:mb-8 lg:mb-12">
    <div class="flex items-center gap-3 sm:gap-6 lg:gap-12 relative pb-1 overflow-x-auto">
      <Link
        :href="route('dashboard.overview')"
        :class="[
          'inline-block pb-3 sm:pb-4 lg:pb-6 px-1 sm:px-2 text-sm sm:text-base font-semibold tracking-wide transition-all duration-200 whitespace-nowrap',
          activeTab === 'overview'
            ? 'text-white border-b-2 sm:border-b-4 border-blue-400'
            : 'text-gray-400 hover:text-gray-200'
        ]"
      >
        Overview
      </Link>

      <Link
        :href="route('dashboard.companies')"
        :class="[
          'inline-block pb-3 sm:pb-4 lg:pb-6 px-1 sm:px-2 text-sm sm:text-base font-semibold tracking-wide transition-all duration-200 whitespace-nowrap',
          activeTab === 'companies'
            ? 'text-white border-b-2 sm:border-b-4 border-blue-400'
            : 'text-gray-400 hover:text-gray-200'
        ]"
      >
        Companies
      </Link>

      <Link
        :href="route('dashboard.research')"
        :class="[
          'inline-block pb-3 sm:pb-4 lg:pb-6 px-1 sm:px-2 text-sm sm:text-base font-semibold tracking-wide transition-all duration-200 whitespace-nowrap',
          activeTab === 'research'
            ? 'text-white border-b-4 border-green-400'
            : 'text-gray-400 hover:text-gray-200'
        ]"
      >
        Research
      </Link>

      <Link
        :href="route('dashboard.analytics')"
        :class="[
          'inline-block pb-3 sm:pb-4 lg:pb-6 px-1 sm:px-2 text-sm sm:text-base font-semibold tracking-wide transition-all duration-200 whitespace-nowrap',
          activeTab === 'analytics'
            ? 'text-white border-b-2 sm:border-b-4 border-purple-400'
            : 'text-gray-400 hover:text-gray-200'
        ]"
      >
        Analytics
      </Link>

      <Link
        :href="route('dashboard.watchlists')"
        :class="[
          'inline-block pb-3 sm:pb-4 lg:pb-6 px-1 sm:px-2 text-sm sm:text-base font-semibold tracking-wide transition-all duration-200 whitespace-nowrap',
          activeTab === 'watchlists'
            ? 'text-white border-b-2 sm:border-b-4 border-orange-400'
            : 'text-gray-400 hover:text-gray-200'
        ]"
      >
        Watchlists
      </Link>
    </div>
  </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { onMounted } from 'vue'

const props = defineProps({
  activeTab: {
    type: String,
    required: true
  }
})

onMounted(() => {
  console.log('DashboardNav activeTab:', props.activeTab)
})
</script>