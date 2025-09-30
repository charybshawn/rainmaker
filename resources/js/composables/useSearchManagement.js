import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

export function useSearchManagement() {
  // Search state
  const searchState = ref({
    showResults: false,
    isSearching: false,
    query: '',
    hasPerformedSearch: false
  })

  // Search results
  const searchResults = ref({
    companies: [],
    blogPosts: [],
    researchItems: [],
    documents: []
  })

  // UI state
  const searchResultsViewMode = ref('grid') // 'grid', 'list', 'tree'
  const searchQuery = ref('')
  const isSearching = ref(false)
  const showSearchResults = ref(false)

  // Pagination state
  const pagination = ref({
    companies: { currentPage: 1, perPage: 10 },
    blogPosts: { currentPage: 1, perPage: 8 },
    researchItems: { currentPage: 1, perPage: 12 },
    documents: { currentPage: 1, perPage: 15 }
  })

  // Search timeout for debouncing
  let searchTimeout = null

  // Computed properties
  const paginatedCompanies = computed(() => {
    if (!searchState.value.showResults) return []

    const start = (pagination.value.companies.currentPage - 1) * pagination.value.companies.perPage
    const end = start + pagination.value.companies.perPage
    return searchResults.value.companies.slice(start, end)
  })

  const paginatedBlogPosts = computed(() => {
    const start = (pagination.value.blogPosts.currentPage - 1) * pagination.value.blogPosts.perPage
    const end = start + pagination.value.blogPosts.perPage
    return searchResults.value.blogPosts.slice(start, end)
  })

  const paginatedResearchItems = computed(() => {
    const start = (pagination.value.researchItems.currentPage - 1) * pagination.value.researchItems.perPage
    const end = start + pagination.value.researchItems.perPage
    return searchResults.value.researchItems.slice(start, end)
  })

  const paginatedDocuments = computed(() => {
    const start = (pagination.value.documents.currentPage - 1) * pagination.value.documents.perPage
    const end = start + pagination.value.documents.perPage
    return searchResults.value.documents.slice(start, end)
  })

  const totalPages = computed(() => ({
    companies: Math.ceil(searchResults.value.companies.length / pagination.value.companies.perPage),
    blogPosts: Math.ceil(searchResults.value.blogPosts.length / pagination.value.blogPosts.perPage),
    researchItems: Math.ceil(searchResults.value.researchItems.length / pagination.value.researchItems.perPage),
    documents: Math.ceil(searchResults.value.documents.length / pagination.value.documents.perPage)
  }))

  const resultsForTreeView = computed(() => {
    if (!searchQuery.value.trim()) {
      return []
    }

    const query = searchQuery.value.toLowerCase().trim()
    const results = []

    // Companies section
    searchResults.value.companies.forEach(company => {
      const companyNode = {
        id: `company-${company.id}`,
        label: company.name,
        data: company,
        type: 'company',
        expanded: false,
        children: []
      }

      // Add research items for this company
      searchResults.value.researchItems.forEach(item => {
        if (item.company_id === company.id) {
          companyNode.children.push({
            id: `research-${item.id}`,
            label: item.title,
            data: item,
            type: 'research'
          })
        }
      })

      results.push(companyNode)
    })

    // Blog posts section (if any)
    if (searchResults.value.blogPosts.length > 0) {
      const blogSection = {
        id: 'blog-posts',
        label: 'Blog Posts',
        type: 'section',
        expanded: true,
        children: searchResults.value.blogPosts.map(post => ({
          id: `blog-${post.id}`,
          label: post.title,
          data: post,
          type: 'blog'
        }))
      }
      results.push(blogSection)
    }

    return results
  })

  // Search functions
  const performUniversalSearch = async (query) => {
    if (!query.trim()) {
      clearSearch()
      return
    }

    try {
      isSearching.value = true
      searchState.value.isSearching = true
      searchState.value.showResults = true

      // Use the universal search endpoint that handles all content types
      const response = await axios.get('/api/search', {
        params: { q: query }
      })

      console.log('Universal search API response:', response.data)

      searchResults.value = {
        companies: response.data.companies || [],
        blogPosts: response.data.blogPosts || [],
        researchItems: response.data.researchItems || [],
        documents: response.data.documents || []
      }

      // Reset pagination when new search results arrive
      resetPagination()

    } catch (error) {
      console.error('Universal search error:', error)
      // Set empty results on error but maintain the structure
      searchResults.value = {
        companies: [],
        blogPosts: [],
        researchItems: [],
        documents: []
      }
    } finally {
      isSearching.value = false
      searchState.value.isSearching = false
    }
  }

  const performSearch = () => {
    if (searchQuery.value) {
      performUniversalSearch(searchQuery.value)
    }
  }

  const clearSearch = () => {
    searchQuery.value = ''
    searchState.value.showResults = false
    searchState.value.isSearching = false
    searchState.value.query = ''
    searchState.value.hasPerformedSearch = false

    // Clear search results
    searchResults.value = {
      companies: [],
      blogPosts: [],
      researchItems: [],
      documents: []
    }

    resetPagination()
  }

  // Event handlers
  const handleSearchPerformed = (data) => {
    searchState.value.showResults = true
    searchState.value.isSearching = false
    searchState.value.query = data.query
    searchState.value.hasPerformedSearch = true

    // Perform the actual search
    if (data.query) {
      performUniversalSearch(data.query)
    }
  }

  const handleSearchResultSelected = (result) => {
    console.log('Search result selected:', result)

    // Handle different result types
    switch (result.type) {
      case 'company':
        navigateToCompany(result.data)
        break
      case 'blog':
        navigateToBlogPost(result.data)
        break
      case 'research':
        openResearchItem(result.data)
        break
      case 'document':
        openDocument(result.data)
        break
      default:
        console.warn('Unknown result type:', result.type)
    }
  }

  const handleSearchCleared = () => {
    clearSearch()
  }

  const handleMobileSearch = (query) => {
    console.log('Mobile search triggered with query:', query)
    searchQuery.value = query

    if (query.trim()) {
      performUniversalSearch(query)
    } else {
      clearSearch()
    }

    // Focus the search input if available
    setTimeout(() => {
      const input = document.querySelector('#header-search-input')
      if (input) {
        input.focus()
      }
    }, 100)
  }

  // Navigation functions
  const navigateToCompany = (company) => {
    const ticker = company.ticker
    if (ticker) {
      router.visit(route('company.profile', { ticker: ticker }))
    } else {
      console.error('Company ticker not available for navigation', company)
    }
  }

  const navigateToBlogPost = (post) => {
    if (post.slug) {
      router.visit(route('blog.show', { slug: post.slug }))
    } else {
      console.error('Blog post slug not available for navigation', post)
    }
  }

  const openResearchItem = (item) => {
    // This would typically open a modal or navigate to a research item view
    console.log('Opening research item:', item)
    // Implementation depends on your research item viewing logic
  }

  const openDocument = (document) => {
    // This would typically download or open the document
    console.log('Opening document:', document)
    // Implementation depends on your document viewing logic
  }

  // Pagination functions
  const changePage = (section, page) => {
    if (page >= 1 && page <= totalPages.value[section]) {
      pagination.value[section].currentPage = page
    }
  }

  const resetPagination = () => {
    pagination.value.companies.currentPage = 1
    pagination.value.blogPosts.currentPage = 1
    pagination.value.researchItems.currentPage = 1
    pagination.value.documents.currentPage = 1
  }

  // View mode functions
  const updateViewMode = (mode) => {
    searchResultsViewMode.value = mode
  }

  // Tree view functions
  const toggleTreeNode = (nodeId) => {
    const toggleNode = (nodes) => {
      for (const node of nodes) {
        if (node.id === nodeId) {
          node.expanded = !node.expanded
          return true
        }
        if (node.children && toggleNode(node.children)) {
          return true
        }
      }
      return false
    }

    toggleNode(resultsForTreeView.value)
  }

  const handleTreeNodeClick = (node) => {
    if (node.data) {
      const result = {
        type: node.type,
        data: node.data
      }
      handleSearchResultSelected(result)
    }
  }

  // Watch for search query changes (debounced)
  watch(searchQuery, (newValue) => {
    clearTimeout(searchTimeout)

    if (newValue.length === 0) {
      clearSearch()
      return
    }

    // Debounce search for 300ms
    searchTimeout = setTimeout(() => {
      if (newValue.trim() && newValue.length >= 2) {
        performUniversalSearch(newValue)
      }
    }, 300)
  })

  return {
    // State
    searchState,
    searchResults,
    searchResultsViewMode,
    searchQuery,
    isSearching,
    showSearchResults,
    pagination,

    // Computed
    paginatedCompanies,
    paginatedBlogPosts,
    paginatedResearchItems,
    paginatedDocuments,
    totalPages,
    resultsForTreeView,

    // Methods
    performUniversalSearch,
    performSearch,
    clearSearch,
    handleSearchPerformed,
    handleSearchResultSelected,
    handleSearchCleared,
    handleMobileSearch,
    navigateToCompany,
    navigateToBlogPost,
    openResearchItem,
    openDocument,
    changePage,
    resetPagination,
    updateViewMode,
    toggleTreeNode,
    handleTreeNodeClick
  }
}