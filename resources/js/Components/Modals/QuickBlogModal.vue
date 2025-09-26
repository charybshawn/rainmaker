<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="$emit('close')"></div>

      <!-- This element is to trick the browser into centering the modal contents. -->
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
              <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                {{ editingPost ? 'Edit Investment Insight' : 'Share Investment Insight' }}
              </h3>

              <form @submit.prevent="submitPost" class="space-y-4">
                <!-- Title -->
                <div>
                  <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                  <input
                    v-model="form.title"
                    type="text"
                    id="title"
                    placeholder="What's your investment insight?"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    :class="{ 'border-red-500': errors.title }"
                  />
                  <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title }}</p>
                </div>

                <!-- Content -->
                <div>
                  <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                  <textarea
                    v-model="form.content"
                    id="content"
                    rows="6"
                    placeholder="Share your analysis, market thoughts, or investment strategy..."
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    :class="{ 'border-red-500': errors.content }"
                  ></textarea>
                  <p v-if="errors.content" class="mt-1 text-sm text-red-600">{{ errors.content }}</p>
                  <p class="mt-1 text-xs text-gray-500">Supports basic markdown formatting</p>
                </div>

                <!-- Category -->
                <div>
                  <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                  <select
                    v-model="form.category"
                    id="category"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    <option value="">Select category</option>
                    <option value="analysis">Market Analysis</option>
                    <option value="strategy">Investment Strategy</option>
                    <option value="insights">Company Insights</option>
                    <option value="quotes">Investment Quotes</option>
                    <option value="news">Market News</option>
                  </select>
                </div>

                <!-- Author Name (for quotes) -->
                <div v-if="form.category === 'quotes'">
                  <label for="author_name" class="block text-sm font-medium text-gray-700">Quote Author</label>
                  <input
                    v-model="form.author_name"
                    type="text"
                    id="author_name"
                    placeholder="e.g., Warren Buffett, Benjamin Graham"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  />
                  <p class="mt-1 text-xs text-gray-500">Required for quotes category</p>
                </div>

                <!-- Quick Company Tags -->
                <div v-if="preselectedCompany">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Related Company</label>
                  <div class="flex items-center">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                      </svg>
                      {{ preselectedCompany.ticker }} - {{ preselectedCompany.name }}
                    </span>
                  </div>
                </div>

                <!-- Status -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Visibility</label>
                  <div class="flex space-x-4">
                    <label class="inline-flex items-center">
                      <input v-model="form.status" type="radio" value="draft" class="form-radio text-indigo-600" />
                      <span class="ml-2 text-sm text-gray-700">Save as Draft</span>
                    </label>
                    <label class="inline-flex items-center">
                      <input v-model="form.status" type="radio" value="published" class="form-radio text-indigo-600" />
                      <span class="ml-2 text-sm text-gray-700">Publish Now</span>
                    </label>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            @click="submitPost"
            type="button"
            :disabled="creating || !form.title || !form.content || (form.category === 'quotes' && !form.author_name)"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:bg-gray-400 disabled:cursor-not-allowed"
          >
            {{ creating ? (editingPost ? 'Updating...' : 'Posting...') : (form.status === 'published' ? (editingPost ? 'Update Insight' : 'Publish Insight') : 'Save Draft') }}
          </button>
          <button
            @click="$emit('close')"
            type="button"
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
          >
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import axios from 'axios'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  preselectedCompany: {
    type: Object,
    default: null
  },
  editingPost: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'posted'])

const form = ref({
  title: '',
  content: '',
  category: '',
  author_name: '',
  status: 'published',
  company_ids: []
})

const creating = ref(false)
const errors = ref({})

// Watch for preselected company
watch(() => props.preselectedCompany, (company) => {
  if (company) {
    form.value.company_ids = [company.id]
  }
}, { immediate: true })

// Watch for editing post
watch(() => props.editingPost, (post) => {
  if (post) {
    form.value = {
      title: post.title || '',
      content: post.content || '',
      category: post.category || '',
      author_name: post.author_name || '',
      status: post.status || 'published',
      company_ids: []
    }
  }
}, { immediate: true })

// Reset form when modal closes
watch(() => props.show, (isShown) => {
  if (!isShown) {
    form.value = {
      title: '',
      content: '',
      category: '',
      author_name: '',
      status: 'published',
      company_ids: props.preselectedCompany ? [props.preselectedCompany.id] : []
    }
    errors.value = {}
  }
})

const submitPost = async () => {
  if (creating.value || !form.value.title || !form.value.content || (form.value.category === 'quotes' && !form.value.author_name)) return

  creating.value = true
  errors.value = {}

  try {
    let response
    if (props.editingPost) {
      // Update existing post
      response = await axios.put(`/my-blog/${props.editingPost.id}`, form.value)
    } else {
      // Create new post
      response = await axios.post('/my-blog', form.value)
    }

    emit('posted', response.data)
    emit('close')

    // Show success message
    window.dispatchEvent(new CustomEvent('blog-posted', {
      detail: {
        message: props.editingPost
          ? 'Investment insight updated successfully!'
          : (form.value.status === 'published' ? 'Investment insight published!' : 'Draft saved successfully!'),
        type: 'success'
      }
    }))
  } catch (error) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      window.dispatchEvent(new CustomEvent('blog-posted', {
        detail: {
          message: props.editingPost ? 'Failed to update post. Please try again.' : 'Failed to save post. Please try again.',
          type: 'error'
        }
      }))
    }
  } finally {
    creating.value = false
  }
}
</script>