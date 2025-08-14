<template>
  <div class="card mb-4">
    <div class="card-header">
      <h5 class="mb-0">Post Settings</h5>
    </div>
    <div class="card-body">
      <!-- Category -->
      <div class="mb-3">
        <label class="form-label">Category *</label>
        <select
          class="form-select"
          v-model="post.category_id"
          :class="{ 'is-invalid': errors.category_id }"
        >
          <option value="">Select a category</option>
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
        <div v-if="errors.category_id" class="invalid-feedback">
          {{ errors.category_id }}
        </div>
      </div>
      <!-- Tags -->
      <div class="mb-3">
        <label class="form-label">Tags</label>
        <div class="tag-input-container">
          <input
            type="text"
            class="form-control"
            :value="localTagInput"
            placeholder="Type to search tags..."
            @input="onTagInput"
            @keydown.enter.prevent="$emit('add-tag-from-input')"
            @keydown="$emit('handle-tag-input-keydown', $event)"
          />
          <div v-if="tagSuggestions.length > 0" class="tag-suggestions">
            <div
              v-for="tag in tagSuggestions"
              :key="tag.id"
              class="tag-suggestion"
              @click="$emit('add-tag', tag)"
            >
              {{ tag.name }}
            </div>
          </div>
        </div>
        <div class="mt-2">
          <span
            v-for="tag in selectedTags"
            :key="tag.id"
            class="badge me-1 mb-1"
            :style="{ backgroundColor: tag.color, color: 'white' }"
          >
            {{ tag.name }}
            <i
              class="bi bi-x ms-1"
              @click="$emit('remove-tag', tag.id)"
              style="cursor: pointer"
            ></i>
          </span>
        </div>
        <small class="text-muted"
          >Press Enter or comma to add tags. Search existing tags or create new
          ones.</small
        >
      </div>
      <!-- Featured Image -->
      <div class="mb-3">
        <label class="form-label">Featured Image</label>
        <div class="featured-image-upload">
          <div v-if="post.featured_image" class="image-preview">
            <img :src="post.featured_image" alt="Featured" class="img-fluid rounded" />
            <button
              @click="removeImage"
              type="button"
              class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1"
            >
              <i class="bi bi-x"></i>
            </button>
          </div>
          <div v-else class="upload-placeholder">
            <i class="bi bi-image fs-1 text-muted"></i>
            <p class="text-muted mb-2">No image selected</p>
            <button @click="selectImage" type="button" class="btn btn-outline-primary btn-sm">
              Choose Image
            </button>
          </div>
          <input
            ref="imageInput"
            type="file"
            accept="image/*"
            @change="handleImageUpload"
            style="display: none"
          />
        </div>
      </div>
      <!-- Status -->
      <div class="mb-3">
        <label class="form-label">Status</label>
        <select class="form-select" v-model="post.status">
          <option value="draft">Draft</option>
          <option value="pending">Pending Review</option>
          <option value="published">Published</option>
        </select>
      </div>
      <!-- SEO Settings -->
      <div class="mb-3">
        <label class="form-label">SEO Title</label>
        <input
          type="text"
          class="form-control"
          v-model="post.seo_title"
          placeholder="SEO optimized title..."
        />
      </div>
      <div class="mb-3">
        <label class="form-label">Meta Description</label>
        <textarea
          class="form-control"
          v-model="post.meta_description"
          rows="3"
          placeholder="Brief description for search engines..."
        ></textarea>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "PostSettingsSidebar",
  props: {
    post: { type: Object, required: true },
    errors: { type: Object, default: () => ({}) },
    categories: { type: Array, default: () => [] },
    tagInput: { type: String, default: "" },
    tagSuggestions: { type: Array, default: () => [] },
    selectedTags: { type: Array, default: () => [] },
  },
  emits: [
    "search-tags",
    "add-tag-from-input",
    "handle-tag-input-keydown",
    "add-tag",
    "remove-tag",
    "select-image",
    "handle-image-upload",
    "remove-featured-image",
  ],
  data() {
    return {
      localTagInput: this.tagInput,
    };
  },
  watch: {
    tagInput(newVal) {
      this.localTagInput = newVal;
    },
  },
  methods: {
    onTagInput(event) {
      this.localTagInput = event.target.value;
      this.$emit("search-tags", this.localTagInput);
    },
    selectImage() {
      this.$refs.imageInput.click();
    },
    handleImageUpload(event) {
      this.$emit('handle-image-upload', event);
    },
    removeImage() {
      this.$refs.imageInput.value = "";
      this.$emit('remove-featured-image');
    },
  },
};
</script>

<style scoped lang="scss">
.featured-image-upload {
  .image-preview {
    position: relative;
    img {
      width: 100%;
      height: 200px;
      object-fit: cover;
    }
  }
  .upload-placeholder {
    border: 2px dashed var(--border-color);
    border-radius: var(--border-radius);
    padding: 2rem;
    text-align: center;
    &:hover {
      border-color: var(--primary-blue);
    }
  }
}
</style>
