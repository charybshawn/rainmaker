<template>
  <div class="tree-node">
    <!-- Node Header -->
    <div
      :class="[
        'flex items-center py-2 px-3 rounded-lg cursor-pointer transition-all duration-200',
        nodeTypeClasses,
        isExpanded ? 'bg-white/10' : 'hover:bg-white/5'
      ]"
      @click="toggleExpanded"
    >
      <!-- Expand/Collapse Icon -->
      <div
        v-if="hasChildren"
        :class="[
          'w-4 h-4 mr-2 transition-transform duration-200',
          isExpanded ? 'rotate-90' : ''
        ]"
      >
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
      </div>
      <div v-else class="w-4 h-4 mr-2"></div>

      <!-- Node Icon -->
      <div class="w-5 h-5 mr-3 flex-shrink-0">
        <!-- Sector Icon -->
        <svg v-if="node.type === 'sector'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
        </svg>
        <!-- Company Icon -->
        <svg v-else-if="node.type === 'company'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
        </svg>
        <!-- Research Icon -->
        <svg v-else-if="node.type === 'research'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <!-- Blog Icon -->
        <svg v-else-if="node.type === 'blog'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
        </svg>
      </div>

      <!-- Node Content -->
      <div class="flex-1 min-w-0">
        <div class="flex items-center justify-between">
          <div class="min-w-0 flex-1">
            <p :class="nodeTitleClasses">
              {{ node.name }}
              <span v-if="node.ticker_symbol" class="text-xs opacity-70 ml-1">({{ node.ticker_symbol }})</span>
            </p>
            <p v-if="node.category" class="text-xs opacity-60 mt-1">
              {{ node.category }}
            </p>
            <p v-if="node.excerpt" class="text-xs opacity-60 mt-1 line-clamp-1">
              {{ node.excerpt }}
            </p>
          </div>
          <div v-if="hasChildren" class="text-xs opacity-50 ml-2">
            {{ node.children.length }}
          </div>
        </div>
      </div>
    </div>

    <!-- Children -->
    <div
      v-if="hasChildren && isExpanded"
      class="ml-6 mt-1 border-l border-white/10"
    >
      <TreeNode
        v-for="child in node.children"
        :key="child.id"
        :node="child"
        :depth="depth + 1"
        @node-click="$emit('node-click', $event)"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  node: {
    type: Object,
    required: true
  },
  depth: {
    type: Number,
    default: 0
  }
})

const emit = defineEmits(['node-click'])

const isExpanded = ref(props.node.type === 'sector' ? true : false)

const hasChildren = computed(() => {
  return props.node.children && props.node.children.length > 0
})

const nodeTypeClasses = computed(() => {
  switch (props.node.type) {
    case 'sector':
      return 'text-blue-200 border border-blue-400/20'
    case 'company':
      return 'text-emerald-200 border border-emerald-400/20'
    case 'research':
      return 'text-purple-200 border border-purple-400/20'
    case 'blog':
      return 'text-orange-200 border border-orange-400/20'
    default:
      return 'text-gray-200 border border-gray-400/20'
  }
})

const nodeTitleClasses = computed(() => {
  const baseClasses = 'font-medium truncate'
  switch (props.node.type) {
    case 'sector':
      return `${baseClasses} text-blue-200 text-sm`
    case 'company':
      return `${baseClasses} text-emerald-200 text-sm`
    case 'research':
      return `${baseClasses} text-purple-200 text-xs`
    case 'blog':
      return `${baseClasses} text-orange-200 text-xs`
    default:
      return `${baseClasses} text-gray-200 text-xs`
  }
})

const toggleExpanded = () => {
  if (hasChildren.value) {
    isExpanded.value = !isExpanded.value
  } else if (props.node.data) {
    // Emit click event for leaf nodes with data
    emit('node-click', props.node)
  }
}
</script>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>