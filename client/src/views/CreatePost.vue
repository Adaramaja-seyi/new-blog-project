<template>
  <div class="create-post">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2>{{ isEditing ? 'Edit Post' : 'Create New Post' }}</h2>
        <p class="text-muted mb-0">{{ isEditing ? 'Update your blog post' : 'Write and publish your next blog post' }}</p>
      </div>
      <div class="d-flex gap-2">
        <button @click="saveDraft" class="btn btn-outline-secondary">
          <i class="bi bi-save me-2"></i>
          Save Draft
        </button>
        <button @click="publishPost" class="btn btn-primary">
          <i class="bi bi-send me-2"></i>
          {{ isEditing ? 'Update Post' : 'Publish Post' }}
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
              <small class="text-muted">A short summary that will appear in post previews</small>
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
              <small class="text-muted">Use markdown formatting for better content structure</small>
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
                v-model="post.category"
                :class="{ 'is-invalid': errors.category }"
              >
                <option value="">Select a category</option>
                <option value="Technology">Technology</option>
                <option value="Development">Development</option>
                <option value="Design">Design</option>
                <option value="Business">Business</option>
                <option value="Lifestyle">Lifestyle</option>
              </select>
              <div v-if="errors.category" class="invalid-feedback">
                {{ errors.category }}
              </div>
            </div>

            <!-- Tags -->
            <div class="mb-3">
              <label class="form-label">Tags</label>
              <input 
                type="text" 
                class="form-control"
                v-model="tagInput"
                placeholder="Add tags separated by commas..."
                @keydown.enter.prevent="addTag"
              />
              <div class="mt-2">
                <span 
                  v-for="tag in post.tags" 
                  :key="tag"
                  class="badge bg-light text-dark me-1 mb-1"
                >
                  {{ tag }}
                  <i 
                    class="bi bi-x ms-1" 
                    @click="removeTag(tag)"
                    style="cursor: pointer;"
                  ></i>
                </span>
              </div>
            </div>

            <!-- Featured Image -->
            <div class="mb-3">
              <label class="form-label">Featured Image</label>
              <div class="featured-image-upload">
                <div 
                  v-if="post.featured_image"
                  class="image-preview"
                >
                  <img :src="post.featured_image" alt="Featured" class="img-fluid rounded" />
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
                  <button @click="selectImage" class="btn btn-outline-primary btn-sm">
                    Choose Image
                  </button>
                </div>
                <input 
                  type="file" 
                  ref="imageInput"
                  accept="image/*"
                  @change="handleImageUpload"
                  style="display: none;"
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
              <h4>{{ post.title || 'Post Title' }}</h4>
              <p class="text-muted">{{ post.excerpt || 'Post excerpt will appear here...' }}</p>
              <div class="preview-meta">
                <span class="badge bg-light text-dark me-2">{{ post.category || 'Category' }}</span>
                <small class="text-muted">{{ new Date().toLocaleDateString() }}</small>
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
  name: 'CreatePost',
  data() {
    return {
      post: {
        title: '',
        excerpt: '',
        content: '',
        category: '',
        tags: [],
        featured_image: '',
        status: 'draft',
        seo_title: '',
        meta_description: ''
      },
      tagInput: '',
      errors: {},
      isEditing: false,
      postId: null
    }
  },
  mounted() {
    // Check if we're editing an existing post
    if (this.$route.params.id) {
      this.isEditing = true
      this.postId = this.$route.params.id
      this.loadPost()
    }
  },
  methods: {
    async loadPost() {
      try {
        // Mock data for editing
        this.post = {
          title: 'Getting Started with Vue 3',
          excerpt: 'Learn the basics of Vue 3 and its new features...',
          content: '# Getting Started with Vue 3\n\nVue 3 introduces many new features and improvements...',
          category: 'Development',
          tags: ['vue', 'javascript', 'frontend'],
          featured_image: '/placeholder-image.jpg',
          status: 'draft',
          seo_title: 'Vue 3 Tutorial - Getting Started Guide',
          meta_description: 'Learn how to get started with Vue 3, the latest version of the popular JavaScript framework.'
        }
      } catch (error) {
        console.error('Error loading post:', error)
      }
    },
    validateForm() {
      this.errors = {}
      
      if (!this.post.title.trim()) {
        this.errors.title = 'Title is required'
      }
      
      if (!this.post.content.trim()) {
        this.errors.content = 'Content is required'
      }
      
      if (!this.post.category) {
        this.errors.category = 'Category is required'
      }
      
      return Object.keys(this.errors).length === 0
    },
    async saveDraft() {
      this.post.status = 'draft'
      await this.savePost()
    },
    async publishPost() {
      if (!this.validateForm()) {
        return
      }
      
      this.post.status = 'published'
      await this.savePost()
    },
    async savePost() {
      try {
        // Mock API call
        console.log('Saving post:', this.post)
        
        // Simulate API delay
        await new Promise(resolve => setTimeout(resolve, 1000))
        
        // Show success message
        alert(this.isEditing ? 'Post updated successfully!' : 'Post created successfully!')
        
        // Redirect to posts list
        this.$router.push('/dashboard/posts')
      } catch (error) {
        console.error('Error saving post:', error)
        alert('Error saving post. Please try again.')
      }
    },
    addTag() {
      const tag = this.tagInput.trim()
      if (tag && !this.post.tags.includes(tag)) {
        this.post.tags.push(tag)
        this.tagInput = ''
      }
    },
    removeTag(tag) {
      this.post.tags = this.post.tags.filter(t => t !== tag)
    },
    selectImage() {
      this.$refs.imageInput.click()
    },
    handleImageUpload(event) {
      const file = event.target.files[0]
      if (file) {
        // Create a preview URL
        this.post.featured_image = URL.createObjectURL(file)
      }
    },
    removeFeaturedImage() {
      this.post.featured_image = ''
      this.$refs.imageInput.value = ''
    }
  },
  watch: {
    'post.title'(newTitle) {
      // Auto-generate SEO title if empty
      if (!this.post.seo_title && newTitle) {
        this.post.seo_title = newTitle
      }
    },
    'post.excerpt'(newExcerpt) {
      // Auto-generate meta description if empty
      if (!this.post.meta_description && newExcerpt) {
        this.post.meta_description = newExcerpt
      }
    }
  }
}
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
