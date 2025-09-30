import { ref, reactive } from 'vue'

export function useModalManager() {
  // Modal visibility state
  const modals = reactive({
    showCreateModal: false,
    showDetailsModal: false,
    showResearchNoteModal: false,
    showNoteModal: false,
    showUploadModal: false,
    showQuickBlogModal: false,
    showDeleteModal: false,
    showDeleteConfirmModal: false,
    debugModalVisible: false
  })

  // Modal data state
  const modalData = reactive({
    // Selected items
    selectedCompany: null,
    selectedResearchNoteId: null,
    selectedResearchNote: null,

    // Modal configuration
    modalInitialTab: 'overview',

    // Quick blog modal
    quickBlogCompany: null,
    editingBlogPost: null,

    // Delete modal
    deleteItem: null,
    deleteItemType: '',

    // Bulk delete modal
    deletionImpact: null,
    loadingDeletionImpact: false
  })

  // Modal management functions
  const openModal = (modalName, data = {}) => {
    if (!modals.hasOwnProperty(modalName)) {
      console.warn(`Modal "${modalName}" does not exist`)
      return
    }

    // Close all other modals first
    closeAllModals()

    // Set modal-specific data
    if (data) {
      Object.assign(modalData, data)
    }

    // Open the requested modal
    modals[modalName] = true
  }

  const closeModal = (modalName) => {
    if (!modals.hasOwnProperty(modalName)) {
      console.warn(`Modal "${modalName}" does not exist`)
      return
    }

    modals[modalName] = false

    // Reset specific modal data based on modal type
    resetModalData(modalName)
  }

  const closeAllModals = () => {
    Object.keys(modals).forEach(modalName => {
      modals[modalName] = false
    })
    resetModalData()
  }

  const resetModalData = (modalName = null) => {
    if (!modalName) {
      // Reset all modal data
      modalData.selectedCompany = null
      modalData.selectedResearchNoteId = null
      modalData.selectedResearchNote = null
      modalData.modalInitialTab = 'overview'
      modalData.quickBlogCompany = null
      modalData.editingBlogPost = null
      modalData.deleteItem = null
      modalData.deleteItemType = ''
      modalData.deletionImpact = null
      modalData.loadingDeletionImpact = false
    } else {
      // Reset data specific to the modal
      switch (modalName) {
        case 'showCreateModal':
        case 'showDetailsModal':
          if (modalName === 'showDetailsModal') {
            modalData.selectedCompany = null
            modalData.modalInitialTab = 'overview'
          }
          break

        case 'showResearchNoteModal':
          modalData.selectedResearchNoteId = null
          modalData.selectedResearchNote = null
          break

        case 'showQuickBlogModal':
          modalData.quickBlogCompany = null
          modalData.editingBlogPost = null
          break

        case 'showDeleteModal':
          modalData.deleteItem = null
          modalData.deleteItemType = ''
          break

        case 'showDeleteConfirmModal':
          modalData.deletionImpact = null
          modalData.loadingDeletionImpact = false
          break
      }
    }
  }

  // Specific modal functions (backwards compatibility)
  const openCreateModal = () => {
    openModal('showCreateModal', {
      selectedCompany: null,
      modalInitialTab: 'overview'
    })
  }

  const closeCreateModal = () => {
    closeModal('showCreateModal')
  }

  const openDetailsModal = (company, initialTab = 'overview') => {
    openModal('showDetailsModal', {
      selectedCompany: company,
      modalInitialTab: initialTab
    })
  }

  const closeDetailsModal = () => {
    closeModal('showDetailsModal')
  }

  const openResearchNoteModal = (researchNote) => {
    openModal('showResearchNoteModal', {
      selectedResearchNote: researchNote,
      selectedResearchNoteId: researchNote?.id || null
    })
  }

  const closeResearchNoteModal = () => {
    closeModal('showResearchNoteModal')
  }

  const openNoteModal = (company) => {
    openModal('showNoteModal', {
      selectedCompany: company
    })
  }

  const closeNoteModal = () => {
    closeModal('showNoteModal')
  }

  const openUploadModal = (company) => {
    openModal('showUploadModal', {
      selectedCompany: company
    })
  }

  const closeUploadModal = () => {
    closeModal('showUploadModal')
  }

  const openQuickBlogModal = (company = null, editingPost = null) => {
    openModal('showQuickBlogModal', {
      quickBlogCompany: company,
      editingBlogPost: editingPost
    })
  }

  const closeQuickBlogModal = () => {
    closeModal('showQuickBlogModal')
  }

  const openDeleteModal = (item, itemType) => {
    openModal('showDeleteModal', {
      deleteItem: item,
      deleteItemType: itemType
    })
  }

  const closeDeleteModal = () => {
    closeModal('showDeleteModal')
  }

  const openDeleteConfirmModal = (deletionImpact = null) => {
    openModal('showDeleteConfirmModal', {
      deletionImpact: deletionImpact,
      loadingDeletionImpact: !deletionImpact
    })
  }

  const closeDeleteConfirmModal = () => {
    closeModal('showDeleteConfirmModal')
  }

  // Helper function to update modal data
  const updateModalData = (updates) => {
    Object.assign(modalData, updates)
  }

  // Helper function to check if any modal is open
  const isAnyModalOpen = () => {
    return Object.values(modals).some(isOpen => isOpen)
  }

  // Helper function to get currently open modal
  const getCurrentModal = () => {
    const openModal = Object.entries(modals).find(([name, isOpen]) => isOpen)
    return openModal ? openModal[0] : null
  }

  return {
    // State
    modals,
    modalData,

    // Generic functions
    openModal,
    closeModal,
    closeAllModals,
    resetModalData,
    updateModalData,

    // Specific modal functions
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

    // Helper functions
    isAnyModalOpen,
    getCurrentModal
  }
}