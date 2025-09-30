# InvestmentDashboard.vue Update Instructions

## Summary
This document shows how to update InvestmentDashboard.vue to use the new modal management system consisting of:

1. `useModalManager` composable for centralized state management
2. `DashboardModals` component for all modal instances

## Required Changes

### 1. Update Imports

**Remove these modal imports:**
```javascript
// REMOVE THESE LINES:
import EditCompanyModal from '@/Components/Modals/EditCompanyModal.vue'
import CreateCompanyModal from '@/Components/Modals/CreateCompanyModal.vue'
import NoteCreationModal from '@/Components/Modals/NoteCreationModal.vue'
import DocumentUploadModal from '@/Components/Modals/DocumentUploadModal.vue'
import QuickBlogModal from '@/Components/Modals/QuickBlogModal.vue'
import ResearchNoteModal from '@/Components/Modals/ResearchNoteModal.vue'
import DeleteConfirmationModal from '@/Components/Modals/DeleteConfirmationModal.vue'
```

**Add these new imports:**
```javascript
// ADD THESE LINES:
import DashboardModals from '@/Components/DashboardModals.vue'
import { useModalManager } from '@/composables/useModalManager'
```

### 2. Replace Modal State Variables

**Remove all these refs:**
```javascript
// REMOVE ALL THESE LINES:
const showCreateModal = ref(false)
const showDetailsModal = ref(false)
const modalInitialTab = ref('overview')
const showResearchNoteModal = ref(false)
const selectedResearchNoteId = ref(null)
const selectedResearchNote = ref(null)
const debugModalVisible = ref(false)
const showNoteModal = ref(false)
const showUploadModal = ref(false)
const showQuickBlogModal = ref(false)
const quickBlogCompany = ref(null)
const editingBlogPost = ref(null)
const showDeleteConfirmModal = ref(false)
const deletionImpact = ref(null)
const loadingDeletionImpact = ref(false)
const showDeleteModal = ref(false)
const deleteItem = ref(null)
const deleteItemType = ref('')
const selectedCompany = ref(null)
```

**Add the modal manager:**
```javascript
// ADD THIS LINE:
const {
  modals,
  modalData,
  openCreateModal,
  closeCreateModal,
  openDetailsModal,
  closeDetailsModal,
  openResearchNoteModal,
  closeResearchNoteModal,
  openNoteModal,
  closeNoteModal,
  openUploadModal,
  closeUploadModal,
  openQuickBlogModal,
  closeQuickBlogModal,
  openDeleteModal,
  closeDeleteModal,
  openDeleteConfirmModal,
  closeDeleteConfirmModal,
  updateModalData,
  closeAllModals
} = useModalManager()
```

### 3. Update Modal Function Calls

**Replace all modal opening calls:**

**Company Creation:**
```javascript
// OLD:
const openCreateModal = () => {
  showDetailsModal.value = false
  selectedCompany.value = null
  // ... rest of logic
  showCreateModal.value = true
}

// NEW: (function already provided by composable)
// Just call: openCreateModal()
```

**Company Details:**
```javascript
// OLD:
const viewCompanyDetails = async (company) => {
  // ... existing logic
  selectedCompany.value = { ...response.data }
  showDetailsModal.value = true
}

// NEW:
const viewCompanyDetails = async (company) => {
  // ... existing logic
  const companyData = { ...response.data }
  openDetailsModal(companyData, 'overview')
}
```

**Research Note Modal:**
```javascript
// OLD:
const viewResearchNote = (note) => {
  selectedResearchNote.value = note
  selectedResearchNoteId.value = note.id
  showResearchNoteModal.value = true
}

// NEW:
const viewResearchNote = (note) => {
  openResearchNoteModal(note)
}
```

**Quick Blog Modal:**
```javascript
// OLD:
const editBlogPost = (post) => {
  editingBlogPost.value = post
  quickBlogCompany.value = post.company
  showQuickBlogModal.value = true
}

// NEW:
const editBlogPost = (post) => {
  openQuickBlogModal(post.company, post)
}
```

### 4. Update Modal Closing Functions

**Replace all close functions with composable functions:**
```javascript
// OLD:
const closeCreateModal = () => {
  companyForm.value = { /* reset */ }
  showCreateModal.value = false
  // ...
}

const closeDetailsModal = () => {
  showDetailsModal.value = false
  isEditingCompany.value = false
  selectedCompany.value = null
  modalInitialTab.value = 'overview'
  errors.value = {}
}

// NEW: Use the composable functions directly
// closeCreateModal() - already provided
// closeDetailsModal() - already provided
// Additional cleanup if needed:
const handleCloseDetailsModal = () => {
  closeDetailsModal()
  isEditingCompany.value = false
  errors.value = {}
}
```

### 5. Update Template References

**Replace all modal state references:**
```javascript
// OLD references:
selectedCompany.value
showDetailsModal.value
modalInitialTab.value
showCreateModal.value
// etc.

// NEW references:
modalData.selectedCompany
modals.showDetailsModal
modalData.modalInitialTab
modals.showCreateModal
// etc.
```

**Replace the entire modal section in template:**
```vue
<!-- OLD: Remove all individual modal components -->
<EditCompanyModal :show="showDetailsModal" ... />
<NoteCreationModal :show="showNoteModal" ... />
<!-- ... all other modals ... -->

<!-- NEW: Replace with single DashboardModals component -->
<DashboardModals
  :modals="modals"
  :modal-data="modalData"
  :edit-form="companyForm"
  :create-form="companyForm"
  :note-form="noteForm"
  :upload-form="uploadForm"
  :edit-errors="errors"
  :create-errors="errors"
  :note-errors="errors"
  :upload-errors="errors"
  :saving="editingCompany"
  :creating="creating"
  :creating-note="creatingNote"
  :uploading="uploading"
  :bulk-deleting="bulkDeleting"
  :edit-market-cap-input="editMarketCapInput"
  :market-cap-input="marketCapInput"
  :edit-market-cap-validation="editMarketCapValidation"
  :market-cap-validation="marketCapValidation"
  :categories="categories"
  :selected-items-count="selectedCompanies.length"
  :format-market-cap="formatMarketCap"
  :format-file-size="formatFileSize"
  @close-details-modal="handleCloseDetailsModal"
  @save-company-edits="saveCompanyEdits"
  @update-edit-form="companyForm = $event"
  @edit-market-cap-input="handleEditMarketCapInput"
  @close-note-modal="closeNoteModal"
  @create-note="createNote"
  @close-upload-modal="closeUploadModal"
  @upload-documents="uploadDocuments"
  @handle-document-upload="handleDocumentUpload"
  @remove-upload-file="removeUploadFile"
  @close-quick-blog-modal="closeQuickBlogModal"
  @handle-blog-posted="handleBlogPosted"
  @close-delete-modal="closeDeleteModal"
  @confirm-delete="confirmDelete"
  @close-research-note-modal="closeResearchNoteModal"
  @handle-view-company-from-note="handleViewCompanyFromNote"
  @close-create-modal="closeCreateModal"
  @create-company="createCompany"
  @handle-market-cap-input="handleMarketCapInput"
  @update-create-form="companyForm = $event"
  @cancel-bulk-delete="cancelBulkDelete"
  @confirm-bulk-delete="confirmBulkDelete"
/>
```

### 6. Update Modal-related Method Calls

**In your existing methods, update references:**
```javascript
// Update references throughout your methods:

// OLD:
if (showDetailsModal.value && selectedCompany.value) {
  viewCompanyDetails(selectedCompany.value)
}

// NEW:
if (modals.showDetailsModal && modalData.selectedCompany) {
  viewCompanyDetails(modalData.selectedCompany)
}

// OLD:
selectedCompany.value?.researchItems

// NEW:
modalData.selectedCompany?.researchItems
```

## Benefits After Migration

1. **Cleaner Code**: ~50 fewer lines of modal state management in main component
2. **Centralized Logic**: All modal logic in one place
3. **Reusable System**: Modal manager can be used in other components
4. **Better Testing**: Modal logic can be tested independently
5. **Easier Maintenance**: Changes to modal behavior only need to be made in one place

## Migration Checklist

- [ ] Update imports
- [ ] Replace modal state variables with useModalManager
- [ ] Update all modal opening function calls
- [ ] Update all modal closing function calls
- [ ] Replace template modal section with DashboardModals component
- [ ] Update all modal state references in methods
- [ ] Test all modal functionality
- [ ] Verify modal-to-modal transitions still work
- [ ] Test bulk delete modal specifically
- [ ] Verify error handling still works in modals

## Testing Notes

After migration, test these scenarios:
1. Create company modal flow
2. Edit company modal flow with all tabs
3. Research note creation and viewing
4. Document upload flow
5. Quick blog modal (create and edit)
6. Delete confirmations
7. Bulk delete with impact analysis
8. Modal-to-modal transitions (e.g., from company details to note creation)
9. Modal state cleanup when closing
10. Error display in modals