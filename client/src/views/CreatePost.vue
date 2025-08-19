<template>
  <div class="create-post">
    <CreatePostHeader
      :isEditing="isEditing"
      :saving="loading"
      @save-draft="saveDraft"
      @publish-post="publishPost"
    />
    <div class="row">
      <div class="col-lg-12">
        <PostFormMain
          :post="post"
          :errors="errors"
          :categories="categories"
          :tags="tags"
          @handle-image-upload="handleImageUpload"
          @remove-featured-image="removeFeaturedImage"
        />
      </div>
    </div>
  </div>
</template>

<script>
import CreatePostHeader from "../components/CreatePostHeader.vue";
import PostFormMain from "../components/CreatePost/PostFormMain.vue";
import { blogAPI } from "../api.js";
import { useToast } from "vue-toastification";

export default {
  setup() {
    const toast = useToast();
    return { toast };
  },
  name: "CreatePost",
  components: {
    CreatePostHeader,
    PostFormMain,
  },
  data() {
    return {
      post: {
        title: "",
        excerpt: "",
        content: "",
        category_id: "",
        tag_id: "",
        featured_image: "",
        status: "",
      },
      tags: [],
      categories: [],
      errors: {},
      isEditing: false,
      postSlug: null,
      loading: false,
      selectedImageFile: null,
    };
  },
  async mounted() {
    try {
      if (this.$route.query.edit) {
        this.isEditing = true;
        this.postSlug = this.$route.query.edit;
        await this.loadPost();
      } else if (this.$route.params.slug) {
        this.isEditing = true;
        this.postSlug = this.$route.params.slug;
        await this.loadPost();
      }
      await Promise.all([this.loadCategories(), this.loadTags()]);
    } catch (error) {
      console.error("Error initializing CreatePost:", error);
    }
  },
  methods: {
    async loadCategories() {
      try {
        const response = await blogAPI.getCategories();
        this.categories = response.data?.data || [];
      } catch (error) {
        console.error("Error loading categories:", error);
      }
    },
    async loadTags() {
      try {
        const response = await blogAPI.getTags();
        this.tags = response.data?.data || [];
      } catch (error) {
        console.error("Error loading tags:", error);
      }
    },
    async loadPost() {
      try {
        this.loading = true;
        const response = await blogAPI.getPost(this.postSlug);
        if (response.data?.success) {
          const postData = response.data.data;
          this.post = {
            title: postData.title || "",
            excerpt: postData.excerpt || "",
            content: postData.content || "",
            category_id: postData.category_id || "",
            tag_id: postData.tag_id || "",
            featured_image: postData.featured_image || "",
            status: postData.status || "draft",
          };
          this.selectedImageFile = null;
        } else {
          throw new Error("Invalid response data");
        }
      } catch (error) {
        console.error("Error loading post:", error);
        this.toast.error("Failed to load post. Please try again.");
      } finally {
        this.loading = false;
      }
    },
    validateForm() {
      this.errors = {};
      if (!this.post.title.trim()) this.errors.title = "Title is required";
      if (!this.post.content.trim())
        this.errors.content = "Content is required";
      if (!this.post.category_id)
        this.errors.category_id = "Category is required";
      return Object.keys(this.errors).length === 0;
    },
    async saveDraft() {
      this.post.status = "draft";
      await this.savePost();
    },
    async publishPost() {
      if (!this.validateForm()) {
        this.toast.error("Please fill in all required fields.");
        return;
      }

      this.post.status =
        this.post.status === "published" ? "published" : "draft";
      await this.savePost();
    },
    async savePost() {
      try {
        this.loading = true;
        const formData = new FormData();
        formData.append("title", this.post.title || "");
        formData.append("content", this.post.content || "");
        formData.append("excerpt", this.post.excerpt || "");
        if (this.post.category_id)
          formData.append("category_id", this.post.category_id);
        if (this.post.tag_id && !isNaN(this.post.tag_id))
          formData.append("tag_id", this.post.tag_id);
        formData.append("status", this.post.status || "draft");
        formData.append(
          "is_published",
          this.post.status === "published" ? "yes" : "no"
        );
        if (this.selectedImageFile instanceof File) {
          formData.append("featured_image", this.selectedImageFile);
        }

        let response;
        if (this.isEditing) {
          formData.append("_method", "PUT");
          response = await blogAPI.updatePost(this.postSlug, formData);
        } else {
          response = await blogAPI.createPost(formData);
        }

        if (response.data?.success) {
          const message = this.isEditing
            ? "Post updated successfully!"
            : this.post.status === "draft"
            ? "Draft saved successfully!"
            : "Post published successfully!";
          this.toast.success(message);
          this.$router.push(
            this.post.status === "draft"
              ? "/dashboard/drafts"
              : "/dashboard/posts"
          );
        } else {
          throw new Error("Invalid response data");
        }
      } catch (error) {
        console.error("Error saving post:", error);
        if (error.response?.data?.errors) {
          this.errors = error.response.data.errors;
          alert(
            "Validation errors:\n" +
              Object.values(this.errors).flat().join("\n")
          );
        } else if (error.response?.data?.message) {
          this.toast.error("Error: " + error.response.data.message);
        } else {
          this.toast.error("Error saving post. Please try again.");
        }
      } finally {
        this.loading = false;
      }
    },
    handleImageUpload(event) {
      const file = event.target.files?.[0];
      if (file) {
        const allowedTypes = [
          "image/jpeg",
          "image/jpg",
          "image/png",
          "image/gif",
          "image/webp",
        ];
        if (!allowedTypes.includes(file.type)) {
          this.toast.info(
            "Please select a valid image file (JPEG, PNG, GIF, or WebP)"
          );
          return;
        }
        if (file.size > 2 * 1024 * 1024) {
          this.toast.info("Image size must be less than 2MB");
          return;
        }
        this.selectedImageFile = file;
        this.post.featured_image = URL.createObjectURL(file);
      }
    },
    removeFeaturedImage() {
      this.post.featured_image = "";
      this.selectedImageFile = null;
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
}

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
