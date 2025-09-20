<template>
    <Head :title="post.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ post.title }}
                    </h2>
                    <div class="mt-1 text-sm text-gray-500">
                        By {{ post.user.name }} •
                        {{ formatDate(post.published_at || post.created_at) }}
                        <span
                            v-if="post.status === 'draft'"
                            class="ml-2 px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full"
                        >
                            Draft
                        </span>
                    </div>
                </div>

                <!-- Edit functionality removed to maintain SPA experience -->
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <article class="p-6 text-gray-900">
                        <!-- Related Companies -->
                        <div v-if="post.companies && post.companies.length > 0" class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Related Companies</h3>
                            <div class="flex flex-wrap gap-2">
                                <Link
                                    v-for="company in post.companies"
                                    :key="company.id"
                                    :href="`/?search=${encodeURIComponent(company.ticker_symbol)}`"
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 hover:bg-blue-200 transition-colors"
                                >
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    {{ company.ticker_symbol }} - {{ company.name }}
                                </Link>
                            </div>
                        </div>

                        <!-- Render Markdown Content -->
                        <div
                            class="prose prose-lg max-w-none"
                            v-html="renderedContent"
                        ></div>
                    </article>
                </div>

                <!-- Navigation Links -->
                <div class="mt-6 flex justify-between">
                    <Link
                        :href="route('user.blog', post.user.name)"
                        class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        ← More from {{ post.user.name }}
                    </Link>

                    <Link
                        v-if="canEdit"
                        :href="route('blog.index')"
                        class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        My Blog →
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { computed } from 'vue'
import { marked } from 'marked'

const props = defineProps({
    post: {
        type: Object,
        required: true
    }
})

const page = usePage()

const canEdit = computed(() => {
    return page.props.auth.user && page.props.auth.user.id === props.post.user_id
})

const renderedContent = computed(() => {
    return marked(props.post.content)
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

.prose h1 {
    @apply text-3xl mb-4;
}

.prose h2 {
    @apply text-2xl mb-3;
}

.prose h3 {
    @apply text-xl mb-2;
}

.prose p {
    @apply mb-4 leading-relaxed;
}

.prose ul,
.prose ol {
    @apply mb-4 pl-6;
}

.prose li {
    @apply mb-1;
}

.prose code {
    @apply bg-gray-100 px-1 py-0.5 rounded text-sm;
}

.prose pre {
    @apply bg-gray-100 p-4 rounded-lg overflow-x-auto mb-4;
}

.prose blockquote {
    @apply border-l-4 border-gray-300 pl-4 italic text-gray-600 mb-4;
}

.prose a {
    @apply text-indigo-600 hover:text-indigo-800;
}
</style>