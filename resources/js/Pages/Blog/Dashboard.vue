<template>
    <Head title="My Blog" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    My Blog Posts
                </h2>
                <Link
                    :href="route('dashboard')"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    Create on Dashboard
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Success Message -->
                        <div
                            v-if="$page.props.flash.success"
                            class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded"
                        >
                            {{ $page.props.flash.success }}
                        </div>

                        <!-- Posts List -->
                        <div v-if="posts.length > 0" class="space-y-4">
                            <div
                                v-for="post in posts"
                                :key="post.id"
                                class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow"
                            >
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                            {{ post.title }}
                                        </h3>
                                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                                            <span
                                                class="px-2 py-1 rounded-full text-xs font-medium"
                                                :class="post.status === 'published'
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-yellow-100 text-yellow-800'"
                                            >
                                                {{ post.status === 'published' ? 'Published' : 'Draft' }}
                                            </span>
                                            <span>
                                                {{ post.status === 'published' ? 'Published' : 'Created' }}
                                                {{ formatDate(post.status === 'published' ? post.published_at : post.created_at) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2 ml-4">
                                        <!-- View Post (if published) -->
                                        <Link
                                            v-if="post.status === 'published'"
                                            :href="route('blog.show', post.slug)"
                                            class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
                                        >
                                            View
                                        </Link>

                                        <!-- Edit functionality removed to maintain SPA experience -->

                                        <!-- Delete Post -->
                                        <button
                                            @click="deletePost(post)"
                                            class="text-red-600 hover:text-red-900 text-sm font-medium"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="text-center py-12">
                            <div class="text-gray-500 mb-4">
                                <svg class="mx-auto h-12 w-12 text-gray-300" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M34 40h10v-4a6 6 0 00-10.712-3.714M34 40H14m20 0v-4a9.971 9.971 0 00-.712-3.714M14 40H4v-4a6 6 0 0110.712-3.714M14 40v-4a9.971 9.971 0 01.712-3.714M28 24a4 4 0 11-8 0 4 4 0 018 0zm-4-8a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No blog posts yet</h3>
                            <p class="text-gray-500 mb-4">Get started by creating your first blog post.</p>
                            <Link
                                :href="route('dashboard')"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                Create on Main Dashboard
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    posts: {
        type: Array,
        required: true
    }
})

const formatDate = (dateString) => {
    if (!dateString) return ''
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const deletePost = (post) => {
    if (confirm(`Are you sure you want to delete "${post.title}"?`)) {
        router.delete(route('blog.destroy', post.id))
    }
}
</script>