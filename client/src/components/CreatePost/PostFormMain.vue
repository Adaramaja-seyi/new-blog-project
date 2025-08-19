<template>
  <div class="card mb-4">
    <div class="card-body">
      <div class="row">
        <!-- Main Input Fields (Title, Excerpt, Content) -->
        <div class="col-lg-8">
          <!-- Title -->
          <div class="mb-4">
            <label class="form-label">Post Title *</label>
            <input
              type="text"
              class="form-control form-control-lg"
              v-model="post.title"
              placeholder="Enter your post title..."
              :class="{ 'is-invalid': errors.title }"
            />
            <div v-if="errors.title" class="invalid-feedback">
              {{ errors.title }}
            </div>
          </div>
          <!-- Excerpt -->
          <div class="mb-4">
            <label class="form-label">Excerpt</label>
            <textarea
              class="form-control"
              v-model="post.excerpt"
              rows="3"
              placeholder="Brief description of your post..."
            ></textarea>
            <small class="text-muted"
              >A short summary that will appear in post previews</small
            >
          </div>
          <!-- Content -->
          <div class="mb-4">
            <label class="form-label">Content *</label>
            <textarea
              class="form-control"
              v-model="post.content"
              rows="15"
              placeholder="Write your post content here..."
              :class="{ 'is-invalid': errors.content }"
            ></textarea>
            <div v-if="errors.content" class="invalid-feedback">
              {{ errors.content }}
            </div>
            <small class="text-muted"
              >Use markdown formatting for better content structure</small
            >
          </div>
        </div>
        <!-- Post Settings -->
        <div class="col-lg-4">
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
                  <option
                    v-for="category in categories"
                    :key="category.id"
                    :value="category.id"
                  >
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

                <select
                  class="form-select"
                  v-model="post.tag_id"
                  :class="{ 'is-invalid': errors.tag_id }"
                >
                  <option value="">Select a tag</option>
                  <option v-for="tag in tags" :key="tag.id" :value="tag.id">
                    {{ tag.name }}
                  </option>
                </select>
                <div v-if="errors.tag_id" class="invalid-feedback">
                  {{ errors.tag_id }}
                </div>
              </div>
              <!-- Featured Image -->
              <div class="mb-3">
                <label class="form-label">Featured Image</label>
                <div class="featured-image-upload">
                  <div v-if="post.featured_image" class="image-preview">
                    <img
                      :src="post.featured_image"
                      alt="Featured"
                      class="img-fluid rounded"
                    />
                    <button
                      @click="removeFeaturedImage"
                      type="button"
                      class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1"
                    >
                      <i class="bi bi-x"></i>
                    </button>
                  </div>
                  <div v-else class="upload-placeholder">
                    <i class="bi bi-image fs-1 text-muted"></i>
                    <p class="text-muted mb-2">No image selected</p>
                    <button
                      @click="selectImage"
                      type="button"
                      class="btn btn-outline-primary btn-sm"
                    >
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
                <select
                  class="form-select"
                  v-model="post.status"
                  :class="{ 'is-invalid': errors.status }"
                >
                  <option value="" disabled>Select status</option>

                  <option value="draft">Draft</option>
                  <option value="published">Published</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "PostFormMain",
  props: {
    post: { type: Object, required: true },
    errors: { type: Object, default: () => ({}) },
    categories: { type: Array, default: () => [] },
    tags: { type: Array, default: () => [] },
  },
  emits: ["handle-image-upload", "remove-featured-image"],
  data() {
    return {};
  },

  methods: {
    getImageUrl(image) {
      if (!image) return "";
      if (typeof image === "string") {
        return `http://localhost:8000${image}`;
      }
      if (image instanceof File || image instanceof Blob) {
        return URL.createObjectURL(image);
      }
      return "";
    },
    onTagInput(event) {
      this.localTagInput = event.target.value;
      this.$emit("search-tags", this.localTagInput);
    },
    selectImage() {
      this.$refs.imageInput.click();
    },
    handleImageUpload(event) {
      this.$emit("handle-image-upload", event);
    },
    removeFeaturedImage() {
      this.$emit("remove-featured-image");
    },
  },
};
</script>

<style scoped lang="scss">
.form-control-lg {
  font-size: 1.25rem;
  font-weight: 600;
}

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
    border: 2px dashed var(--border-color, #dee2e6);
    border-radius: var(--border-radius, 0.25rem);
    padding: 2rem;
    text-align: center;
    &:hover {
      border-color: var(--primary-blue, #007bff);
    }
  }
}

.tag-input-container {
  position: relative;
}

.tag-suggestions {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: white;
  border: 1px solid var(--border-color, #dee2e6);
  border-radius: var(--border-radius, 0.25rem);
  z-index: 1000;
  max-height: 200px;
  overflow-y: auto;
}

.tag-suggestion {
  padding: 0.5rem 1rem;
  cursor: pointer;
  &:hover {
    background-color: var(--primary-blue, #007bff);
    color: white;
  }
}

.badge {
  font-size: 0.75rem;
  font-weight: 500;
}
</style>
