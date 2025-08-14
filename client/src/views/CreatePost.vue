<template>
  <div class="create-post">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2>{{ isEditing ? "Edit Post" : "Create New Post" }}</h2>
        <p class="text-muted mb-0">
          {{
            isEditing
              ? "Update your blog post"
              : "Write and publish your next blog post"
          }}
        </p>
      </div>
      <div class="d-flex gap-2">
        <button @click="saveDraft" class="btn btn-outline-secondary">
          <i class="bi bi-save me-2"></i>
          Save Draft
        </button>
        <button @click="publishPost" class="btn btn-primary">
          <i class="bi bi-send me-2"></i>
          {{ isEditing ? "Update Post" : "Publish Post" }}
        </button>
      </div>
    </div>

    <!-- Post Form -->
    <div class="row">
      <!-- Main Content -->
      <div class="col-lg-8">
        <div class="card mb-4">
          <div class="card-body">
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
        </div>
      </div>

      <!-- Sidebar -->
      <div class="col-lg-4">
        <!-- Post Settings -->
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
              <div class="tag-input-container">
                <input
                  type="text"
                  class="form-control"
                  v-model="tagInput"
                  placeholder="Type to search tags..."
                  @input="searchTags"
                  @keydown.enter.prevent="addTagFromInput"
                  @keydown="handleTagInputKeydown"
                />
                <!-- Tag suggestions -->
                <div v-if="tagSuggestions.length > 0" class="tag-suggestions">
                  <div
                    v-for="tag in tagSuggestions"
                    :key="tag.id"
                    class="tag-suggestion"
                    @click="addTag(tag)"
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
                    @click="removeTag(tag.id)"
                    style="cursor: pointer"
                  ></i>
                </span>
              </div>
              <small class="text-muted"
                >Press Enter or comma to add tags. Search existing tags or
                create new ones.</small
              >
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
                    class="btn btn-outline-primary btn-sm"
                  >
                    Choose Image
                  </button>
                </div>
                <input
                  type="file"
                  ref="imageInput"
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

        <!-- Post Preview -->
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">Preview</h5>
          </div>
          <div class="card-body">
            <div class="preview-content">
              <h4>{{ post.title || "Post Title" }}</h4>
              <p class="text-muted">
                {{ post.excerpt || "Post excerpt will appear here..." }}
              </p>
              <div class="preview-meta">
                <span class="badge bg-light text-dark me-2">{{
                  post.category || "Category"
                }}</span>
                <small class="text-muted">{{
                  new Date().toLocaleDateString()
                }}</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { blogAPI } from "../api.js";

export default {
  name: "CreatePost",
  data() {
    return {
      post: {
        title: "",
        excerpt: "",
        content: "",
        category_id: "",
        featured_image: "",
        status: "draft",
        meta_title: "",
        meta_description: "",
      },
      selectedTags: [],
      tagInput: "",
      tagSuggestions: [],
      categories: [],
      errors: {},
      isEditing: false,
      postId: null,
      loading: false,
    };
  },
  mounted() {
    // Check if we're editing an existing post
    if (this.$route.params.id) {
      this.isEditing = true;
      this.postId = this.$route.params.id;
      this.loadPost();
    }

    // Load categories
    this.loadCategories();
  },
  methods: {
    async loadCategories() {
      try {
        const response = await blogAPI.getCategories();
        this.categories =
          response.data.data && response.data.data.length > 0
            ? response.data.data
            : [
                { id: 1, name: "Technology" },
                { id: 2, name: "Lifestyle" },
                { id: 3, name: "Travel" },
                { id: 4, name: "Food" },
                { id: 5, name: "Health" },
                { id: 6, name: "Education" },
                { id: 7, name: "Business" },
                { id: 8, name: "Entertainment" },
                { id: 9, name: "Sports" },
                { id: 10, name: "Fashion" },
              ];
      } catch (error) {
        console.error("Error loading categories:", error);
        this.categories = [
          { id: 1, name: "Technology" },
          { id: 2, name: "Lifestyle" },
          { id: 3, name: "Travel" },
        ];
      }
    },

    async searchTags() {
      if (this.tagInput.length > 1) {
        try {
          const response = await blogAPI.searchTags(this.tagInput);
          this.tagSuggestions = (response.data || []).filter(
            (tag) =>
              !this.selectedTags.find((selected) => selected.id === tag.id)
          );
        } catch (error) {
          console.error("Error searching tags:", error);
          this.tagSuggestions = [];
        }
      } else {
        this.tagSuggestions = [];
      }
    },

    addTag(tag) {
      if (!this.selectedTags.find((selected) => selected.id === tag.id)) {
        this.selectedTags.push(tag);
      }
      this.tagInput = "";
      this.tagSuggestions = [];
    },

    async addTagFromInput() {
      if (this.tagInput.trim()) {
        // Check if tag already exists in suggestions
        const existingTag = this.tagSuggestions.find(
          (tag) => tag.name.toLowerCase() === this.tagInput.trim().toLowerCase()
        );

        if (existingTag) {
          this.addTag(existingTag);
        } else {
          // Create new tag
          try {
            const response = await blogAPI.createTag({
              name: this.tagInput.trim(),
            });
            if (response.data) {
              this.addTag(response.data);
            }
          } catch (error) {
            console.error("Error creating tag:", error);
            // Add as a temporary tag for now
            const tempTag = {
              id: Date.now(),
              name: this.tagInput.trim(),
              color: "#6c757d",
            };
            this.addTag(tempTag);
          }
        }
      }
    },

    handleTagInputKeydown(event) {
      if (event.key === ",") {
        event.preventDefault();
        this.addTagFromInput();
      }
    },

    removeTag(tagId) {
      this.selectedTags = this.selectedTags.filter((tag) => tag.id !== tagId);
    },

    async loadPost() {
      try {
        const response = await blogAPI.getPost(this.postId);
        if (response.data) {
          const postData = response.data;
          this.post = {
            title: postData.title || "",
            excerpt: postData.excerpt || "",
            content: postData.content || "",
            category_id: postData.category_id || "",
            featured_image: postData.featured_image || "",
            status: postData.status || "draft",
            meta_title: postData.meta_title || "",
            meta_description: postData.meta_description || "",
          };
          this.selectedTags = postData.tags || [];
        }
      } catch (error) {
        console.error("Error loading post:", error);
        alert("Failed to load post. Please try again.");
      }
    },
    validateForm() {
      this.errors = {};

      if (!this.post.title.trim()) {
        this.errors.title = "Title is required";
      }

      if (!this.post.content.trim()) {
        this.errors.content = "Content is required";
      }

      if (!this.post.category_id) {
        this.errors.category_id = "Category is required";
      }

      return Object.keys(this.errors).length === 0;
    },
    async saveDraft() {
      this.post.status = "draft";
      await this.savePost();
    },
    async publishPost() {
      if (!this.validateForm()) {
        return;
      }

      this.post.status = "published";
      await this.savePost();
    },
    async savePost() {
      try {
        this.post.tags = this.selectedTags;
        let response;
        if (this.isEditing) {
          response = await blogAPI.updatePost(this.postId, this.post);
        } else {
          response = await blogAPI.createPost(this.post);
        }
        if (response.data && response.data.success) {
          alert(
            this.isEditing
              ? "Post updated successfully!"
              : "Post created successfully!"
          );
          this.$router.push("/dashboard/posts");
        } else {
          alert("Error saving post. Please try again.");
        }
      } catch (error) {
        console.error("Error saving post:", error);
        alert("Error saving post. Please try again.");
      }
    },
    selectImage() {
      this.$refs.imageInput.click();
    },
    handleImageUpload(event) {
      const file = event.target.files[0];
      if (file) {
        // Create a preview URL
        this.post.featured_image = URL.createObjectURL(file);
      }
    },
    removeFeaturedImage() {
      this.post.featured_image = "";
      this.$refs.imageInput.value = "";
    },
  },
  watch: {
    "post.title"(newTitle) {
      // Auto-generate SEO title if empty
      if (!this.post.seo_title && newTitle) {
        this.post.seo_title = newTitle;
      }
    },
    "post.excerpt"(newExcerpt) {
      // Auto-generate meta description if empty
      if (!this.post.meta_description && newExcerpt) {
        this.post.meta_description = newExcerpt;
      }
    },
  },
};
</script>

<style scoped lang="scss">
.create-post {
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
      border: 2px dashed var(--border-color);
      border-radius: var(--border-radius);
      padding: 2rem;
      text-align: center;

      &:hover {
        border-color: var(--primary-blue);
      }
    }
  }

  .preview-content {
    h4 {
      font-size: 1.1rem;
      margin-bottom: 0.5rem;
    }

    .preview-meta {
      margin-top: 1rem;
    }
  }

  .badge {
    font-size: 0.75rem;
    font-weight: 500;
  }
}

// Responsive adjustments
@media (max-width: 768px) {
  .create-post {
    .d-flex.justify-content-between {
      flex-direction: column;
      gap: 1rem;
      align-items: stretch;

      .btn {
        width: 100%;
      }
    }
  }
}
</style>
