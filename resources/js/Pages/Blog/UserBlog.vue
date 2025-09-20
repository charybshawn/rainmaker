<template>
    <Head :title="`${user.name}'s Blog`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ user.name }}'s Blog
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Posts List -->
                        <div v-if="posts.length > 0" class="space-y-8">
                            <article
                                v-for="post in posts"
                                :key="post.id"
                                class="border-b border-gray-200 pb-8 last:border-b-0"
                            >
                                <div class="mb-4">
                                    <h3 class="text-2xl font-bold text-gray-900 mb-2">
                                        <Link
                                            :href="route('blog.show', post.slug)"
                                            class="hover:text-indigo-600 transition-colors"
                                        >
                                            {{ post.title }}
                                        </Link>
                                    </h3>
                                    <div class="text-sm text-gray-500">
                                        Published {{ formatDate(post.published_at) }}
                                    </div>
                                </div>

                                <!-- Post Preview -->
                                <div class="prose prose-lg max-w-none mb-4">
                                    <div v-html="getExcerpt(post.content)"></div>
                                </div>

                                <Link
                                    :href="route('blog.show', post.slug)"
                                    class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium"
                                >
                                    Read more
                                    <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </Link>
                            </article>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="text-center py-12">
                            <div class="text-gray-500 mb-4">
                                <svg class="mx-auto h-12 w-12 text-gray-300" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5a2 2 0 00-2 2v8a2 2 0 002 2h14l5 5V13a2 2 0 00-2-2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No published posts yet</h3>
                            <p class="text-gray-500">{{ user.name }} hasn't published any blog posts yet.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { marked } from 'marked'

const props = defineProps({
    user: {
        type: Object,
        required: true
    },
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
        month: 'long',
        day: 'numeric'
    })
}

const getExcerpt = (content) => {
    // Convert markdown to HTML first
    const html = marked(content)

    // Remove HTML tags and get first 200 characters
    const text = html.replace(/<[^>]*>/g, '')
    const excerpt = text.length > 200 ? text.substring(0, 200) + '...' : text

    return marked(excerpt)
}
</script>

<style scoped>
.prose {
    @apply text-gray-900;
}

.prose h1,
.prose h2,
.prose h3,
.prose h4,
.prose h5,
.prose h6 {
    @apply text-gray-900 font-semibold;
}

.prose p {
    @apply mb-4 leading-relaxed;
}

.prose code {
    @apply bg-gray-100 px-1 py-0.5 rounded text-sm;
}

.prose a {
    @apply text-indigo-600 hover:text-indigo-800;
}
</style>