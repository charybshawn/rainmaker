<template>
  <div class="flex items-center space-x-2 sm:space-x-4 lg:space-x-8 relative">
    <button
      v-for="tab in tabs"
      :key="tab.id"
      @click="$emit('tab-change', tab.id)"
      :class="[
        'group relative flex items-center space-x-1 sm:space-x-2 lg:space-x-3 px-3 sm:px-4 lg:px-6 py-2 sm:py-3 rounded-full font-medium transition-all duration-500 transform-gpu text-xs sm:text-sm',
        'border-0 shadow-none backdrop-blur-none',
        activeTab === tab.id
          ? `bg-gradient-to-br from-${tab.color}-500/20 via-${tab.color}-400/10 to-transparent text-${tab.color}-200 scale-105 shadow-[0_0_8px_rgba(${tab.shadowColor},0.2)]`
          : 'text-gray-300 hover:text-white hover:scale-105 hover:shadow-[0_0_6px_rgba(255,255,255,0.1)]'
      ]"
      style="backdrop-filter: blur(0px);"
    >
      <div class="relative z-10 flex items-center space-x-1 sm:space-x-2 lg:space-x-3">
        <div :class="[
          'p-2 rounded-full transition-all duration-500',
          activeTab === tab.id
            ? `bg-${tab.color}-500/30 shadow-[0_0_5px_rgba(${tab.shadowColor},0.3)]`
            : 'bg-white/5 group-hover:bg-white/10'
        ]">
          <component :is="tab.icon" class="w-4 h-4" />
        </div>
        <span class="font-semibold tracking-wide">{{ tab.label }}</span>
      </div>
      <div
        v-if="activeTab === tab.id"
        :class="`absolute inset-0 bg-gradient-to-br from-${tab.color}-500/5 via-transparent to-${tab.color}-400/5 rounded-full`"
      />
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue'

// Icons as inline SVG components
const OverviewIcon = {
  template: `
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
    </svg>
  `
}

const PortfolioIcon = {
  template: `
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
    </svg>
  `
}

const InsightsIcon = {
  template: `
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
    </svg>
  `
}

const AnalyticsIcon = {
  template: `
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
    </svg>
  `
}

const ActionsIcon = {
  template: `
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
    </svg>
  `
}

// Props
defineProps({
  activeTab: {
    type: String,
    required: true
  }
})

// Emits
defineEmits(['tab-change'])

// Tab configuration
const tabs = [
  {
    id: 'overview',
    label: 'Overview',
    color: 'blue',
    shadowColor: '59,130,246',
    icon: OverviewIcon
  },
  {
    id: 'portfolio',
    label: 'Portfolio',
    color: 'green',
    shadowColor: '34,197,94',
    icon: PortfolioIcon
  },
  {
    id: 'insights',
    label: 'Insights',
    color: 'purple',
    shadowColor: '147,51,234',
    icon: InsightsIcon
  },
  {
    id: 'analytics',
    label: 'Analytics',
    color: 'orange',
    shadowColor: '249,115,22',
    icon: AnalyticsIcon
  },
  {
    id: 'actions',
    label: 'Actions',
    color: 'yellow',
    shadowColor: '234,179,8',
    icon: ActionsIcon
  }
]
</script>