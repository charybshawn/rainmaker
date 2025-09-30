<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 relative">
    <StarsBackground />

    <div class="w-full md:px-4 lg:px-6 xl:px-8 2xl:px-12 py-3 sm:py-6 lg:py-8 relative z-10">
      <!-- Header -->
      <DashboardHeader
        :user="user"
        :git-info="gitInfo"
        @logout="handleLogout"
      />

      <!-- Main Content Container with Enhanced Glassmorphism -->
      <div class="mt-4 sm:mt-6 lg:mt-8 relative bg-gradient-to-br from-white/10 via-white/5 to-white/10 backdrop-blur-2xl rounded-none md:rounded-2xl border border-white/20 shadow-[0_8px_32px_0_rgba(31,38,135,0.37)]">
        <!-- Inner Shadow for Depth -->
        <div class="absolute inset-0 rounded-none md:rounded-2xl shadow-[inset_0_2px_22px_rgba(255,255,255,0.05)]"></div>

        <!-- Content -->
        <div class="relative p-4 sm:p-6 lg:p-8">
          <!-- Navigation -->
          <DashboardNav :active-tab="activeTab" />

          <!-- Page Title -->
          <div class="mb-4 sm:mb-6 lg:mb-8 mt-3 sm:mt-4 lg:mt-6">
            <slot name="title">
              <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white flex items-center drop-shadow-lg">
                <slot name="icon"></slot>
                <slot name="heading"></slot>
              </h2>
            </slot>
          </div>

          <!-- Main Content -->
          <slot />
        </div>
      </div>

      <!-- Footer -->
      <div class="mt-8 text-center text-xs text-gray-500 space-y-1">
        <div class="flex items-center justify-center gap-4">
          <span class="font-medium">Rainmaker</span>
          <span class="text-gray-600">•</span>
          <span>Investment Research Platform</span>
          <span v-if="gitInfo.branch" class="text-gray-600">•</span>
          <span v-if="gitInfo.branch" class="font-mono">{{ gitInfo.branch }}</span>
        </div>
        <div v-if="gitInfo.commit">
          <a
            :href="`https://github.com/yourusername/rainmaker/commit/${gitInfo.commit}`"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-1.5 hover:text-gray-400 transition-colors font-mono"
            title="View source commit"
          >
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.30.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
            </svg>
            {{ gitInfo.commit.substring(0, 7) }}
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import StarsBackground from '@/Components/StarsBackground.vue'
import DashboardHeader from '@/Components/DashboardHeader.vue'
import DashboardNav from '@/Components/DashboardNav.vue'
import axios from 'axios'

const props = defineProps({
  activeTab: {
    type: String,
    required: true
  }
})

const page = usePage()
const user = page.props.auth.user
const gitInfo = ref({ branch: '', commit: '', version: '' })

onMounted(async () => {
  try {
    const response = await axios.get('/api/git-info')
    gitInfo.value = response.data
  } catch (error) {
    console.error('Error fetching git info:', error)
  }
})

const handleLogout = () => {
  router.post('/logout')
}
</script>