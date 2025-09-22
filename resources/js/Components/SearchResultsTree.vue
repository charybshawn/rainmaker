<template>
  <div class="search-results-tree">
    <!-- Tree Header -->
    <div class="flex items-center justify-between mb-4 pb-2 border-b border-white/10">
      <h3 class="text-lg font-semibold text-white flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2v10z"></path>
        </svg>
        Tree View
      </h3>
      <div class="text-sm text-gray-300 flex items-center space-x-4">
        <span v-if="totalNodes > 0">{{ totalNodes }} items</span>
        <button
          @click="toggleExpandAll"
          class="px-2 py-1 rounded bg-white/10 hover:bg-white/20 transition-all text-xs"
        >
          {{ allExpanded ? 'Collapse All' : 'Expand All' }}
        </button>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="!searchResults || searchResults.length === 0" class="text-center py-8">
      <svg class="w-16 h-16 mx-auto text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414a1 1 0 00-.707-.293H6"></path>
      </svg>
      <p class="text-gray-400">No search results to display in tree view</p>
      <p class="text-sm text-gray-500 mt-2">Try searching for companies, research items, or blog posts</p>
    </div>

    <!-- Tree Structure -->
    <div v-else class="space-y-2">
      <TreeNode
        v-for="node in searchResults"
        :key="node.id"
        :node="node"
        :depth="0"
        @node-click="handleNodeClick"
        :ref="el => treeNodeRefs[node.id] = el"
      />
    </div>

    <!-- Statistics -->
    <div v-if="searchResults && searchResults.length > 0" class="mt-6 pt-4 border-t border-white/10">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
        <div class="p-3 rounded-lg bg-blue-500/10 border border-blue-400/20">
          <div class="text-xl font-bold text-blue-200">{{ statistics.sectors }}</div>
          <div class="text-xs text-blue-300/70">Sectors</div>
        </div>
        <div class="p-3 rounded-lg bg-emerald-500/10 border border-emerald-400/20">
          <div class="text-xl font-bold text-emerald-200">{{ statistics.companies }}</div>
          <div class="text-xs text-emerald-300/70">Companies</div>
        </div>
        <div class="p-3 rounded-lg bg-purple-500/10 border border-purple-400/20">
          <div class="text-xl font-bold text-purple-200">{{ statistics.researchItems }}</div>
          <div class="text-xs text-purple-300/70">Research</div>
        </div>
        <div class="p-3 rounded-lg bg-orange-500/10 border border-orange-400/20">
          <div class="text-xl font-bold text-orange-200">{{ statistics.blogPosts }}</div>
          <div class="text-xs text-orange-300/70">Blog Posts</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue'
import TreeNode from './TreeNode.vue'

const props = defineProps({
  searchResults: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['node-click'])

const treeNodeRefs = ref({})
const allExpanded = ref(false)

const totalNodes = computed(() => {
  let count = 0
  const countNodes = (nodes) => {
    nodes.forEach(node => {
      count++
      if (node.children && node.children.length > 0) {
        countNodes(node.children)
      }
    })
  }
  countNodes(props.searchResults || [])
  return count
})

const statistics = computed(() => {
  const stats = {
    sectors: 0,
    companies: 0,
    researchItems: 0,
    blogPosts: 0
  }

  const countByType = (nodes) => {
    nodes.forEach(node => {
      switch (node.type) {
        case 'sector':
          stats.sectors++
          break
        case 'company':
          stats.companies++
          break
        case 'research':
          stats.researchItems++
          break
        case 'blog':
          stats.blogPosts++
          break
      }

      if (node.children && node.children.length > 0) {
        countByType(node.children)
      }
    })
  }

  countByType(props.searchResults || [])
  return stats
})

const handleNodeClick = (node) => {
  // Forward the node click event to the parent component
  emit('node-click', node)
}

const toggleExpandAll = async () => {
  allExpanded.value = !allExpanded.value

  // This would be a more complex implementation that would need to communicate
  // with all TreeNode components to expand/collapse them
  // For now, this is a placeholder that could be enhanced later

  await nextTick()
  console.log(allExpanded.value ? 'Expanding all nodes' : 'Collapsing all nodes')
}
</script>

<style scoped>
/* Add any specific styles for the tree container if needed */
</style>