<template>
  <div class="flex items-center">
    <!-- Single Morphing Container -->
    <div
      :class="[
        'relative flex items-center bg-gradient-to-r rounded-full shadow-inner border cursor-pointer transition-all duration-700 ease-out transform-gpu',
        showHeaderSearch
          ? 'w-72 sm:w-96 h-12 from-white/8 to-white/12 border-white/10 shadow-[0_2px_8px_0_rgba(139,69,197,0.15)]'
          : 'w-10 h-10 from-white/5 to-white/5 border-white/5 hover:from-white/15 hover:to-white/20 hover:border-white/20 justify-center shadow-[0_2px_8px_0_rgba(31,38,135,0.1)] hover:shadow-[0_2px_8px_0_rgba(139,69,197,0.2)] hover:scale-105'
      ]"
      style="backdrop-filter: blur(20px) saturate(180%); transform-origin: right center;"
      @click="!showHeaderSearch ? toggleHeaderSearch() : null"
      @mouseenter="!showHeaderSearch ? toggleHeaderSearch() : null"
      title="Search"
    >
      <!-- Magnifying Glass Icon -->
      <div
        :class="[
          'flex items-center justify-center shrink-0 transition-all duration-700 ease-out transform-gpu',
          showHeaderSearch
            ? 'ml-4 w-4 h-4 text-white/70'
            : 'w-full h-full text-gray-300 hover:text-purple-200 hover:drop-shadow-[0_0_3px_rgba(139,69,197,0.4)]'
        ]"
      >
        <svg class="w-4 h-4 transition-all duration-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
      </div>

      <!-- Search Input (only visible when expanded) -->
      <input
        v-if="showHeaderSearch"
        id="header-search-input"
        v-model="searchQuery"
        type="text"
        placeholder="Search companies, tickers..."
        class="flex-1 h-full ml-3 mr-4 sm:mr-6 bg-transparent border-0 text-white placeholder-white/60 focus:outline-none focus:ring-0 focus:border-transparent focus:placeholder-white/80 transition-opacity duration-300 delay-200 text-xs sm:text-sm"
        style="box-shadow: none !important;"
        ref="searchInput"
      />
    </div>
  </div>
</template>

<script setup>
import { useUniversalSearch } from '@/composables/useUniversalSearch'

// Use the search composable
const {
  searchQuery,
  showHeaderSearch,
  showSearchResults,
  isSearching,
  searchResults,
  hasSearchResults,
  totalSearchResults,
  performSearch,
  clearSearch,
  closeSearch,
  toggleHeaderSearch,
  closeHeaderSearch
} = useUniversalSearch()

// Expose search state and methods to parent
defineExpose({
  searchQuery,
  showSearchResults,
  isSearching,
  searchResults,
  hasSearchResults,
  totalSearchResults,
  closeHeaderSearch
})
</script>