<template>
  <div class="relative">
    <!-- Overflow Button -->
    <button
      @click="toggleMenu"
      :class="[
        'group relative flex items-center space-x-1 sm:space-x-2 lg:space-x-3 px-3 sm:px-4 lg:px-6 py-2 sm:py-3 rounded-full font-medium transition-all duration-500 transform-gpu text-xs sm:text-sm',
        'border-0 shadow-none backdrop-blur-none text-gray-300 hover:text-white hover:scale-105 hover:shadow-[0_0_6px_rgba(255,255,255,0.1)]'
      ]"
      style="backdrop-filter: blur(0px);"
    >
      <div class="relative z-10 flex items-center space-x-1 sm:space-x-2 lg:space-x-3">
        <div class="p-2 rounded-full transition-all duration-500 bg-white/5 group-hover:bg-white/10">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
          </svg>
        </div>
        <span class="font-semibold tracking-wide">More</span>
      </div>
    </button>

    <!-- Dropdown Menu -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="transform scale-95 opacity-0"
      enter-to-class="transform scale-100 opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="transform scale-100 opacity-100"
      leave-to-class="transform scale-95 opacity-0"
    >
      <div
        v-if="showMenu"
        class="absolute top-full mt-2 right-0 w-48 backdrop-blur-3xl bg-black/20 rounded-2xl shadow-[0_20px_25px_-5px_rgba(0,0,0,0.4)] border border-white/20 py-2 z-50"
        style="backdrop-filter: blur(20px) saturate(180%); background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));"
        @click.stop
      >
        <button
          v-for="tab in hiddenTabs"
          :key="tab.id"
          @click="selectTab(tab.id)"
          :class="[
            'w-full flex items-center space-x-3 px-4 py-3 text-left text-sm font-medium transition-all duration-200',
            activeTab === tab.id
              ? 'text-blue-200 bg-blue-500/20'
              : 'text-gray-300 hover:text-white hover:bg-white/10'
          ]"
        >
          <div :class="[
            'p-1.5 rounded-full transition-all duration-200',
            activeTab === tab.id
              ? 'bg-blue-500/30'
              : 'bg-white/5 group-hover:bg-white/10'
          ]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path v-if="tab.id === 'overview'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              <path v-else-if="tab.id === 'companies'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              <path v-else-if="tab.id === 'research'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              <path v-else-if="tab.id === 'insights'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
          </div>
          <span>{{ tab.label }}</span>
        </button>
      </div>
    </Transition>

    <!-- Click outside to close -->
    <div
      v-if="showMenu"
      class="fixed inset-0 z-40"
      @click="closeMenu"
    ></div>
  </div>
</template>

<script setup>
import { ref, defineProps, defineEmits, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  hiddenTabs: {
    type: Array,
    required: true
  },
  activeTab: {
    type: String,
    required: true
  }
})

const emit = defineEmits(['tab-selected'])

const showMenu = ref(false)

const toggleMenu = () => {
  showMenu.value = !showMenu.value
}

const closeMenu = () => {
  showMenu.value = false
}

const selectTab = (tabId) => {
  emit('tab-selected', tabId)
  closeMenu()
}

// Close menu on escape key
const handleEscape = (event) => {
  if (event.key === 'Escape') {
    closeMenu()
  }
}

onMounted(() => {
  document.addEventListener('keydown', handleEscape)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscape)
})
</script>