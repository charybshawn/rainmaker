<template>
  <!-- Edit Company Modal -->
  <EditCompanyModal
    :show="showDetailsModal"
    :company="selectedCompany"
    :editForm="editForm"
    :editErrors="editErrors"
    :saving="editingCompany"
    :editMarketCapInput="editMarketCapInput"
    :editMarketCapValidation="editMarketCapValidation"
    :formatMarketCap="formatMarketCap"
    @close="$emit('close-details-modal')"
    @save-edit="$emit('save-company-edits', $event)"
    @update:edit-form="$emit('update:edit-form', $event)"
    @edit-market-cap-input="$emit('edit-market-cap-input', $event)"
  />

  <!-- Note Creation Modal -->
  <NoteCreationModal
    :show="showNoteModal"
    :selectedCompany="selectedCompany"
    :form="noteForm"
    :errors="noteErrors"
    :creatingNote="creatingNote"
    :categories="categories"
    @close="$emit('close-note-modal')"
    @save="$emit('create-note', $event)"
  />

  <!-- Research Asset Creation Modal -->
  <DocumentUploadModal
    :show="showUploadModal"
    :selectedCompany="selectedCompany"
    :form="uploadForm"
    :errors="uploadErrors"
    :uploading="uploading"
    :categories="categories"
    :formatFileSize="formatFileSize"
    @close="$emit('close-upload-modal')"
    @save="$emit('upload-documents', $event)"
    @file-upload="$emit('handle-document-upload', $event)"
    @remove-file="$emit('remove-upload-file', $event)"
  />

  <!-- Quick Blog Modal -->
  <QuickBlogModal
    :show="showQuickBlogModal"
    :preselected-company="quickBlogCompany"
    :editing-post="editingBlogPost"
    @close="$emit('close-quick-blog-modal')"
    @posted="$emit('handle-blog-posted', $event)"
  />

  <!-- Delete Confirmation Modal -->
  <DeleteConfirmationModal
    :show="showDeleteModal"
    :item-name="deleteItem?.title || deleteItem?.name || ''"
    :item-type="deleteItemType"
    @close="$emit('close-delete-modal')"
    @confirm="$emit('confirm-delete')"
  />

  <!-- Research Note Modal -->
  <ResearchNoteModal
    :show="showResearchNoteModal"
    :researchNote="selectedResearchNote"
    @close="$emit('close-research-note-modal')"
    @view-company="$emit('handle-view-company-from-note', $event)"
  />

  <!-- Create Company Modal -->
  <CreateCompanyModal
    :show="showCreateModal"
    :form="createForm"
    :errors="createErrors"
    :creating="creating"
    :marketCapInput="marketCapInput"
    :marketCapValidation="marketCapValidation"
    :formatMarketCap="formatMarketCap"
    @close="$emit('close-create-modal')"
    @save="$emit('create-company', $event)"
    @market-cap-input="$emit('handle-market-cap-input', $event)"
    @update:form="$emit('update:create-form', $event)"
  />

  <!-- Bulk Delete Confirmation Modal -->
  <div
    v-if="showDeleteConfirmModal"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
    @click.self="$emit('cancel-bulk-delete')"
  >
    <div class="relative w-full max-w-2xl bg-gradient-to-br from-slate-900/95 to-slate-800/95 rounded-2xl border border-white/10 backdrop-blur-xl shadow-2xl">
      <!-- Modal Header -->
      <div class="flex items-center justify-between p-6 border-b border-white/10">
        <h3 class="text-xl font-semibold text-white">
          Confirm Bulk Deletion
        </h3>
        <button
          @click="$emit('cancel-bulk-delete')"
          class="p-1 rounded-lg hover:bg-white/10 transition-colors"
        >
          <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Modal Content -->
      <div class="p-6">
        <!-- Loading State -->
        <div v-if="loadingDeletionImpact" class="flex items-center justify-center py-8">
          <div class="text-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-white mx-auto"></div>
            <p class="text-gray-400 mt-2">Analyzing deletion impact...</p>
          </div>
        </div>

        <!-- Deletion Impact Summary -->
        <div v-else-if="deletionImpact" class="space-y-6">
          <!-- Warning Message -->
          <div class="flex items-start space-x-3 p-4 bg-red-500/20 border border-red-400/30 rounded-lg">
            <svg class="w-6 h-6 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <div>
              <h4 class="text-red-300 font-medium">Deletion Impact Analysis</h4>
              <p class="text-red-200/80 text-sm mt-1">
                You are about to soft-delete {{ deletionImpact.totals.companies }} companies. This will also affect related data:
              </p>
            </div>
          </div>

          <!-- Impact Summary -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 bg-blue-500/20 border border-blue-400/30 rounded-lg">
              <div class="text-center">
                <div class="text-2xl font-bold text-blue-300">{{ deletionImpact.totals.research_items }}</div>
                <div class="text-sm text-blue-200/80">Research Items</div>
              </div>
            </div>
            <div class="p-4 bg-purple-500/20 border border-purple-400/30 rounded-lg">
              <div class="text-center">
                <div class="text-2xl font-bold text-purple-300">{{ deletionImpact.totals.documents }}</div>
                <div class="text-sm text-purple-200/80">Documents</div>
              </div>
            </div>
            <div class="p-4 bg-orange-500/20 border border-orange-400/30 rounded-lg">
              <div class="text-center">
                <div class="text-2xl font-bold text-orange-300">{{ deletionImpact.totals.blog_post_associations }}</div>
                <div class="text-sm text-orange-200/80">Blog Associations</div>
              </div>
            </div>
          </div>

          <!-- Detailed Company List -->
          <div v-if="deletionImpact.companies.length > 0" class="space-y-3">
            <h4 class="text-lg font-medium text-white border-b border-white/10 pb-2">Companies to be deleted:</h4>
            <div class="max-h-60 overflow-y-auto space-y-2">
              <div
                v-for="company in deletionImpact.companies"
                :key="company.id"
                class="flex items-center justify-between p-3 bg-white/5 rounded-lg border border-white/10"
              >
                <div>
                  <div class="font-medium text-white">{{ company.name }}</div>
                  <div class="text-sm text-gray-400">{{ company.ticker }}</div>
                </div>
                <div class="text-sm text-gray-400 space-x-4">
                  <span v-if="company.research_items > 0">{{ company.research_items }} research</span>
                  <span v-if="company.documents > 0">{{ company.documents }} docs</span>
                  <span v-if="company.blog_associations > 0">{{ company.blog_associations }} blogs</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Soft Delete Notice -->
          <div class="p-4 bg-green-500/20 border border-green-400/30 rounded-lg">
            <div class="flex items-start space-x-3">
              <svg class="w-5 h-5 text-green-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <div>
                <h4 class="text-green-300 font-medium">Soft Delete</h4>
                <p class="text-green-200/80 text-sm mt-1">
                  Data will be marked as deleted but can be recovered by administrators. Related data will remain intact.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="flex items-center justify-end space-x-3 p-6 border-t border-white/10">
        <button
          @click="$emit('cancel-bulk-delete')"
          class="px-4 py-2 text-gray-400 hover:text-white transition-colors"
        >
          Cancel
        </button>
        <button
          @click="$emit('confirm-bulk-delete')"
          :disabled="loadingDeletionImpact || bulkDeleting"
          class="px-6 py-2 bg-red-500/20 hover:bg-red-500/30 border border-red-400/30 text-red-300 hover:text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="bulkDeleting">Deleting...</span>
          <span v-else>Confirm Deletion</span>
        </button>
      </div>
    </div>
  </div>

  <!-- Toast Notifications -->
  <ToastNotification />
</template>

<script setup>
import EditCompanyModal from '@/Components/Modals/EditCompanyModal.vue'
import CreateCompanyModal from '@/Components/Modals/CreateCompanyModal.vue'
import NoteCreationModal from '@/Components/Modals/NoteCreationModal.vue'
import DocumentUploadModal from '@/Components/Modals/DocumentUploadModal.vue'
import QuickBlogModal from '@/Components/Modals/QuickBlogModal.vue'
import ResearchNoteModal from '@/Components/Modals/ResearchNoteModal.vue'
import DeleteConfirmationModal from '@/Components/Modals/DeleteConfirmationModal.vue'
import ToastNotification from '@/Components/ToastNotification.vue'

defineProps({
  // Modal visibility states
  showDetailsModal: {
    type: Boolean,
    default: false
  },
  showNoteModal: {
    type: Boolean,
    default: false
  },
  showUploadModal: {
    type: Boolean,
    default: false
  },
  showQuickBlogModal: {
    type: Boolean,
    default: false
  },
  showDeleteModal: {
    type: Boolean,
    default: false
  },
  showResearchNoteModal: {
    type: Boolean,
    default: false
  },
  showCreateModal: {
    type: Boolean,
    default: false
  },
  showDeleteConfirmModal: {
    type: Boolean,
    default: false
  },

  // Data props
  selectedCompany: {
    type: Object,
    default: null
  },
  selectedResearchNote: {
    type: Object,
    default: null
  },
  quickBlogCompany: {
    type: Object,
    default: null
  },
  deleteItem: {
    type: Object,
    default: null
  },
  deleteItemType: {
    type: String,
    default: ''
  },
  deletionImpact: {
    type: Object,
    default: null
  },

  // Form props
  editForm: {
    type: Object,
    required: true
  },
  createForm: {
    type: Object,
    required: true
  },
  noteForm: {
    type: Object,
    required: true
  },
  uploadForm: {
    type: Object,
    required: true
  },

  // Error props
  editErrors: {
    type: Object,
    default: () => ({})
  },
  createErrors: {
    type: Object,
    default: () => ({})
  },
  noteErrors: {
    type: Object,
    default: () => ({})
  },
  uploadErrors: {
    type: Object,
    default: () => ({})
  },

  // State props
  editingCompany: {
    type: Boolean,
    default: false
  },
  creating: {
    type: Boolean,
    default: false
  },
  creatingNote: {
    type: Boolean,
    default: false
  },
  uploading: {
    type: Boolean,
    default: false
  },
  editingBlogPost: {
    type: Boolean,
    default: false
  },
  loadingDeletionImpact: {
    type: Boolean,
    default: false
  },
  bulkDeleting: {
    type: Boolean,
    default: false
  },

  // Validation props
  editMarketCapInput: {
    type: String,
    default: ''
  },
  editMarketCapValidation: {
    type: Object,
    default: () => ({})
  },
  marketCapInput: {
    type: String,
    default: ''
  },
  marketCapValidation: {
    type: Object,
    default: () => ({})
  },

  // Data arrays
  categories: {
    type: Array,
    default: () => []
  },

  // Utility functions
  formatMarketCap: {
    type: Function,
    required: true
  },
  formatFileSize: {
    type: Function,
    required: true
  }
})

defineEmits([
  'close-details-modal',
  'save-company-edits',
  'update:edit-form',
  'edit-market-cap-input',
  'close-note-modal',
  'create-note',
  'close-upload-modal',
  'upload-documents',
  'handle-document-upload',
  'remove-upload-file',
  'close-quick-blog-modal',
  'handle-blog-posted',
  'close-delete-modal',
  'confirm-delete',
  'close-research-note-modal',
  'handle-view-company-from-note',
  'close-create-modal',
  'create-company',
  'handle-market-cap-input',
  'update:create-form',
  'cancel-bulk-delete',
  'confirm-bulk-delete'
])
</script>

<style scoped>
/* Component-specific styles if needed */
</style>