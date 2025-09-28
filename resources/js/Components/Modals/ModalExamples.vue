<template>
  <div class="p-8 space-y-4">
    <h1 class="text-2xl font-bold text-white mb-8">Modal Template Examples</h1>

    <!-- Action Modal Examples -->
    <div class="space-y-4">
      <h2 class="text-lg font-semibold text-white/90">Action Modals</h2>

      <div class="flex flex-wrap gap-4">
        <button
          @click="showDeleteModal = true"
          class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors"
        >
          Delete Confirmation
        </button>

        <button
          @click="showUpdateModal = true"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
        >
          Update Confirmation
        </button>

        <button
          @click="showCustomModal = true"
          class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors"
        >
          Custom Action Modal
        </button>
      </div>
    </div>

    <!-- Info Modal Examples -->
    <div class="space-y-4">
      <h2 class="text-lg font-semibold text-white/90">Info Modals</h2>

      <div class="flex flex-wrap gap-4">
        <button
          @click="showContentModal = true"
          class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors"
        >
          Content Modal (66% Width)
        </button>

        <button
          @click="showLargeModal = true"
          class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors"
        >
          Large Modal (90% Width)
        </button>

        <button
          @click="showFormModal = true"
          class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-colors"
        >
          Form Modal
        </button>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <BaseActionModal
      :show="showDeleteModal"
      @close="showDeleteModal = false"
      @confirm="handleDelete"
      title="Delete Item"
      message="Are you sure you want to delete this item? This action cannot be undone."
      danger-mode
      confirm-text="Delete"
    >
      <template #icon>
        <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
      </template>
    </BaseActionModal>

    <!-- Update Confirmation Modal -->
    <BaseActionModal
      :show="showUpdateModal"
      @close="showUpdateModal = false"
      @confirm="handleUpdate"
      title="Update Item"
      message="Are you sure you want to update this item with the new changes?"
      confirm-text="Update"
    >
      <template #icon>
        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
      </template>
    </BaseActionModal>

    <!-- Custom Action Modal -->
    <BaseActionModal
      :show="showCustomModal"
      @close="showCustomModal = false"
    >
      <template #icon>
        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
      </template>

      <template #title>
        Custom Action
      </template>

      <template #default>
        This is a custom action modal with different content and actions.
      </template>

      <template #actions>
        <button
          @click="showCustomModal = false"
          class="px-4 py-2 text-sm font-medium text-white/80 bg-white/10 hover:bg-white/20 border border-white/20 rounded-lg transition-all duration-200"
        >
          Maybe Later
        </button>
        <button
          @click="handleCustomAction"
          class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 rounded-lg transition-all duration-200 shadow-lg"
        >
          Proceed
        </button>
      </template>
    </BaseActionModal>

    <!-- Content Info Modal -->
    <BaseInfoModal
      :show="showContentModal"
      @close="showContentModal = false"
      title="Sample Content Modal"
      subtitle="This demonstrates the BaseInfoModal template"
    >
      <div class="space-y-6">
        <div>
          <h3 class="text-lg font-semibold text-white mb-3">About This Modal</h3>
          <p class="text-white/80 leading-relaxed">
            This is an example of the BaseInfoModal template. On desktop screens, it takes up 66% of the screen width and provides a clean, organized layout for displaying content.
          </p>
        </div>

        <div>
          <h3 class="text-lg font-semibold text-white mb-3">Features</h3>
          <ul class="space-y-2 text-white/80">
            <li class="flex items-start">
              <span class="inline-block w-2 h-2 bg-blue-400 rounded-full mt-2 mr-3"></span>
              Responsive design - full screen on mobile, 66% width on desktop
            </li>
            <li class="flex items-start">
              <span class="inline-block w-2 h-2 bg-blue-400 rounded-full mt-2 mr-3"></span>
              Scrollable content area for long content
            </li>
            <li class="flex items-start">
              <span class="inline-block w-2 h-2 bg-blue-400 rounded-full mt-2 mr-3"></span>
              Flexible header with title, subtitle, and actions
            </li>
            <li class="flex items-start">
              <span class="inline-block w-2 h-2 bg-blue-400 rounded-full mt-2 mr-3"></span>
              Optional footer for additional actions
            </li>
          </ul>
        </div>

        <div>
          <h3 class="text-lg font-semibold text-white mb-3">Long Content Example</h3>
          <div class="space-y-4 text-white/80">
            <p>This section demonstrates how the modal handles longer content. The content area is scrollable, maintaining the modal's structure while allowing users to access all information.</p>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.</p>
            <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
            <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
          </div>
        </div>
      </div>

      <template #footer>
        <div class="flex justify-end space-x-3">
          <button
            @click="showContentModal = false"
            class="px-4 py-2 text-sm font-medium text-white/80 bg-white/10 hover:bg-white/20 border border-white/20 rounded-lg transition-all duration-200"
          >
            Close
          </button>
          <button
            class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 rounded-lg transition-all duration-200 shadow-lg"
          >
            Save Changes
          </button>
        </div>
      </template>
    </BaseInfoModal>

    <!-- Large Info Modal (90% width) -->
    <BaseInfoModal
      :show="showLargeModal"
      @close="showLargeModal = false"
      title="Large Modal Example"
      subtitle="This modal uses 90% of the screen width"
      max-width="90%"
    >
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <h3 class="text-lg font-semibold text-white mb-3">Left Column</h3>
          <p class="text-white/80">This demonstrates how larger modals can accommodate more complex layouts with multiple columns.</p>
        </div>
        <div>
          <h3 class="text-lg font-semibold text-white mb-3">Right Column</h3>
          <p class="text-white/80">The 90% width provides more space for detailed content and complex interfaces.</p>
        </div>
      </div>
    </BaseInfoModal>

    <!-- Form Modal Example -->
    <BaseInfoModal
      :show="showFormModal"
      @close="showFormModal = false"
      title="Form Example"
      subtitle="Using BaseInfoModal for forms"
    >
      <form @submit.prevent="handleFormSubmit" class="space-y-6">
        <div>
          <label class="block text-sm font-medium text-white mb-2">Name</label>
          <input
            type="text"
            v-model="formData.name"
            class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="Enter your name"
          >
        </div>

        <div>
          <label class="block text-sm font-medium text-white mb-2">Email</label>
          <input
            type="email"
            v-model="formData.email"
            class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="Enter your email"
          >
        </div>

        <div>
          <label class="block text-sm font-medium text-white mb-2">Message</label>
          <textarea
            v-model="formData.message"
            rows="4"
            class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
            placeholder="Enter your message"
          ></textarea>
        </div>
      </form>

      <template #footer>
        <div class="flex justify-end space-x-3">
          <button
            @click="showFormModal = false"
            class="px-4 py-2 text-sm font-medium text-white/80 bg-white/10 hover:bg-white/20 border border-white/20 rounded-lg transition-all duration-200"
          >
            Cancel
          </button>
          <button
            @click="handleFormSubmit"
            class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 rounded-lg transition-all duration-200 shadow-lg"
          >
            Submit
          </button>
        </div>
      </template>
    </BaseInfoModal>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import BaseActionModal from './BaseActionModal.vue'
import BaseInfoModal from './BaseInfoModal.vue'

// Action Modal States
const showDeleteModal = ref(false)
const showUpdateModal = ref(false)
const showCustomModal = ref(false)

// Info Modal States
const showContentModal = ref(false)
const showLargeModal = ref(false)
const showFormModal = ref(false)

// Form Data
const formData = reactive({
  name: '',
  email: '',
  message: ''
})

// Action Handlers
const handleDelete = () => {
  console.log('Delete confirmed')
  showDeleteModal.value = false
}

const handleUpdate = () => {
  console.log('Update confirmed')
  showUpdateModal.value = false
}

const handleCustomAction = () => {
  console.log('Custom action confirmed')
  showCustomModal.value = false
}

const handleFormSubmit = () => {
  console.log('Form submitted:', formData)
  showFormModal.value = false
}
</script>