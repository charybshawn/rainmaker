import { ref, computed, watch, nextTick } from 'vue'
import axios from 'axios'

export function useUniversalSearch() {
  // Search state
  const searchQuery = ref('')
  const showHeaderSearch = ref(false)
  const showSearchResults = ref(false)
  const isSearching = ref(false)
  const searchResults = ref({
    companies: [],
    blogPosts: [],
    researchItems: []
  })

  // Computed properties
  const hasSearchResults = computed(() => {
    return searchResults.value.companies.length > 0 ||
           searchResults.value.blogPosts.length > 0 ||
           searchResults.value.researchItems.length > 0
  })

  const totalSearchResults = computed(() => {
    return searchResults.value.companies.length +
           searchResults.value.blogPosts.length +
           searchResults.value.researchItems.length
  })

  // Methods
  const performSearch = async () => {
    if (!searchQuery.value.trim()) {
      clearSearch()
      return
    }

    isSearching.value = true
    showSearchResults.value = true

    try {
      const response = await axios.get('/api/search', {
        params: { q: searchQuery.value }
      })

      searchResults.value = response.data
    } catch (error) {
      console.error('Search error:', error)
      searchResults.value = {
        companies: [],
        blogPosts: [],
        researchItems: []
      }
    } finally {
      isSearching.value = false
    }
  }

  const clearSearch = () => {
    searchQuery.value = ''
    showSearchResults.value = false
    searchResults.value = {
      companies: [],
      blogPosts: [],
      researchItems: []
    }
  }

  const closeSearch = () => {
    showHeaderSearch.value = false
    clearSearch()
  }

  const toggleHeaderSearch = () => {
    showHeaderSearch.value = !showHeaderSearch.value

    if (showHeaderSearch.value) {
      nextTick(() => {
        const input = document.querySelector('#header-search-input')
        if (input) input.focus()
      })
    }
  }

  const closeHeaderSearch = () => {
    showHeaderSearch.value = false
    closeSearch()
  }

  // Watch for search query changes
  watch(searchQuery, (newQuery) => {
    if (newQuery.trim()) {
      const timeoutId = setTimeout(() => {
        performSearch()
      }, 300) // Debounce search

      return () => clearTimeout(timeoutId)
    } else {
      clearSearch()
    }
  })

  return {
    // State
    searchQuery,
    showHeaderSearch,
    showSearchResults,
    isSearching,
    searchResults,

    // Computed
    hasSearchResults,
    totalSearchResults,

    // Methods
    performSearch,
    clearSearch,
    closeSearch,
    toggleHeaderSearch,
    closeHeaderSearch
  }
}