
<template>
  <div class="create-post">
    <CreatePostHeader
      :isEditing="isEditing"
      @save-draft="saveDraft"
      @publish-post="publishPost"
    />
    <div class="row">
      <div class="col-lg-8">
        <PostFormMain :post="post" :errors="errors" />
      </div>
      <div class="col-lg-4">
        <PostSettingsSidebar
          :post="post"
          :errors="errors"
          :categories="categories"
          :tag-input="tagInput"
          :tag-suggestions="tagSuggestions"
          :selected-tags="selectedTags"
          @search-tags="searchTags"
          @add-tag-from-input="addTagFromInput"
          @handle-tag-input-keydown="handleTagInputKeydown"
          @add-tag="addTag"
          @remove-tag="removeTag"
          @handle-image-upload="handleImageUpload"
          @remove-featured-image="removeFeaturedImage"
        />
        <PostPreview :post="post" />
      </div>
    </div>
  </div>
</template>

<script>
import CreatePostHeader from "../components/CreatePostHeader.vue";
import PostFormMain from "../components/CreatePost/PostFormMain.vue";
import PostSettingsSidebar from "../components/CreatePost/PostSettingsSidebar.vue";
import PostPreview from "../components/CreatePost/PostPreview.vue";
import { blogAPI } from "../api.js";
import api from "../api.js";

export default {
  name: "CreatePost",
  components: {
    CreatePostHeader,
    PostFormMain,
    PostSettingsSidebar,
    PostPreview,
  },
  data() {
    return {
      post: {
        title: "",
        excerpt: "",
        content: "",
        category_id: "",
        featured_image: "",
        status: "draft",
        seo_title: "",
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
      selectedImageFile: null, // Store the actual file
    };
  },
  mounted() {
    if (this.$route.params.id) {
      this.isEditing = true;
      this.postId = this.$route.params.id;
      this.loadPost();
    }
    this.loadCategories();
  },
  methods: {
    async loadCategories() {
      try {
        const response = await blogAPI.getCategories();
        this.categories = response.data.data && response.data.data.length > 0 ? response.data.data : [
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
            (tag) => !this.selectedTags.find((selected) => selected.id === tag.id)
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
        const existingTag = this.tagSuggestions.find(
          (tag) => tag.name.toLowerCase() === this.tagInput.trim().toLowerCase()
        );
        if (existingTag) {
          this.addTag(existingTag);
        } else {
          try {
            const response = await blogAPI.createTag({ name: this.tagInput.trim() });
            if (response.data) {
              this.addTag(response.data);
            }
          } catch (error) {
            console.error("Error creating tag:", error);
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
        if (response.data && response.data.success) {
          const postData = response.data.data;
          this.post = {
            title: postData.title || "",
            excerpt: postData.excerpt || "",
            content: postData.content || "",
            category_id: postData.category_id || "",
            featured_image: postData.featured_image || "",
            status: postData.status || "draft",
            seo_title: postData.seo_title || postData.meta_title || "",
            meta_description: postData.meta_description || "",
          };
          this.selectedTags = postData.tags || [];
          
          // Clear the selected file since we're loading an existing image
          this.selectedImageFile = null;
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
        // Create FormData to handle file upload
        const formData = new FormData();
        
        // Add all post fields - only append if values exist
        if (this.post.title) formData.append('title', this.post.title);
        if (this.post.content) formData.append('content', this.post.content);
        if (this.post.excerpt) formData.append('excerpt', this.post.excerpt);
        if (this.post.category_id) formData.append('category_id', this.post.category_id);
        if (this.post.status) formData.append('status', this.post.status);
        if (this.post.seo_title) formData.append('meta_title', this.post.seo_title);
        if (this.post.meta_description) formData.append('meta_description', this.post.meta_description);
        
        // Always append is_published
        formData.append('is_published', this.post.status === 'published' ? '1' : '0');
        
        // Add image file if selected
        if (this.selectedImageFile && this.selectedImageFile instanceof File) {
          formData.append('featured_image', this.selectedImageFile);
        }
        
        // Add tags as JSON string
        if (this.selectedTags && this.selectedTags.length > 0) {
          formData.append('tags', JSON.stringify(this.selectedTags.map(tag => tag.id)));
        }
        
        let response;
        if (this.isEditing) {
          // Use the special update route that handles file uploads
          response = await api.post(`/posts/${this.postId}/update`, formData);
        } else {
          response = await api.post('/posts', formData);
        }
        
        if (response.data && response.data.success) {
          alert(this.isEditing ? "Post updated successfully!" : "Post created successfully!");
          this.$router.push("/dashboard/posts");
        } else {
          alert("Error saving post. Please try again.");
        }
      } catch (error) {
        console.error("Error saving post:", error);
        
        // Show specific validation errors if available
        if (error.response && error.response.data) {
          if (error.response.data.errors) {
            // Laravel validation errors
            const errorMessages = Object.values(error.response.data.errors).flat();
            alert('Validation errors:\n' + errorMessages.join('\n'));
          } else if (error.response.data.message) {
            alert('Error: ' + error.response.data.message);
          } else {
            alert("Error saving post. Please try again.");
          }
        } else {
          alert("Error saving post. Please try again.");
        }
      }
    },
    handleImageUpload(event) {
      const file = event.target.files && event.target.files[0];
      
      if (file) {
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
          alert('Please select a valid image file (JPEG, PNG, GIF, or WebP)');
          return;
        }
        
        // Validate file size (2MB limit)
        if (file.size > 2 * 1024 * 1024) {
          alert('Image size must be less than 2MB');
          return;
        }
        
        // Store the file for upload
        this.selectedImageFile = file;
        
        // Create preview URL
        this.post.featured_image = URL.createObjectURL(file);
      }
    },
    removeFeaturedImage() {
      this.post.featured_image = "";
      this.selectedImageFile = null;
    },
  },
  watch: {
    "post.title"(newTitle) {
      if (!this.post.seo_title && newTitle) {
        this.post.seo_title = newTitle;
      }
    },
    "post.excerpt"(newExcerpt) {
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
