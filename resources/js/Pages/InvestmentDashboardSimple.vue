<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
    <Head title="Investment Research Platform" />

    <div class="container mx-auto px-4 py-8">
      <h1 class="text-4xl font-bold text-white mb-8">Investment Research Platform</h1>

      <div class="bg-white/10 backdrop-blur-md rounded-lg p-6">
        <h2 class="text-2xl font-semibold text-white mb-4">Welcome to Rainmaker</h2>
        <p class="text-gray-300">The platform is loading successfully!</p>

        <!-- Test recent blog posts -->
        <div v-if="recentBlogPosts && recentBlogPosts.length > 0" class="mt-6">
          <h3 class="text-xl font-semibold text-white mb-4">Recent Posts</h3>
          <div v-for="post in recentBlogPosts" :key="post.id" class="mb-2">
            <div class="text-blue-300">{{ post.title }}</div>
            <div class="text-gray-400 text-sm">by {{ post.user.name }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'

// Props from Laravel
defineProps({
  recentBlogPosts: {
    type: Array,
    default: () => []
  }
})
</script>