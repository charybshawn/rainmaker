<template>
  <DashboardLayout active-tab="overview">
    <template #title>
      <h2 class="text-3xl lg:text-4xl font-bold text-white flex items-center drop-shadow-lg">
        <svg class="w-10 h-10 mr-4 text-blue-400 drop-shadow-[0_0_8px_rgba(59,130,246,0.5)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
        </svg>
        Investment Overview
      </h2>
    </template>

    <!-- Widgets -->
    <DashboardWidgets
      :is-authenticated="!!$page.props.auth.user"
      :user="$page.props.auth.user"
      @company-selected="handleCompanySelected"
    />
  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import DashboardWidgets from '@/Components/DashboardWidgets.vue'
import axios from 'axios'

defineProps({
  auth: Object,
  recentBlogPosts: Array
})

// State
const gitInfo = ref({ branch: '', commit: '', version: '' })
const selectedCompany = ref(null)

// Fetch git info
onMounted(async () => {
  try {
    const response = await axios.get('/api/git-info')
    gitInfo.value = response.data
  } catch (error) {
    console.error('Error fetching git info:', error)
  }
})

// Handlers
const handleLogout = () => {
  router.post('/logout')
}

const handleCompanySelected = (company) => {
  selectedCompany.value = company
  // Navigate to company profile page
  router.visit(`/companies/${company.ticker}`)
}
</script>