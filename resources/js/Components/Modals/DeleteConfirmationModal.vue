<template>
  <BaseActionModal
    :show="show"
    @close="$emit('close')"
    @confirm="$emit('confirm')"
    :title="`Delete ${itemType}`"
    danger-mode
    :confirm-text="`Delete ${itemType}`"
  >
    <template #icon>
      <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
      </svg>
    </template>

    <template #default>
      Are you sure you want to delete
      <span class="font-medium text-white">"{{ itemName }}"</span>?
      <br><br>
      <span class="text-red-400 font-medium">This action cannot be undone.</span>
    </template>
  </BaseActionModal>
</template>

<script setup>
import BaseActionModal from './BaseActionModal.vue'

defineProps({
  show: {
    type: Boolean,
    default: false
  },
  itemName: {
    type: String,
    required: true
  },
  itemType: {
    type: String,
    default: 'Item'
  }
})

defineEmits(['close', 'confirm'])
</script>