<template>
  <div class="container">
    <!-- Loading State -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border" role="status">
        <span
          class="visually- loading: true, error: false, newComment: '', submitting: false, deleting: false, likingPost: false, isLiked: false, auth: null"
          >Loading...</span
        >
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="text-center py-5">
      <h3 class="text-danger">Post Not Found</h3>
      <p class="text-muted">The post you're looking for doesn't exist.</p>
      <router-link to="/" class="btn btn-primary"> Back to Home </router-link>
    </div>

    <!-- Post Content -->
    <div v-else-if="post" class="row">
      <div class="col-lg-8">
        <!-- Post Header -->
        <header class="mb-4">
          <h1 class="display-5 mb-3">{{ post.title }}</h1>
          <div class="d-flex align-items-center text-muted mb-4">
            <span>By {{ post.user?.name || post.author || "Unknown Author" }}</span>
            <span class="mx-2">•</span>
            <span>{{ formatDate(post.created_at) }}</span>
            <span v-if="post.category" class="mx-2">•</span>
            <span v-if="post.category" class="badge bg-secondary">{{
              post.category.name || post.category
            }}</span>
          </div>

          <!-- Post Actions -->
          <div class="post-actions mb-4">
            <div class="d-flex align-items-center gap-3">
              <!-- Like Button -->
              <button
                v-if="isAuthenticated"
                @click="toggleLike"
                :class="['btn', isLiked ? 'btn-danger' : 'btn-outline-danger']"
                :disabled="likingPost"
              >
                <i class="bi bi-heart-fill me-1" v-if="isLiked"></i>
                <i class="bi bi-heart me-1" v-else></i>
                {{ post.likes_count || 0 }}
              </button>

              <!-- Like button for unauthenticated users -->
              <router-link
                v-else
                :to="{ path: '/login', query: { redirect: $route.fullPath } }"
                :class="['btn btn-outline-danger']"
              >
                <i class="bi bi-heart me-1"></i>
                {{ post.likes_count || 0 }}
              </router-link>

              <!-- Views -->
              <span class="text-muted">
                <i class="bi bi-eye me-1"></i>
                {{ post.views || 0 }} views
              </span>
            </div>
          </div>
        </header>

        <!-- Post Content -->
        <article class="post-content mb-5">
          <div v-html="post.content"></div>
        </article>

        <!-- Comments Section -->
        <section class="comments-section">
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
                <button type="submit" class="btn btn-primary" :disabled="submitting">
                  {{ submitting ? "Posting..." : "Post Comment" }}
                </button>
              </form>
            </div>
          </div>

          <!-- Login prompt for unauthenticated users -->
          <div v-else class="card mb-4">
            <div class="card-body text-center">
              <h5 class="card-title">Join the Conversation</h5>
              <p class="text-muted">Please log in to comment on this post.</p>
              <router-link
                :to="{ path: '/login', query: { redirect: $route.fullPath } }"
                class="btn btn-primary"
              >
                Login to Comment
              </router-link>
              <span class="mx-2">or</span>
              <router-link
                :to="{ path: '/register', query: { redirect: $route.fullPath } }"
                class="btn btn-outline-primary"
              >
                Sign Up
              </router-link>
            </div>
          </div>

          <!-- Comments List -->
          <div v-if="comments.length > 0">
            <div v-for="comment in comments" :key="comment.id" class="card mb-3">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <h6 class="card-subtitle mb-1">
                    {{ comment.author_name || comment.author || "Anonymous" }}
                  </h6>
                  <small class="text-muted">{{ formatDate(comment.created_at) }}</small>
                </div>
                <p class="card-text">{{ comment.content }}</p>

                <!-- Delete button for comment author -->
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

          <!-- No Comments -->
          <div v-else class="text-center py-4">
            <p class="text-muted">No comments yet. Be the first to comment!</p>
          </div>
        </section>
      </div>

      <!-- Sidebar -->
      <div class="col-lg-4">
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0">About the Author</h5>
          </div>
          <div class="card-body">
            <h6>{{ post.user?.name || post.author || "Unknown Author" }}</h6>
            <p class="text-muted">
              {{ post.user?.bio || post.author_bio || "No bio available." }}
            </p>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">Related Posts</h5>
          </div>
          <div class="card-body">
            <ul class="list-unstyled">
              <li v-for="relatedPost in relatedPosts" :key="relatedPost.id" class="mb-3">
                <router-link
                  :to="{ name: 'PostDetail', params: { slug: relatedPost.slug } }"
                  class="text-decoration-none"
                >
                  <h6 class="mb-1">{{ relatedPost.title }}</h6>
                  <small class="text-muted">{{
                    formatDate(relatedPost.created_at)
                  }}</small>
                </router-link>
              </li>
            </ul>
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
      auth: null,
    };
  },
  computed: {
    isAuthenticated() {
      if (!this.auth) return false;
      return this.auth.isAuthenticated.value || false;
    },
    currentUser() {
      return this.auth?.user || null;
    },
  },
  created() {
    this.auth = useAuth();
  },
  mounted() {
    this.fetchPost();
    this.fetchComments();
    this.fetchRelatedPosts();
    this.checkLikeStatus();
  },
  methods: {
    async fetchPost() {
      try {
        this.loading = true;
        const postSlug = this.$route.params.slug;

        const response = await blogAPI.getPost(postSlug);
        this.post = response.data;
      } catch (error) {
        console.error("Error fetching post:", error);
        this.error = true;
      } finally {
        this.loading = false;
      }
    },
    async fetchComments() {
      try {
        const postSlug = this.$route.params.slug;

        const response = await blogAPI.getPostComments(postSlug);
        this.comments = response.data;
      } catch (error) {
        console.error("Error fetching comments:", error);
      }
    },
    async fetchRelatedPosts() {
      try {
        const postSlug = this.$route.params.slug;

        const response = await blogAPI.getRelatedPosts(postSlug);
        this.relatedPosts = response.data;
      } catch (error) {
        console.error("Error fetching related posts:", error);
      }
    },
    async checkLikeStatus() {
      if (!this.isAuthenticated || !this.post) return;

      try {
        const response = await blogAPI.checkLikeStatus(this.post.id);
        this.isLiked = response.data.is_liked;
      } catch (error) {
        console.error("Error checking like status:", error);
      }
    },
    async toggleLike() {
      if (!this.isAuthenticated || this.likingPost) return;

      try {
        this.likingPost = true;

        if (this.isLiked) {
          await blogAPI.unlikePost(this.post.id);
          this.post.likes_count = Math.max(0, (this.post.likes_count || 0) - 1);
          this.isLiked = false;
        } else {
          await blogAPI.likePost(this.post.id);
          this.post.likes_count = (this.post.likes_count || 0) + 1;
          this.isLiked = true;
        }
      } catch (error) {
        console.error("Error toggling like:", error);
      } finally {
        this.likingPost = false;
      }
    },
    async submitComment() {
      if (!this.newComment.trim()) return;

      try {
        this.submitting = true;
        const postId = this.post.id;

        const response = await blogAPI.createComment(postId, {
          content: this.newComment,
        });

        this.comments.unshift(response.data);
        this.newComment = "";
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
        this.comments = this.comments.filter((comment) => comment.id !== commentId);
      } catch (error) {
        console.error("Error deleting comment:", error);
        alert("Failed to delete comment. Please try again.");
      } finally {
        this.deleting = false;
      }
    },
    canDeleteComment(comment) {
      if (!this.isAuthenticated) return false;
      // User can delete their own comments or if they're the post author
      return (
        comment.user_id === this.currentUser?.id ||
        this.post?.user_id === this.currentUser?.id
      );
    },
    formatDate(dateString) {
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
.post-content {
  font-size: 1.1rem;
  line-height: 1.8;
}

.post-content h2 {
  margin-top: 2rem;
  margin-bottom: 1rem;
}

.post-content ul {
  margin-bottom: 1.5rem;
}

.comments-section {
  border-top: 1px solid var(--border-color);
  padding-top: 2rem;
}
</style>
