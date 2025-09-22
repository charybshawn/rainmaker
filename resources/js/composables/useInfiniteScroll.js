import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'

/**
 * Composable for infinite scroll functionality
 * @param {Function} fetchMore - Function to fetch more items (should return Promise with {data, pagination})
 * @param {Object} options - Configuration options
 * @returns {Object} - Reactive refs and methods for infinite scrolling
 */
export function useInfiniteScroll(fetchMore, options = {}) {
  // Default options
  const {
    initialPage = 1,
    initialLimit = 10,
    threshold = 200, // Distance from bottom to trigger load
    debounceMs = 100, // Debounce scroll events
    resetTriggers = [], // Reactive values that should reset the scroll
  } = options

  // State
  const items = ref([])
  const isLoading = ref(false)
  const isInitialLoading = ref(false)
  const currentPage = ref(initialPage)
  const hasMorePages = ref(true)
  const error = ref(null)
  const totalItems = ref(0)
  const limit = ref(initialLimit)

  // Pagination metadata
  const pagination = ref({
    current_page: 1,
    total_pages: 1,
    has_more_pages: false,
    total_items: 0,
    per_page: initialLimit,
    from: null,
    to: null,
  })

  // Intersection Observer for scroll detection
  let observer = null
  let sentinelElement = null
  let debounceTimer = null

  // Create sentinel element for intersection observer
  const createSentinel = () => {
    if (sentinelElement) return sentinelElement

    sentinelElement = document.createElement('div')
    sentinelElement.style.height = '1px'
    sentinelElement.style.position = 'absolute'
    sentinelElement.style.bottom = `${threshold}px`
    sentinelElement.style.left = '0'
    sentinelElement.style.right = '0'
    sentinelElement.style.pointerEvents = 'none'
    sentinelElement.dataset.infiniteScrollSentinel = 'true'

    return sentinelElement
  }

  // Setup intersection observer
  const setupObserver = (container) => {
    if (!container || observer) return

    const sentinel = createSentinel()
    container.style.position = 'relative'
    container.appendChild(sentinel)

    observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting && hasMorePages.value && !isLoading.value) {
            clearTimeout(debounceTimer)
            debounceTimer = setTimeout(() => {
              loadMore()
            }, debounceMs)
          }
        })
      },
      {
        root: null,
        rootMargin: '0px',
        threshold: 0.1,
      }
    )

    observer.observe(sentinel)
  }

  // Cleanup observer
  const cleanupObserver = () => {
    if (observer) {
      observer.disconnect()
      observer = null
    }
    if (sentinelElement) {
      sentinelElement.remove()
      sentinelElement = null
    }
    if (debounceTimer) {
      clearTimeout(debounceTimer)
      debounceTimer = null
    }
  }

  // Load more items
  const loadMore = async () => {
    if (isLoading.value || !hasMorePages.value) return

    isLoading.value = true
    error.value = null

    try {
      const response = await fetchMore({
        page: currentPage.value,
        limit: limit.value,
      })

      if (response && response.data) {
        // Append new items to existing array
        items.value = [...items.value, ...response.data]

        // Update pagination info
        if (response.pagination) {
          pagination.value = response.pagination
          hasMorePages.value = response.pagination.has_more_pages
          totalItems.value = response.pagination.total_items
          currentPage.value = response.pagination.current_page + 1
        } else {
          // Fallback if pagination structure is different
          hasMorePages.value = response.data.length === limit.value
          currentPage.value += 1
        }
      }
    } catch (err) {
      error.value = err.message || 'Failed to load more items'
      console.error('Infinite scroll error:', err)
    } finally {
      isLoading.value = false
    }
  }

  // Initial load
  const initialize = async (container) => {
    isInitialLoading.value = true

    try {
      await loadMore()

      // Setup observer after initial load
      if (container) {
        await nextTick()
        setupObserver(container)
      }
    } finally {
      isInitialLoading.value = false
    }
  }

  // Reset infinite scroll state
  const reset = async (container) => {
    cleanupObserver()

    items.value = []
    currentPage.value = initialPage
    hasMorePages.value = true
    error.value = null
    totalItems.value = 0
    pagination.value = {
      current_page: 1,
      total_pages: 1,
      has_more_pages: false,
      total_items: 0,
      per_page: limit.value,
      from: null,
      to: null,
    }

    if (container) {
      await nextTick()
      setupObserver(container)
    }
  }

  // Force refresh - reset and reload
  const refresh = async (container) => {
    await reset(container)
    await initialize(container)
  }

  // Manual trigger for loading more (useful for "Load More" buttons)
  const triggerLoadMore = () => {
    if (!isLoading.value && hasMorePages.value) {
      loadMore()
    }
  }

  // Computed values
  const isEmpty = computed(() => items.value.length === 0 && !isInitialLoading.value)
  const canLoadMore = computed(() => hasMorePages.value && !isLoading.value)
  const showLoadingIndicator = computed(() => isLoading.value && items.value.length > 0)

  // Cleanup on unmount
  onUnmounted(() => {
    cleanupObserver()
  })

  return {
    // State
    items,
    isLoading,
    isInitialLoading,
    currentPage,
    hasMorePages,
    error,
    totalItems,
    pagination,

    // Computed
    isEmpty,
    canLoadMore,
    showLoadingIndicator,

    // Methods
    initialize,
    reset,
    refresh,
    loadMore,
    triggerLoadMore,
    setupObserver,
    cleanupObserver,
  }
}