<template>
  <Teleport to="body">
    <div v-show="show" class="fixed inset-0 z-50 flex items-start sm:items-center justify-center p-0 sm:p-4" @click.self="$emit('close')">
      <!-- Backdrop with Dark Mode Context -->
      <div class="fixed inset-0 bg-black/70 backdrop-blur-md dark:bg-black/80"></div>
      <!-- Modal Container with Dark Mode Context -->
      <div class="relative dark bg-gradient-to-br from-white/5 via-white/10 to-white/5 backdrop-blur-xl rounded-none sm:rounded-2xl border-0 sm:border sm:border-white/10 w-full h-full sm:h-auto sm:w-[66%] sm:max-h-[80vh] overflow-y-auto shadow-[0_8px_32px_0_rgba(31,38,135,0.15)] transition-all duration-500" style="backdrop-filter: blur(20px) saturate(180%);">
      <!-- Modal Header -->
      <div class="sticky top-0 bg-gray-900 border-b border-white/20 px-4 sm:px-8 py-4 sm:py-6 rounded-t-none sm:rounded-t-2xl z-10">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-3xl font-semibold text-white">{{ props.isEditing ? '✏️ Edit Research Post' : '📝 Create Research Post' }}</h2>
            <p class="text-lg text-gray-300 mt-1" v-if="selectedCompany">
              Investment analysis for {{ selectedCompany.name }} ({{ selectedCompany.ticker }})
            </p>
            <p class="text-sm text-gray-400 mt-1" v-else>
              Professional investment research with rich formatting
            </p>
          </div>
          <div class="flex items-center space-x-3">
          <!-- Save Button -->
          <button
            @click="$emit('save')"
            :disabled="creatingNote"
            class="group relative px-6 py-3 transition-all duration-500 hover:scale-105 bg-gradient-to-br from-green-500/20 via-green-400/10 to-transparent text-green-200 hover:text-white rounded-full shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(34,197,94,0.2)] border border-white/10 backdrop-blur-xl font-medium disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
            style="backdrop-filter: blur(20px) saturate(150%);"
            :title="props.isEditing ? 'Update Research' : 'Save Research'"
          >
            <svg v-if="!creatingNote" class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <svg v-else class="animate-spin w-5 h-5 mr-2 inline" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ creatingNote ? (props.isEditing ? 'Updating...' : 'Saving...') : (props.isEditing ? 'Update Research' : 'Save Research') }}
          </button>
          <!-- Close Button -->
          <button
            @click="$emit('close')"
            class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 border border-white/20 hover:border-white/30 flex items-center justify-center transition-all duration-300 hover:scale-105 backdrop-blur-xl"
            style="backdrop-filter: blur(20px) saturate(150%);"
            title="Close"
          >
            <svg class="w-5 h-5 text-white/70 hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
          </div>
        </div>
      </div>

      <!-- Modal Content -->
      <div class="px-4 sm:px-8 py-4 sm:py-6">

      <form class="space-y-6">
        <!-- General Error Message -->
        <div v-if="errors.general" class="bg-gradient-to-br from-red-500/20 via-red-400/10 to-transparent backdrop-blur-xl border border-red-400/30 text-red-200 px-4 py-3 rounded-xl mb-4" style="backdrop-filter: blur(20px) saturate(180%);">
          {{ errors.general }}
        </div>

        <!-- Title -->
        <div>
          <label for="note_title" class="block text-sm font-medium text-white mb-2">Research Title</label>
          <input
            id="note_title"
            v-model="props.form.title"
            type="text"
            required
            class="w-full px-4 py-4 text-xl rounded-xl bg-black/10 backdrop-blur-xl border border-white/20 text-white placeholder-gray-400 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-500 font-semibold"
            style="backdrop-filter: blur(20px) saturate(180%);"
            placeholder="e.g., Apple Inc. Q3 2024: Strong iPhone Sales Drive Revenue Growth"
          />
          <div v-if="errors.title" class="text-red-400 text-sm mt-1">{{ errors.title }}</div>
          <div class="mt-1 text-xs text-gray-400">
            ✨ Write a compelling headline that captures your key investment insight
          </div>
        </div>

        <!-- Content -->
        <div>
          <label for="note_content" class="block text-sm font-medium text-white mb-2">Research Content</label>
          <div class="relative">
            <!-- Tiptap Rich Text Editor with Clean Toolbar -->
            <div class="tiptap-editor-container">
              <!-- Clean Toolbar -->
              <div v-if="editor" class="tiptap-toolbar">
                <!-- Text Formatting -->
                <div class="toolbar-group">
                  <button
                    type="button"
                    @click="editor.chain().focus().toggleBold().run()"
                    :class="{ 'is-active': editor.isActive('bold') }"
                    class="toolbar-btn"
                    title="Bold"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4h4a4 4 0 110 8H8V4zM8 12h6a4 4 0 110 8H8v-8z"></path>
                    </svg>
                  </button>
                  <button
                    type="button"
                    @click="editor.chain().focus().toggleItalic().run()"
                    :class="{ 'is-active': editor.isActive('italic') }"
                    class="toolbar-btn"
                    title="Italic"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 4l4 16m-4-8h8"></path>
                    </svg>
                  </button>
                  <button
                    type="button"
                    @click="editor.chain().focus().toggleStrike().run()"
                    :class="{ 'is-active': editor.isActive('strike') }"
                    class="toolbar-btn"
                    title="Strikethrough"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12h12"></path>
                    </svg>
                  </button>
                  <button
                    type="button"
                    @click="editor.chain().focus().toggleHighlight().run()"
                    :class="{ 'is-active': editor.isActive('highlight') }"
                    class="toolbar-btn"
                    title="Highlight"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                    </svg>
                  </button>
                </div>

                <!-- Headings -->
                <div class="toolbar-group">
                  <button
                    type="button"
                    @click="editor.chain().focus().toggleHeading({ level: 1 }).run()"
                    :class="{ 'is-active': editor.isActive('heading', { level: 1 }) }"
                    class="toolbar-btn"
                    title="Heading 1"
                  >
                    H1
                  </button>
                  <button
                    type="button"
                    @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
                    :class="{ 'is-active': editor.isActive('heading', { level: 2 }) }"
                    class="toolbar-btn"
                    title="Heading 2"
                  >
                    H2
                  </button>
                  <button
                    type="button"
                    @click="editor.chain().focus().toggleHeading({ level: 3 }).run()"
                    :class="{ 'is-active': editor.isActive('heading', { level: 3 }) }"
                    class="toolbar-btn"
                    title="Heading 3"
                  >
                    H3
                  </button>
                </div>

                <!-- Lists & Content -->
                <div class="toolbar-group">
                  <button
                    type="button"
                    @click="editor.chain().focus().toggleBulletList().run()"
                    :class="{ 'is-active': editor.isActive('bulletList') }"
                    class="toolbar-btn"
                    title="Bullet List"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                  </button>
                  <button
                    type="button"
                    @click="editor.chain().focus().toggleOrderedList().run()"
                    :class="{ 'is-active': editor.isActive('orderedList') }"
                    class="toolbar-btn"
                    title="Numbered List"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10M7 12h4m6 5H7l4-4"></path>
                    </svg>
                  </button>
                  <button
                    type="button"
                    @click="editor.chain().focus().toggleBlockquote().run()"
                    :class="{ 'is-active': editor.isActive('blockquote') }"
                    class="toolbar-btn"
                    title="Quote"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                  </button>
                  <button
                    type="button"
                    @click="editor.chain().focus().toggleCodeBlock().run()"
                    :class="{ 'is-active': editor.isActive('codeBlock') }"
                    class="toolbar-btn"
                    title="Code Block"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m-4 4l-4-4 4-4m8 0l4 4-4 4"></path>
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Editor Content -->
              <EditorContent
                :editor="editor"
                class="tiptap-content"
                placeholder="Write your research content here..."
              />
            </div>
          </div>
          <div v-if="errors.content" class="text-red-400 text-sm mt-1">{{ errors.content }}</div>
          <div class="mt-2 text-xs text-gray-400">
💡 <strong>Pro tip:</strong> Use the toolbar buttons to format your content professionally. You can also paste markdown text directly into the editor - it will be automatically converted to rich text formatting!
          </div>
        </div>

        <!-- Category, Visibility, and Source Date -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <label for="note_category" class="block text-sm font-medium text-white mb-2">Category</label>
            <div v-if="!showNewCategoryInput" class="relative">
              <select
                id="note_category"
                v-model="props.form.category_id"
                class="w-full px-4 py-3 pr-12 rounded-xl bg-black/10 backdrop-blur-xl border border-white/20 text-white shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-500"
                style="backdrop-filter: blur(20px) saturate(180%);"
              >
                <option value="">Select category (optional)</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                  {{ category.name }}
                </option>
              </select>
              <!-- Plus Icon for Admin -->
              <button
                v-if="user && (user.roles?.some(role => role.name === 'admin') || user.permissions?.some(perm => perm.name === 'manage categories'))"
                @click="toggleNewCategoryInput"
                type="button"
                class="absolute right-3 top-1/2 transform -translate-y-1/2 w-6 h-6 rounded-full bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 hover:text-blue-300 transition-all duration-200 flex items-center justify-center border border-blue-400/30 hover:border-blue-400/50"
                title="Create new category"
              >
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
              </button>
            </div>

            <!-- New Category Creation Form -->
            <div v-else class="space-y-3">
              <div class="flex gap-2">
                <input
                  id="new-category-name"
                  v-model="newCategoryName"
                  type="text"
                  placeholder="Category name..."
                  class="flex-1 px-4 py-3 rounded-xl bg-black/10 backdrop-blur-xl border border-white/20 text-white placeholder-gray-400 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-500"
                  style="backdrop-filter: blur(20px) saturate(180%);"
                  @keydown.enter="createNewCategory"
                />
                <input
                  v-model="newCategoryColor"
                  type="color"
                  class="w-12 h-12 rounded-xl border border-white/20 bg-transparent cursor-pointer"
                  title="Category color"
                />
              </div>
              <div class="flex gap-2">
                <button
                  @click="createNewCategory"
                  :disabled="!newCategoryName.trim() || creatingCategory"
                  type="button"
                  class="px-4 py-2 bg-green-500/20 hover:bg-green-500/30 text-green-300 rounded-lg border border-green-400/20 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                >
                  <svg v-if="creatingCategory" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <span>{{ creatingCategory ? 'Creating...' : 'Create' }}</span>
                </button>
                <button
                  @click="toggleNewCategoryInput"
                  type="button"
                  class="px-4 py-2 bg-gray-500/20 hover:bg-gray-500/30 text-gray-300 rounded-lg border border-gray-400/20 transition-colors"
                >
                  Cancel
                </button>
              </div>
            </div>
            <div v-if="errors.category_id" class="text-red-400 text-sm mt-1">{{ errors.category_id }}</div>
          </div>

          <div>
            <label for="note_visibility" class="block text-sm font-medium text-white mb-2">Visibility</label>
            <select
              id="note_visibility"
              v-model="props.form.visibility"
              class="w-full px-4 py-3 rounded-xl bg-black/10 backdrop-blur-xl border border-white/20 text-white shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-500"
              style="backdrop-filter: blur(20px) saturate(180%);"
            >
              <option value="private">Private (Only me)</option>
              <option value="team">Team</option>
              <option value="public">Public</option>
            </select>
            <div v-if="errors.visibility" class="text-red-400 text-sm mt-1">{{ errors.visibility }}</div>
          </div>

          <div>
            <label for="note_source_date" class="block text-sm font-medium text-white mb-2">Source Date</label>
            <input
              id="note_source_date"
              v-model="props.form.source_date"
              type="date"
              class="w-full px-4 py-3 rounded-xl bg-black/10 backdrop-blur-xl border border-white/20 text-white shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-500"
              style="backdrop-filter: blur(20px) saturate(180%);"
              placeholder="YYYY-MM-DD"
            />
            <div v-if="errors.source_date" class="text-red-400 text-sm mt-1">{{ errors.source_date }}</div>
            <div class="mt-1 text-xs text-gray-400">
              📅 When was this article originally published?
            </div>
          </div>
        </div>

        <!-- Tags Section -->
        <div>
          <label class="block text-sm font-medium text-white mb-2">Tags</label>
          <TagSelector
            v-model="props.form.selectedTags"
            placeholder="Search or create tags..."
            help-text="Add tags to categorize and organize your research"
          />
          <div v-if="errors.tag_ids" class="text-red-400 text-sm mt-1">{{ errors.tag_ids }}</div>
        </div>

        <!-- Research Files Section -->
        <div class="space-y-4">
          <label class="block text-sm font-medium text-white mb-3">Research Files (optional)</label>

          <!-- Upload Type Toggle -->
          <div class="flex space-x-2 mb-4">
            <button
              type="button"
              @click="props.form.uploadType = 'file'"
              :class="[
                'px-4 py-2 text-sm rounded-lg font-medium transition-all duration-300',
                (!props.form.uploadType || props.form.uploadType === 'file')
                  ? 'bg-blue-500/20 text-blue-300 border border-blue-400/30'
                  : 'bg-white/5 text-gray-300 border border-white/10 hover:bg-white/10'
              ]"
            >
              📁 Upload Files
            </button>
            <button
              type="button"
              @click="props.form.uploadType = 'url'"
              :class="[
                'px-4 py-2 text-sm rounded-lg font-medium transition-all duration-300',
                props.form.uploadType === 'url'
                  ? 'bg-blue-500/20 text-blue-300 border border-blue-400/30'
                  : 'bg-white/5 text-gray-300 border border-white/10 hover:bg-white/10'
              ]"
            >
              🔗 Download from URL
            </button>
            <button
              type="button"
              @click="props.form.uploadType = 'existing'; $emit('load-existing-files')"
              :class="[
                'px-4 py-2 text-sm rounded-lg font-medium transition-all duration-300',
                props.form.uploadType === 'existing'
                  ? 'bg-blue-500/20 text-blue-300 border border-blue-400/30'
                  : 'bg-white/5 text-gray-300 border border-white/10 hover:bg-white/10'
              ]"
            >
              🗂️ Select Existing Files
            </button>
          </div>

          <!-- File Upload Area -->
          <div v-if="!props.form.uploadType || props.form.uploadType === 'file'"
               @click="triggerFileInput"
               class="border-2 border-dashed border-white/20 rounded-xl p-6 text-center hover:border-white/30 transition-colors backdrop-blur-xl bg-white/5 cursor-pointer">
            <svg class="mx-auto h-12 w-12 text-gray-300" stroke="currentColor" fill="none" viewBox="0 0 48 48">
              <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <div class="mt-4">
              <span class="mt-2 block text-sm font-medium text-white">
                Click to upload files or drag and drop
              </span>
              <span class="mt-1 block text-xs text-gray-400">
                PDF, DOC, XLS, PPT, Images, TXT, CSV (max 10MB each)
              </span>
              <input
                ref="fileInput"
                id="research-file-upload"
                name="research-file-upload"
                type="file"
                multiple
                accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.jpg,.jpeg,.png,.gif,.webp,.svg"
                @change="(event) => {
                  const files = Array.from(event.target.files);
                  if (files.length > 0) {
                    if (!props.form.files) props.form.files = [];
                    props.form.files = [...props.form.files, ...files];
                  }
                  $emit('file-upload', event);
                }"
                class="sr-only"
              />
            </div>
          </div>

          <!-- URL Download Area -->
          <div v-if="props.form.uploadType === 'url'" class="space-y-4">
            <div class="flex items-center space-x-2">
              <input
                v-model="props.form.newUrl"
                type="url"
                placeholder="https://example.com/article-to-extract.html"
                class="flex-1 px-4 py-3 rounded-xl bg-black/10 backdrop-blur-xl border border-white/20 text-white placeholder-gray-400 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-500"
                style="backdrop-filter: blur(20px) saturate(180%);"
              />
              <button
                type="button"
                @click="extractArticle"
                :disabled="!form.newUrl || extractingArticle"
                class="px-4 py-3 bg-green-500/20 text-green-300 border border-green-400/30 rounded-xl hover:bg-green-500/30 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 font-medium"
              >
                <svg v-if="!extractingArticle" class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <svg v-else class="animate-spin w-4 h-4 mr-2 inline" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ extractingArticle ? 'Extracting...' : 'Extract Article' }}
              </button>
              <button
                type="button"
                @click="$emit('add-url', form.newUrl)"
                :disabled="!form.newUrl"
                class="px-4 py-3 bg-blue-500/20 text-blue-300 border border-blue-400/30 rounded-xl hover:bg-blue-500/30 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 font-medium"
              >
                Add URL
              </button>
            </div>

            <!-- Article Extraction Preview -->
            <div v-if="extractedArticle" class="mt-6 p-6 bg-green-500/10 border border-green-400/30 rounded-xl backdrop-blur-xl space-y-4">
              <div class="flex items-center justify-between">
                <h4 class="text-lg font-medium text-green-300 flex items-center">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                  </svg>
                  Article Extracted Successfully
                </h4>
                <button
                  type="button"
                  @click="clearExtractedArticle"
                  class="text-red-400 hover:text-red-300 p-1 transition-colors"
                  title="Clear extraction"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                  <span class="text-gray-400">Title:</span>
                  <span class="text-white ml-2">{{ extractedArticle.title || 'No title found' }}</span>
                </div>
                <div v-if="extractedArticle.author">
                  <span class="text-gray-400">Author:</span>
                  <span class="text-white ml-2">{{ extractedArticle.author }}</span>
                </div>
                <div v-if="extractedArticle.word_count">
                  <span class="text-gray-400">Word Count:</span>
                  <span class="text-white ml-2">{{ extractedArticle.word_count }} words</span>
                </div>
                <div v-if="extractedArticle.read_time">
                  <span class="text-gray-400">Read Time:</span>
                  <span class="text-white ml-2">{{ extractedArticle.read_time }} min</span>
                </div>
                <div v-if="extractedArticle.images && extractedArticle.images.length > 0">
                  <span class="text-gray-400">Images:</span>
                  <span class="text-white ml-2">{{ extractedArticle.images.length }} images found</span>
                </div>
                <div v-if="extractedArticle.main_image">
                  <span class="text-gray-400">Main Image:</span>
                  <span class="text-green-300 ml-2">✓ Available</span>
                </div>
              </div>

              <!-- Main Image Preview -->
              <div v-if="extractedArticle.main_image" class="p-4 bg-black/20 rounded-lg border border-white/10">
                <h5 class="text-sm font-medium text-gray-300 mb-3">Main Image:</h5>
                <div class="flex justify-center">
                  <img
                    :src="extractedArticle.main_image"
                    :alt="extractedArticle.title"
                    class="max-h-32 max-w-full object-contain rounded-lg border border-white/10 shadow-lg"
                    @error="$event.target.style.display='none'"
                  />
                </div>
              </div>

              <!-- Images Gallery (Collapsible) -->
              <div v-if="extractedArticle.images && extractedArticle.images.length > 0" class="p-4 bg-black/20 rounded-lg border border-white/10">
                <button
                  @click="showImageGallery = !showImageGallery"
                  class="flex items-center justify-between w-full text-sm font-medium text-gray-300 mb-3 hover:text-white transition-colors"
                >
                  <span>Additional Images ({{ extractedArticle.images.length }}) - Select to Include</span>
                  <svg
                    :class="{ 'rotate-180': showImageGallery }"
                    class="w-4 h-4 transition-transform duration-200"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </button>

                <div v-if="showImageGallery" class="space-y-3">
                  <!-- Include main image option -->
                  <div v-if="extractedArticle.main_image" class="flex items-center space-x-3 p-3 bg-black/10 rounded-lg border border-white/10">
                    <input
                      type="checkbox"
                      :id="`main-image-${extractedArticle.main_image}`"
                      v-model="includeMainImage"
                      class="rounded bg-white/10 border-white/20 text-green-400 focus:ring-green-400/20"
                    />
                    <img
                      :src="extractedArticle.main_image"
                      alt="Main image"
                      class="w-16 h-16 object-cover rounded border border-white/10"
                      @error="$event.target.style.display='none'"
                    />
                    <div class="flex-1">
                      <label :for="`main-image-${extractedArticle.main_image}`" class="text-sm text-white cursor-pointer">
                        <strong>Main Image</strong> - Include at the beginning of the article
                      </label>
                    </div>
                  </div>

                  <!-- Additional images selection -->
                  <div class="grid grid-cols-1 gap-3 max-h-48 overflow-y-auto">
                    <div
                      v-for="(image, index) in extractedArticle.images.slice(0, 6)"
                      :key="index"
                      class="flex items-center space-x-3 p-3 bg-black/10 rounded-lg border border-white/10"
                    >
                      <input
                        type="checkbox"
                        :id="`image-${index}`"
                        v-model="selectedImageIndices"
                        :value="index"
                        class="rounded bg-white/10 border-white/20 text-green-400 focus:ring-green-400/20"
                      />
                      <img
                        :src="image.url"
                        :alt="image.alt || `Image ${index + 1}`"
                        class="w-16 h-16 object-cover rounded border border-white/10 cursor-pointer"
                        @error="$event.target.style.display='none'"
                        @click="selectedImage = image.url"
                      />
                      <div class="flex-1">
                        <label :for="`image-${index}`" class="text-sm text-white cursor-pointer">
                          Additional Image {{ index + 1 }}
                        </label>
                        <p class="text-xs text-gray-400">Click image to preview</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Content Preview -->
              <div class="max-h-48 overflow-y-auto p-4 bg-black/20 rounded-lg border border-white/10">
                <h5 class="text-sm font-medium text-gray-300 mb-2">Content Preview:</h5>
                <div class="text-gray-300 text-sm leading-relaxed" v-html="extractedArticle.excerpt || extractedArticle.content?.substring(0, 500) + '...'"></div>
              </div>

              <!-- Image Lightbox Modal -->
              <div v-if="selectedImage" @click="selectedImage = null" class="fixed inset-0 z-[9999] bg-black/80 flex items-center justify-center p-4">
                <div class="relative max-w-4xl max-h-full">
                  <img :src="selectedImage" class="max-w-full max-h-full object-contain rounded-lg" />
                  <button
                    @click="selectedImage = null"
                    class="absolute top-2 right-2 w-8 h-8 bg-black/50 text-white rounded-full flex items-center justify-center hover:bg-black/70 transition-colors"
                  >
                    ×
                  </button>
                </div>
              </div>

              <div class="flex space-x-3">
                <button
                  type="button"
                  @click="useExtractedArticle"
                  class="flex-1 px-4 py-2 bg-green-500/20 text-green-300 border border-green-400/30 rounded-lg hover:bg-green-500/30 transition-all duration-300 font-medium"
                >
                  ✅ Use This Content
                </button>
                <button
                  type="button"
                  @click="clearExtractedArticle"
                  class="px-4 py-2 bg-red-500/20 text-red-300 border border-red-400/30 rounded-lg hover:bg-red-500/30 transition-all duration-300 font-medium"
                >
                  ❌ Discard
                </button>
              </div>
            </div>

            <!-- Added URLs List -->
            <div v-if="props.form.urls && props.form.urls.length > 0" class="mt-4 space-y-2">
              <h4 class="text-sm font-medium text-white">URLs to Download:</h4>
              <div v-for="(url, index) in props.form.urls" :key="index"
                   class="flex items-center justify-between p-3 bg-blue-500/10 border border-blue-400/30 rounded-xl backdrop-blur-xl">
                <div class="flex items-center flex-1 min-w-0">
                  <svg class="w-5 h-5 text-blue-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                  </svg>
                  <span class="text-blue-300 truncate">{{ url }}</span>
                </div>
                <button
                  type="button"
                  @click="$emit('remove-url', index)"
                  class="text-red-400 hover:text-red-300 p-1 transition-colors ml-2 flex-shrink-0"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Existing Files Selection Area -->
          <div v-if="props.form.uploadType === 'existing'" class="space-y-4">
            <div class="flex items-center space-x-2">
              <input
                v-model="existingFilesSearch"
                @input="$emit('search-existing-files', $event.target.value)"
                type="text"
                placeholder="Search your existing files..."
                class="flex-1 px-4 py-3 rounded-xl bg-black/10 backdrop-blur-xl border border-white/20 text-white placeholder-gray-400 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-500"
                style="backdrop-filter: blur(20px) saturate(180%);"
              />
            </div>

            <!-- Loading existing files -->
            <div v-if="loadingExistingFiles" class="text-center py-8">
              <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-400 mx-auto"></div>
              <p class="text-sm text-gray-400 mt-2">Loading your files...</p>
            </div>

            <!-- Existing files list -->
            <div v-else-if="availableFiles.length > 0"
                 class="max-h-64 overflow-y-auto border border-white/10 rounded-xl bg-white/5 backdrop-blur-xl">
              <table class="min-w-full">
                <thead class="border-b border-white/10">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                      <input
                        type="checkbox"
                        @change="toggleAllFiles($event)"
                        class="rounded bg-white/10 border-white/20 text-blue-400 focus:ring-blue-400/20"
                      />
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Size</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Source</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                  <tr
                    v-for="file in availableFiles"
                    :key="file.id"
                    @click="$emit('toggle-file-selection', file)"
                    class="cursor-pointer transition-all duration-300 hover:bg-white/10"
                    :class="{
                      'bg-blue-500/20': props.form.selectedExistingFiles?.some(f => f.id === file.id),
                      'bg-transparent': !props.form.selectedExistingFiles?.some(f => f.id === file.id)
                    }"
                  >
                    <td class="px-4 py-3">
                      <input
                        type="checkbox"
                        :checked="props.form.selectedExistingFiles?.some(f => f.id === file.id)"
                        @click.stop
                        @change="$emit('toggle-file-selection', file)"
                        class="rounded bg-white/10 border-white/20 text-blue-400 focus:ring-blue-400/20"
                      />
                    </td>
                    <td class="px-4 py-3">
                      <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-white text-sm truncate">{{ file.name || file.original_name }}</span>
                      </div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-400">{{ formatFileSize(file.size) }}</td>
                    <td class="px-4 py-3 text-sm text-gray-400 capitalize">{{ file.source_type?.replace('_', ' ') }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- No files state -->
            <div v-else-if="!loadingExistingFiles" class="text-center py-8 text-gray-400">
              <svg class="mx-auto h-12 w-12 text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
              </svg>
              <p class="mt-2 text-sm">No existing files found</p>
              <p class="text-xs text-gray-500">Upload some files first to use this feature</p>
            </div>

            <!-- Selected files count -->
            <div v-if="props.form.selectedExistingFiles?.length > 0" class="text-sm text-blue-400">
              {{ props.form.selectedExistingFiles.length }} file(s) selected
            </div>
          </div>


          <!-- File List -->
          <div v-if="props.form.files && props.form.files.length > 0" class="mt-4 space-y-2">
            <div v-for="(file, index) in props.form.files" :key="index"
                 class="flex items-center justify-between p-3 bg-white/5 border border-white/10 rounded-xl backdrop-blur-xl">
              <div class="flex items-center">
                <svg class="w-5 h-5 text-gray-300 mr-3" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
                <div>
                  <p class="text-white font-medium">{{ file.name }}</p>
                  <p class="text-sm text-gray-400">{{ formatFileSize(file.size) }}</p>
                </div>
              </div>
              <button
                type="button"
                @click="$emit('remove-file', index)"
                class="text-red-400 hover:text-red-300 p-1 transition-colors"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
          </div>
          <div v-if="errors.files" class="text-red-400 text-sm mt-1">{{ errors.files }}</div>
        </div>

      </form>
      </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, watch, computed, nextTick } from 'vue'
import axios from 'axios'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Highlight from '@tiptap/extension-highlight'
import { Markdown } from 'tiptap-markdown'
import TagSelector from '@/Components/TagSelector.vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  selectedCompany: {
    type: Object,
    default: null
  },
  form: {
    type: Object,
    required: true
  },
  errors: {
    type: Object,
    default: () => ({})
  },
  creatingNote: {
    type: Boolean,
    default: false
  },
  categories: {
    type: Array,
    default: () => []
  },
  availableFiles: {
    type: Array,
    default: () => []
  },
  loadingExistingFiles: {
    type: Boolean,
    default: false
  },
  isEditing: {
    type: Boolean,
    default: false
  },
  user: {
    type: Object,
    default: null
  },
  tags: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits([
  'close',
  'save',
  'file-upload',
  'add-url',
  'remove-url',
  'remove-file',
  'search-existing-files',
  'load-existing-files',
  'toggle-file-selection',
  'category-created'
])

// Reactive data
const existingFilesSearch = ref('')
const extractingArticle = ref(false)

// File input ref
const fileInput = ref(null)

// Category creation
const showNewCategoryInput = ref(false)
const newCategoryName = ref('')
const newCategoryColor = ref('#3B82F6')
const creatingCategory = ref(false)
const extractedArticle = ref(null)
const showImageGallery = ref(false)
const selectedImage = ref(null)
const includeMainImage = ref(true)
const selectedImageIndices = ref([])

// Create reactive local form reference to enable template binding with fallback
const form = computed(() => {
  return props.form || {
    title: '',
    content: '',
    company_id: null,
    category_id: '',
    source_date: '',
    visibility: 'private',
    selectedTags: [],
    newUrl: '',
    selectedExistingFiles: []
  }
})

// Tiptap Editor Setup
const editor = useEditor({
  content: props.form?.content || '',
  extensions: [
    StarterKit.configure({
      heading: {
        levels: [1, 2, 3, 4],
      },
    }),
    Highlight.configure({
      HTMLAttributes: {
        class: 'bg-yellow-400/20 text-yellow-300 px-1 rounded',
      },
    }),
    Markdown.configure({
      html: true,                // Enable HTML output
      tightLists: true,         // Render tight lists
      tightListClass: 'tight',  // CSS class for tight lists
      bulletListMarker: '-',    // Use - for bullet lists
      linkify: true,           // Automatically detect links
      breaks: false,           // Don't convert line breaks to <br>
      transformPastedText: true, // Transform pasted markdown
      transformCopiedText: true, // Transform copied text to markdown
    }),
  ],
  editorProps: {
    attributes: {
      class: 'prose prose-invert max-w-none focus:outline-none p-4 min-h-[400px]',
    },
  },
  onUpdate: ({ editor }) => {
    // Update the form content when editor changes
    if (props.form) {
      props.form.content = editor.getHTML()
    }
  },
})

// Watch for form content changes to update editor
watch(() => props.form?.content, (newContent) => {
  if (editor.value && newContent !== editor.value.getHTML()) {
    editor.value.commands.setContent(newContent || '')
  }
})

// Watch for form prop changes to update editor
watch(() => props.form, (newForm, oldForm) => {
  // Form prop changed - could add any necessary reactive updates here if needed
}, { deep: true, immediate: true })

// Utility functions
const formatFileSize = (bytes) => {
  if (!bytes) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const toggleAllFiles = (event) => {
  if (event.target.checked) {
    // Select all files
    props.form.selectedExistingFiles = [...props.availableFiles]
  } else {
    // Deselect all files
    props.form.selectedExistingFiles = []
  }
}

// Category creation functions
const createNewCategory = async () => {
  if (!newCategoryName.value.trim()) return

  try {
    creatingCategory.value = true
    const response = await axios.post('/api/categories', {
      name: newCategoryName.value.trim(),
      color: newCategoryColor.value
    })

    // Emit the new category to parent component
    emit('category-created', response.data)

    // Reset form
    newCategoryName.value = ''
    newCategoryColor.value = '#3B82F6'
    showNewCategoryInput.value = false
  } catch (error) {
    console.error('Error creating category:', error)
    // You could add toast notification here
  } finally {
    creatingCategory.value = false
  }
}

const toggleNewCategoryInput = () => {
  showNewCategoryInput.value = !showNewCategoryInput.value
  if (showNewCategoryInput.value) {
    // Focus on input after next tick
    nextTick(() => {
      const input = document.querySelector('#new-category-name')
      if (input) input.focus()
    })
  }
}

// Article extraction methods
const extractArticle = async () => {
  if (!props.form.newUrl) return

  extractingArticle.value = true
  extractedArticle.value = null

  try {
    const response = await fetch('/api/extract-article', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        url: props.form.newUrl
      })
    })

    const result = await response.json()

    if (result.success) {
      extractedArticle.value = result.data
      // Optional: Clear the URL input after successful extraction
      // form.newUrl = ''
    } else {
      alert('Failed to extract article: ' + result.message)
    }
  } catch (error) {
    console.error('Article extraction error:', error)
    alert('Error extracting article. Please try again.')
  } finally {
    extractingArticle.value = false
  }
}

const useExtractedArticle = () => {
  if (!extractedArticle.value) return

  // Auto-populate the form with extracted data
  if (extractedArticle.value.title && !props.form.title) {
    props.form.title = extractedArticle.value.title
  }

  // Auto-populate source date if available
  if (extractedArticle.value.published_date && !props.form.source_date) {
    props.form.source_date = extractedArticle.value.published_date
  }

  // Enhance content with user-selected images
  let enhancedContent = extractedArticle.value.content || ''

  // Add main image at the beginning if user selected it
  if (includeMainImage.value && extractedArticle.value.main_image) {
    const mainImageHtml = `<figure>
      <img src="${extractedArticle.value.main_image}" alt="${extractedArticle.value.title || 'Article image'}" style="max-width: 100%; height: auto; border-radius: 8px; margin: 1rem 0;" />
      <figcaption style="font-size: 0.9em; color: #666; text-align: center; margin-top: 8px; font-style: italic;">Main article image</figcaption>
    </figure>`

    enhancedContent = mainImageHtml + '\n\n' + enhancedContent
  }

  // Add user-selected additional images
  if (selectedImageIndices.value.length > 0 && extractedArticle.value.images) {
    const selectedImages = selectedImageIndices.value
      .map(index => extractedArticle.value.images[index])
      .filter(img => img && img.url && img.url !== extractedArticle.value.main_image) // Avoid duplicating main image
      .map((img, displayIndex) => `<figure>
        <img src="${img.url}" alt="${img.alt || `Additional image ${displayIndex + 1}`}" style="max-width: 100%; height: auto; border-radius: 8px; margin: 1rem 0;" />
        <figcaption style="font-size: 0.9em; color: #666; text-align: center; margin-top: 8px; font-style: italic;">${img.alt || `Additional image ${displayIndex + 1}`}</figcaption>
      </figure>`)
      .join('\n\n')

    if (selectedImages) {
      enhancedContent += '\n\n<hr style="margin: 2rem 0; border: none; height: 1px; background: #e5e7eb;" />\n\n<h4 style="color: #374151; margin-bottom: 1rem;">Additional Images</h4>\n\n' + selectedImages
    }
  }

  // Always add source URL citation at the bottom
  if (props.form.newUrl) {
    const sourceUrl = props.form.newUrl
    const sourceDomain = new URL(sourceUrl).hostname.replace('www.', '')

    enhancedContent += `\n\n<hr style="margin: 2rem 0; border: none; height: 2px; background: linear-gradient(to right, transparent, rgba(59, 130, 246, 0.5), transparent);" />

<div style="margin-top: 2rem; padding: 1rem; background: rgba(59, 130, 246, 0.1); border-left: 4px solid #3b82f6; border-radius: 0 8px 8px 0;">
  <p style="margin: 0; font-size: 0.9em; color: #9ca3af; font-style: italic;">
    <strong>Source:</strong>
    <a href="${sourceUrl}" target="_blank" rel="noopener noreferrer" style="color: #60a5fa; text-decoration: none; border-bottom: 1px solid rgba(96, 165, 250, 0.3);">
      ${sourceDomain}
    </a>
  </p>
  <p style="margin: 0.5rem 0 0 0; font-size: 0.8em; color: #6b7280;">
    Article extracted on ${new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}
  </p>
</div>`
  }

  // Set the enhanced content in the editor
  if (editor.value) {
    editor.value.commands.setContent(enhancedContent)
    props.form.content = enhancedContent
  }

  // Clear the extracted article preview and reset selections
  extractedArticle.value = null
  includeMainImage.value = true
  selectedImageIndices.value = []
  showImageGallery.value = false

  // Optional: Clear the URL input
  props.form.newUrl = ''
}

const clearExtractedArticle = () => {
  extractedArticle.value = null
  showImageGallery.value = false
  selectedImage.value = null
}

// Trigger file input click
const triggerFileInput = () => {
  if (fileInput.value) {
    fileInput.value.click()
  }
}


</script>

<style scoped>
/* Tiptap Editor Styling to match modal's glassmorphism design */
.tiptap-editor-container {
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px 0 rgba(31, 38, 135, 0.15);
  background: rgba(0, 0, 0, 0.2);
  backdrop-filter: blur(20px) saturate(180%);
  border: 1px solid rgba(255, 255, 255, 0.2);
  min-height: 400px;
  display: flex;
  flex-direction: column;
}

/* Clean Toolbar Styling */
.tiptap-toolbar {
  background: rgba(0, 0, 0, 0.3);
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  padding: 12px 16px;
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  backdrop-filter: blur(20px) saturate(180%);
  border-radius: 12px 12px 0 0;
}

.toolbar-group {
  display: flex;
  gap: 4px;
  align-items: center;
  padding: 0 8px;
  border-right: 1px solid rgba(255, 255, 255, 0.1);
}

.toolbar-group:last-child {
  border-right: none;
}

.toolbar-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 6px 8px;
  border-radius: 6px;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: rgba(255, 255, 255, 0.7);
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
  min-width: 32px;
  height: 32px;
}

.toolbar-btn:hover {
  background: rgba(59, 130, 246, 0.2);
  border-color: rgba(59, 130, 246, 0.4);
  color: #ffffff;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px 0 rgba(31, 38, 135, 0.15);
}

.toolbar-btn.is-active {
  background: rgba(59, 130, 246, 0.3);
  border-color: rgba(59, 130, 246, 0.5);
  color: #60a5fa;
}

/* Editor Content Area */
.tiptap-content {
  flex: 1;
  background: rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(20px) saturate(180%);
  min-height: 350px;
}

/* Tiptap Editor Prose Styling */
:deep(.tiptap-content .ProseMirror) {
  outline: none;
  padding: 20px;
  color: #ffffff;
  font-size: 15px;
  line-height: 1.7;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  min-height: 330px;
  background: transparent;
}

/* Placeholder */
:deep(.tiptap-content .ProseMirror p.is-editor-empty:first-child::before) {
  content: attr(data-placeholder);
  float: left;
  color: #9ca3af;
  pointer-events: none;
  height: 0;
}

/* Typography Styles */
:deep(.tiptap-content .ProseMirror h1) {
  font-size: 2.25rem;
  font-weight: 700;
  color: #ffffff;
  margin: 2rem 0 1rem 0;
  border-bottom: 2px solid rgba(59, 130, 246, 0.3);
  padding-bottom: 0.5rem;
}

:deep(.tiptap-content .ProseMirror h2) {
  font-size: 1.875rem;
  font-weight: 600;
  color: #ffffff;
  margin: 1.75rem 0 0.75rem 0;
  position: relative;
}

:deep(.tiptap-content .ProseMirror h2::before) {
  content: '';
  position: absolute;
  left: -1rem;
  top: 50%;
  transform: translateY(-50%);
  width: 4px;
  height: 1.5rem;
  background: linear-gradient(to bottom, #3b82f6, #8b5cf6);
  border-radius: 2px;
}

:deep(.tiptap-content .ProseMirror h3) {
  font-size: 1.5rem;
  font-weight: 600;
  color: #e5e7eb;
  margin: 1.5rem 0 0.5rem 0;
}

:deep(.tiptap-content .ProseMirror h4) {
  font-size: 1.25rem;
  font-weight: 500;
  color: #d1d5db;
  margin: 1.25rem 0 0.5rem 0;
}

:deep(.tiptap-content .ProseMirror p) {
  color: rgba(255, 255, 255, 0.9);
  margin: 1rem 0;
  line-height: 1.8;
}

:deep(.tiptap-content .ProseMirror strong) {
  color: #ffffff;
  font-weight: 600;
}

:deep(.tiptap-content .ProseMirror em) {
  color: #e5e7eb;
  font-style: italic;
}

/* Lists */
:deep(.tiptap-content .ProseMirror ul) {
  margin: 1rem 0;
  padding-left: 1.5rem;
}

:deep(.tiptap-content .ProseMirror ol) {
  margin: 1rem 0;
  padding-left: 1.5rem;
}

:deep(.tiptap-content .ProseMirror li) {
  color: rgba(255, 255, 255, 0.9);
  margin: 0.5rem 0;
}

:deep(.tiptap-content .ProseMirror ul li::marker) {
  color: #3b82f6;
}

:deep(.tiptap-content .ProseMirror ol li::marker) {
  color: #3b82f6;
  font-weight: 600;
}

/* Blockquotes */
:deep(.tiptap-content .ProseMirror blockquote) {
  border-left: 4px solid #3b82f6;
  background: rgba(59, 130, 246, 0.1);
  margin: 1.5rem 0;
  padding: 1.25rem 1.5rem;
  border-radius: 0 0.75rem 0.75rem 0;
  position: relative;
  backdrop-filter: blur(10px);
}

:deep(.tiptap-content .ProseMirror blockquote::before) {
  content: '"';
  position: absolute;
  top: -0.5rem;
  left: 1rem;
  font-size: 3rem;
  color: #3b82f6;
  font-weight: 700;
  line-height: 1;
}

:deep(.tiptap-content .ProseMirror blockquote p) {
  color: #e5e7eb;
  font-style: italic;
  margin: 0;
  font-size: 1.125rem;
}

/* Code */
:deep(.tiptap-content .ProseMirror code) {
  background: rgba(59, 130, 246, 0.2);
  color: #60a5fa;
  padding: 0.25rem 0.5rem;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  font-family: 'JetBrains Mono', 'Fira Code', 'Consolas', monospace;
  border: 1px solid rgba(59, 130, 246, 0.3);
}

:deep(.tiptap-content .ProseMirror pre) {
  background: rgba(0, 0, 0, 0.4);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 0.75rem;
  padding: 1.5rem;
  margin: 1.5rem 0;
  overflow-x: auto;
  backdrop-filter: blur(10px);
}

:deep(.tiptap-content .ProseMirror pre code) {
  background: transparent;
  border: none;
  padding: 0;
  color: #e5e7eb;
  font-family: 'JetBrains Mono', 'Fira Code', 'Consolas', monospace;
}

/* Links */
:deep(.tiptap-content .ProseMirror a) {
  color: #60a5fa;
  text-decoration: none;
  border-bottom: 1px solid rgba(96, 165, 250, 0.3);
  transition: all 0.3s ease;
}

:deep(.tiptap-content .ProseMirror a:hover) {
  color: #93c5fd;
  border-bottom-color: #93c5fd;
}

/* Highlights */
:deep(.tiptap-content .ProseMirror mark) {
  background: rgba(245, 158, 11, 0.3);
  color: #fbbf24;
  padding: 0.125rem 0.25rem;
  border-radius: 0.25rem;
}

/* Selection */
:deep(.tiptap-content .ProseMirror ::selection) {
  background: rgba(59, 130, 246, 0.3);
}

/* Focus */
:deep(.tiptap-content .ProseMirror:focus) {
  outline: none;
}

/* Scrollbar */
:deep(.tiptap-content .ProseMirror::-webkit-scrollbar) {
  width: 8px;
}

:deep(.tiptap-content .ProseMirror::-webkit-scrollbar-track) {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 4px;
}

:deep(.tiptap-content .ProseMirror::-webkit-scrollbar-thumb) {
  background: rgba(255, 255, 255, 0.3);
  border-radius: 4px;
}

:deep(.tiptap-content .ProseMirror::-webkit-scrollbar-thumb:hover) {
  background: rgba(255, 255, 255, 0.5);
}

/* Tables (if used) */
:deep(.tiptap-content .ProseMirror table) {
  border-collapse: collapse;
  margin: 1rem 0;
  overflow: hidden;
  table-layout: fixed;
  width: 100%;
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 0.5rem;
}

:deep(.tiptap-content .ProseMirror table td),
:deep(.tiptap-content .ProseMirror table th) {
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-sizing: border-box;
  min-width: 1em;
  padding: 0.5rem 0.75rem;
  position: relative;
  vertical-align: top;
  color: rgba(255, 255, 255, 0.9);
}

:deep(.tiptap-content .ProseMirror table th) {
  background: rgba(59, 130, 246, 0.2);
  color: #ffffff;
  font-weight: 600;
  text-align: left;
}

:deep(.tiptap-content .ProseMirror table tr:hover) {
  background: rgba(255, 255, 255, 0.05);
}
</style>