# Modal Template System

This directory contains a standardized modal template system for the Rainmaker application. The system provides two base modal templates that ensure consistent styling, responsive behavior, and accessibility across all modals.

## Base Templates

### 1. BaseActionModal.vue
**Purpose**: Small confirmation/action modals (delete, update, etc.)

**Features**:
- Compact size (~400px max width on desktop)
- Full-screen on mobile with close button
- Glassmorphism design with backdrop blur
- Built-in icon, title, message, and action slots
- Danger mode for destructive actions
- ESC key and backdrop click handling
- Automatic focus management

**Props**:
```javascript
{
  show: Boolean,           // Controls modal visibility
  title: String,          // Modal title
  message: String,        // Modal message content
  closeable: Boolean,     // Allow closing (default: true)
  showCancel: Boolean,    // Show cancel button (default: true)
  showConfirm: Boolean,   // Show confirm button (default: true)
  cancelText: String,     // Cancel button text (default: 'Cancel')
  confirmText: String,    // Confirm button text (default: 'Confirm')
  confirmButtonClass: String, // Custom confirm button classes
  dangerMode: Boolean     // Use red styling for destructive actions
}
```

**Slots**:
- `icon`: Custom icon (placed in circular container)
- `title`: Custom title content
- `default`: Custom message content
- `actions`: Custom action buttons (replaces default cancel/confirm)

### 2. BaseInfoModal.vue
**Purpose**: Large content display modals

**Features**:
- 66% screen width on desktop (configurable)
- Full-screen on mobile
- Scrollable content area
- Header with title, subtitle, and actions
- Optional footer for actions
- Responsive design with mobile-first approach
- Custom scrollbar styling

**Props**:
```javascript
{
  show: Boolean,              // Controls modal visibility
  title: String,             // Modal title
  subtitle: String,          // Modal subtitle
  closeable: Boolean,        // Allow closing (default: true)
  closeOnBackdropClick: Boolean, // Close on backdrop click (default: true)
  maxWidth: String           // Modal width: '50%', '66%', '75%', '90%', 'full'
}
```

**Slots**:
- `title`: Custom title content
- `subtitle`: Custom subtitle content
- `headerActions`: Additional header action buttons
- `default`: Main modal content (scrollable)
- `footer`: Footer content for actions
- `actions`: Mobile-specific sticky actions

## Design System

### Glassmorphism Styling
Both modals use consistent glassmorphism effects:
```css
bg-gradient-to-br from-white/5 via-white/10 to-white/5 backdrop-blur-xl border border-white/20
```

### Responsive Breakpoints
- **Mobile** (`< 640px`): Full-screen modals with close buttons
- **Desktop** (`≥ 640px`): Centered modals with specified widths

### Animation System
Smooth enter/exit transitions:
- **Enter**: Fade in + scale up from 95% to 100%
- **Exit**: Fade out + scale down to 95%
- **Mobile**: Slide up from bottom on mobile

### Accessibility Features
- ARIA attributes with unique IDs
- Keyboard navigation (ESC key)
- Focus management
- Screen reader support
- Semantic HTML structure

## Usage Examples

### Basic Action Modal
```vue
<template>
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
      <TrashIcon class="w-6 h-6 text-red-400" />
    </template>
  </BaseActionModal>
</template>
```

### Custom Action Modal
```vue
<template>
  <BaseActionModal
    :show="showCustomModal"
    @close="showCustomModal = false"
  >
    <template #icon>
      <CheckIcon class="w-6 h-6 text-green-400" />
    </template>

    <template #title>Custom Action</template>

    <template #default>
      This is custom content with different styling.
    </template>

    <template #actions>
      <button @click="showCustomModal = false" class="btn-secondary">
        Maybe Later
      </button>
      <button @click="handleCustomAction" class="btn-primary">
        Proceed
      </button>
    </template>
  </BaseActionModal>
</template>
```

### Basic Info Modal
```vue
<template>
  <BaseInfoModal
    :show="showContentModal"
    @close="showContentModal = false"
    title="Content Modal"
    subtitle="This demonstrates the BaseInfoModal template"
  >
    <div class="space-y-6">
      <h3 class="text-lg font-semibold text-white">Section Title</h3>
      <p class="text-white/80">Your content here...</p>
    </div>

    <template #footer>
      <div class="flex justify-end space-x-3">
        <button @click="showContentModal = false" class="btn-secondary">
          Close
        </button>
        <button class="btn-primary">
          Save Changes
        </button>
      </div>
    </template>
  </BaseInfoModal>
</template>
```

### Large Info Modal (90% width)
```vue
<template>
  <BaseInfoModal
    :show="showLargeModal"
    @close="showLargeModal = false"
    title="Large Modal"
    max-width="90%"
  >
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>Left column content</div>
      <div>Right column content</div>
    </div>
  </BaseInfoModal>
</template>
```

### Form Modal
```vue
<template>
  <BaseInfoModal
    :show="showFormModal"
    @close="showFormModal = false"
    title="Form Example"
    :close-on-backdrop-click="false"
  >
    <form @submit.prevent="handleSubmit" class="space-y-6">
      <div>
        <label class="block text-sm font-medium text-white mb-2">Name</label>
        <input
          type="text"
          v-model="form.name"
          class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
      </div>
    </form>

    <template #footer>
      <div class="flex justify-end space-x-3">
        <button @click="showFormModal = false" class="btn-secondary">
          Cancel
        </button>
        <button @click="handleSubmit" class="btn-primary">
          Submit
        </button>
      </div>
    </template>
  </BaseInfoModal>
</template>
```

## Migration Guide

### Converting Existing Modals

1. **Identify Modal Type**:
   - Small confirmation/action modals → Use `BaseActionModal`
   - Large content modals → Use `BaseInfoModal`

2. **Replace Template Structure**:
   ```vue
   <!-- OLD -->
   <div class="fixed inset-0 z-50...">
     <div class="backdrop..."></div>
     <div class="modal...">
       <!-- content -->
     </div>
   </div>

   <!-- NEW -->
   <BaseActionModal :show="show" @close="close" @confirm="confirm">
     <!-- slots -->
   </BaseActionModal>
   ```

3. **Map Content to Slots**:
   - Header content → `title`, `subtitle` slots
   - Icon content → `icon` slot
   - Main content → `default` slot
   - Action buttons → `actions` or `footer` slots

4. **Update Props and Events**:
   - Remove custom backdrop/modal logic
   - Use standardized props
   - Update event handlers

### Example Migration

**Before** (Custom Modal):
```vue
<template>
  <div v-if="show" class="fixed inset-0 z-50">
    <div class="backdrop" @click="$emit('close')"></div>
    <div class="modal">
      <h3>Delete Item</h3>
      <p>Are you sure?</p>
      <button @click="$emit('close')">Cancel</button>
      <button @click="$emit('confirm')">Delete</button>
    </div>
  </div>
</template>
```

**After** (Using BaseActionModal):
```vue
<template>
  <BaseActionModal
    :show="show"
    @close="$emit('close')"
    @confirm="$emit('confirm')"
    title="Delete Item"
    message="Are you sure?"
    danger-mode
  />
</template>

<script setup>
import BaseActionModal from './BaseActionModal.vue'
// ... rest of component
</script>
```

## Best Practices

### 1. Choose the Right Template
- Use `BaseActionModal` for:
  - Delete confirmations
  - Update confirmations
  - Simple yes/no dialogs
  - Quick actions

- Use `BaseInfoModal` for:
  - Displaying detailed content
  - Forms and multi-step processes
  - Data visualization
  - Rich content presentation

### 2. Content Guidelines
- Keep action modal messages concise and clear
- Use appropriate icons for context
- Provide clear action button labels
- Consider accessibility in color choices

### 3. Mobile Considerations
- Test responsive behavior on small screens
- Ensure touch targets are large enough
- Consider thumb-friendly button placement
- Verify scroll performance on mobile

### 4. Performance
- Use `v-if` instead of `v-show` for modals to avoid unnecessary DOM
- Consider lazy loading for complex modal content
- Optimize images and media within modals

## File Structure
```
Components/Modals/
├── BaseActionModal.vue     # Action/confirmation modal template
├── BaseInfoModal.vue       # Large content modal template
├── ModalExamples.vue       # Example implementations
├── DeleteConfirmationModal.vue # Updated to use BaseActionModal
├── NoteCreationModal.vue   # Complex modal (to be migrated)
├── ResearchNoteModal.vue   # Complex modal (to be migrated)
└── README.md              # This documentation
```

## Future Improvements

1. **Additional Templates**:
   - BaseWizardModal for multi-step processes
   - BaseDrawerModal for side panels
   - BaseAlertModal for notifications

2. **Enhanced Features**:
   - Animation customization
   - Theme variants
   - Size presets
   - Advanced accessibility features

3. **Developer Tools**:
   - Modal generator CLI
   - Testing utilities
   - Storybook integration
   - Performance monitoring

---

*This modal system provides a solid foundation for consistent, accessible, and maintainable modal interfaces across the Rainmaker application.*