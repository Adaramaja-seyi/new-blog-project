<template>
  <div class="container">
    <!-- Loading State -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="text-center py-5">
      <h3 class="text-danger mb-3">Post Not Found</h3>
      <p class="text-muted mb-4">The post you're looking for doesn't exist.</p>
      <router-link to="/" class="btn btn-primary">Back to Home</router-link>
    </div>

    <!-- Post Content -->
    <div v-else-if="post" class="row">
      <div class="col-lg-8">
        <!-- Post Header -->
        <header class="my-4">
          <!-- Featured Image -->
          <div v-if="post.featured_image" class="mb-4">
            <img
              :src="getImageUrl(post.featured_image)"
              :alt="post.title || 'Post image'"
              class="img-fluid rounded shadow-sm w-100"
              style="height: 400px; object-fit: cover"
            />
          </div>
          <div
            class="d-flex flex-wrap align-items-center text-muted mb-4 gap-2"
          >
            <span>
              By
              <router-link
                v-if="post.user?.id"
                :to="{ name: 'UserProfile', params: { id: post.user.id } }"
                class="text-decoration-none"
              >
                {{ post.user?.name || post.author }}
              </router-link>
            </span>
            <span class="mx-2">•</span>
            <span>{{ formatDate(post.created_at) }}</span>
            <template v-if="post.category">
              <span class="mx-2">•</span>
              <router-link
                :to="{ name: 'Category', params: { slug: post.category.slug } }"
                class="badge text-white"
                :style="{ backgroundColor: post.category.color || '#6c757d' }"
              >
                {{ post.category.name }}
              </router-link>
            </template>
            <small v-if="post.updated_at" class="ms-3">
              (updated {{ formatDate(post.updated_at) }})
            </small>
          </div>
          <h1 class="display-5 mb-3">{{ post.title }}</h1>
          <p v-if="post.excerpt" class="lead text-muted mb-3">
            {{ post.excerpt }}
          </p>
          <!-- Post Actions -->
          <div class="d-flex align-items-center gap-3 mb-4">
            <span class="text-muted">
              <i class="bi bi-eye me-1"></i>
              {{ post.views_count || 0 }} views
            </span>
            <button
              v-if="isAuthenticated"
              @click="toggleLike"
              class="btn btn-sm"
              :class="[isLiked ? 'btn-danger' : 'btn-outline-danger']"
              :disabled="likingPost"
            >
              <i
                :class="['bi', isLiked ? 'bi-heart-fill' : 'bi-heart', 'me-1']"
              ></i>
              {{ post.likes_count || 0 }}
            </button>
            <router-link
              v-else
              :to="{ path: '/login', query: { redirect: $route.fullPath } }"
              class="btn btn-sm btn-outline-danger"
            >
              <i class="bi bi-heart me-1"></i>
              {{ post.likes_count || 0 }}
            </router-link>
            <span class="text-muted">
              <i class="bi bi-chat me-1"></i>
              {{ post.comments_count || 0 }} comments
            </span>
          </div>
        </header>

        <!-- Post Content -->
        <article class="mb-5" v-html="post.content"></article>

        <!-- Tags -->
        <div class="mb-4">
          <h6 class="mb-2">Tags</h6>
          <div v-if="post.tags?.length" class="d-flex flex-wrap gap-2">
            <router-link
              v-for="tag in post.tags"
              :key="tag.id"
              :to="{ name: 'Tag', params: { slug: tag.slug } }"
              class="badge bg-light text-dark border"
            >
              #{{ tag.name }}
            </router-link>
          </div>
          <p v-else class="text-muted">No tags for this post.</p>
        </div>

        <!-- Comments Section -->
        <section class="comments-section pt-4 border-top">
          <h3 class="mb-4">Comments ({{ comments.length }})</h3>

          <!-- Add Comment Form -->
          <div v-if="isAuthenticated" class="card mb-4">
            <div class="card-body">
              <h5 class="card-title">Add a Comment</h5>
              <form @submit.prevent="submitComment">
                <div class="mb-3">
                  <textarea
                    v-model="newComment"
                    class="form-control"
                    rows="3"
                    placeholder="Write your comment here..."
                    required
                  ></textarea>
                </div>
                <button
                  type="submit"
                  class="btn btn-primary"
                  :disabled="submitting"
                >
                  {{ submitting ? "Posting..." : "Post Comment" }}
                </button>
              </form>
            </div>
          </div>
          <div v-else class="card mb-4">
            <div class="card-body text-center">
              <h5 class="card-title">Join the Conversation</h5>
              <p class="text-muted mb-3">
                Please log in to comment on this post.
              </p>
              <router-link
                :to="{ path: '/login', query: { redirect: $route.fullPath } }"
                class="btn btn-primary me-2"
              >
                Login
              </router-link>
              <router-link
                :to="{
                  path: '/register',
                  query: { redirect: $route.fullPath },
                }"
                class="btn btn-outline-primary"
              >
                Sign Up
              </router-link>
            </div>
          </div>

          <!-- Comments List -->
          <div v-if="comments.length">
            <div
              v-for="comment in comments"
              :key="comment.id"
              class="card mb-3"
            >
              <div class="card-body">
                <div
                  class="d-flex justify-content-between align-items-start mb-2"
                >
                  <h6 class="card-subtitle mb-1">
                    {{
                      comment.author_name || comment.user?.name || "Anonymous"
                    }}
                  </h6>
                  <small class="text-muted">{{
                    formatDate(comment.created_at)
                  }}</small>
                </div>
                <p class="card-text">{{ comment.content }}</p>
                <div v-if="canDeleteComment(comment)" class="text-end">
                  <button
                    @click="deleteComment(comment.id)"
                    class="btn btn-sm btn-outline-danger"
                    :disabled="deleting"
                  >
                    Delete
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="text-center py-4">
            <p class="text-muted">No comments yet. Be the first to comment!</p>
          </div>
        </section>
      </div>

      <!-- Sidebar -->
      <div class="col-lg-4 my-4">
        <div
          class="card mb-4"
          v-if="this.post.user.id === this.currentUser?.id"
        >
          <div class="card-header">
            <h5 class="mb-0">About the Author</h5>
          </div>
          <div class="card-body">
            <h6>{{ post.user?.name || post.author || "Unknown Author" }}</h6>
            <p class="text-muted">
              {{ post.user?.bio || post.author_bio || "No bio available." }}
            </p>
            <router-link
              v-if="post.user?.id"
              :to="{ name: 'UserProfile', params: { id: post.user.id } }"
              class="btn btn-sm btn-outline-primary"
            >
              View Profile
            </router-link>
          </div>
        </div>
        <div class="card my-4">
          <div class="card-header">
            <h5 class="mb-0">Related Posts</h5>
          </div>
          <div class="card-body">
            <ul v-if="relatedPosts.length" class="list-unstyled">
              <li
                v-for="relatedPost in relatedPosts"
                :key="relatedPost.id"
                class="mb-3"
              >
                <router-link
                  :to="{
                    name: 'PostDetail',
                    params: { slug: relatedPost.slug },
                  }"
                  class="text-decoration-none"
                >
                  <h6 class="mb-1">{{ relatedPost.title }}</h6>
                  <small class="text-muted">{{
                    formatDate(relatedPost.created_at)
                  }}</small>
                </router-link>
              </li>
            </ul>
            <p v-else class="text-muted">No related posts available.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { blogAPI } from "../api.js";
import { useAuth } from "../stores/auth.js";

export default {
  name: "PostDetail",
  data() {
    return {
      post: null,
      comments: [],
      relatedPosts: [],
      loading: false,
      error: false,
      newComment: "",
      submitting: false,
      deleting: false,
      likingPost: false,
      isLiked: false,
      baseUrl: import.meta.env.VUE_APP_API_BASE_URL || "http://localhost:8000",
      fallbackImage: "https://via.placeholder.com/800x400?text=No+Image",
    };
  },
  computed: {
    auth() {
      return useAuth();
    },
    isAuthenticated() {
      return this.auth?.isLoggedIn;
    },
    currentUser() {
      return this.auth?.user || null;
    },
  },
  mounted() {
    this.fetchPost().then(() => {
      if (this.post) {
        Promise.all([
          this.fetchComments(),
          this.fetchRelatedPosts(),
          this.checkLikeStatus(),
        ]);
      }
    });
  },
  methods: {
    getImageUrl(path) {
      if (!path) {
        return this.fallbackImage;
      }
      return `${this.baseUrl}${path}`;
    },

    async fetchPost() {
      this.loading = true;
      try {
        const response = await blogAPI.getPost(this.$route.params.slug);
        this.post = response.data.data || response.data;
      } catch (error) {
        console.error("Error fetching post:", error);
        this.error = true;
      } finally {
        this.loading = false;
      }
    },
    async fetchComments() {
      try {
        const response = await blogAPI.getPostComments(this.$route.params.slug);
        this.comments = response.data.data || response.data || [];
      } catch (error) {
        console.error("Error fetching comments:", error);
      }
    },
    async fetchRelatedPosts() {
      try {
        const response = await blogAPI.getRelatedPosts(this.$route.params.slug);
        this.relatedPosts = response.data.data || response.data || [];
      } catch (error) {
        console.error("Error fetching related posts:", error);
      }
    },
    async checkLikeStatus() {
      if (!this.isAuthenticated || !this.post) return;
      try {
        const response = await blogAPI.checkLikeStatus(this.post.id);
        const payload = response.data.data || response.data;
        this.isLiked = !!payload?.is_liked;
        if (typeof payload?.likes_count === "number") {
          this.post.likes_count = payload.likes_count;
        }
      } catch (error) {
        console.error("Error checking like status:", error);
      }
    },
    async toggleLike() {
      if (!this.isAuthenticated || this.likingPost) return;
      try {
        this.likingPost = true;
        const action = this.isLiked ? blogAPI.unlikePost : blogAPI.likePost;
        const response = await action(this.post.id);
        const payload = response.data.data || response.data;
        this.post.likes_count =
          payload?.likes_count ??
          (this.post.likes_count || 0) + (this.isLiked ? -1 : 1);
        this.isLiked = !this.isLiked;
      } catch (error) {
        console.error("Error toggling like:", error);
        alert("Failed to update like status. Please try again.");
      } finally {
        this.likingPost = false;
      }
    },
    async submitComment() {
      if (!this.newComment.trim() || this.submitting) return;
      try {
        this.submitting = true;
        const response = await blogAPI.createComment(this.post.slug, {
          content: this.newComment,
        });
        const comment =
          response.data.comment || response.data.data || response.data;
        if (comment) {
          this.comments.unshift({
            ...comment,
            user: comment.user || {
              id: this.currentUser?.id,
              name: this.currentUser?.name,
            },
          });
          this.post.comments_count = (this.post.comments_count || 0) + 1;
          this.newComment = "";
        }
      } catch (error) {
        console.error("Error posting comment:", error);
        alert("Failed to post comment. Please try again.");
      } finally {
        this.submitting = false;
      }
    },
    async deleteComment(commentId) {
      if (!confirm("Are you sure you want to delete this comment?")) return;
      try {
        this.deleting = true;
        await blogAPI.deleteComment(commentId);
        this.comments = this.comments.filter((c) => c.id !== commentId);
        this.post.comments_count = Math.max(
          0,
          (this.post.comments_count || 0) - 1
        );
      } catch (error) {
        console.error("Error deleting comment:", error);
        alert("Failed to delete comment. Please try again.");
      } finally {
        this.deleting = false;
      }
    },
    canDeleteComment(comment) {
      return (
        this.isAuthenticated &&
        (comment.user_id === this.currentUser?.id ||
          this.post?.user?.id === this.currentUser?.id)
      );
    },
    formatDate(dateString) {
      if (!dateString) return "";
      return new Date(dateString).toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
      });
    },
  },
};
</script>

<style scoped>
.comments-section {
  border-top: 1px solid var(--bs-border-color);
  padding-top: 2rem;
}

.post-content {
  font-size: 1.1rem;
  line-height: 1.8;
}

.post-content :deep(h2) {
  margin: 2rem 0 1rem;
}

.post-content :deep(ul) {
  margin-bottom: 1.5rem;
}
</style>
