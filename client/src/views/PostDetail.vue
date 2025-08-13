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
            <span>By {{ post.author }}</span>
            <span class="mx-2">•</span>
            <span>{{ formatDate(post.created_at) }}</span>
            <span v-if="post.category" class="mx-2">•</span>
            <span v-if="post.category" class="badge bg-secondary">{{
              post.category
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
                  <h6 class="card-subtitle mb-1">{{ comment.author }}</h6>
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
            <h6>{{ post.author }}</h6>
            <p class="text-muted">{{ post.author_bio || "No bio available." }}</p>
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
                  :to="{ name: 'PostDetail', params: { id: relatedPost.id } }"
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
  },
  methods: {
    async fetchPost() {
      try {
        this.loading = true;
        const postId = this.$route.params.id;

        // TODO: Implement API call to fetch post
        // const response = await blogAPI.getPost(postId)
        // this.post = response.data

        // Mock data for now
        this.post = {
          id: postId,
          title: "Getting Started with Vue 3",
          content: `
            <p>Vue 3 is the latest version of the popular JavaScript framework that makes building user interfaces simple and efficient.</p>
            <p>In this post, we'll explore the key features and improvements that Vue 3 brings to the table.</p>
            <h2>Key Features</h2>
            <ul>
              <li>Composition API</li>
              <li>Better TypeScript support</li>
              <li>Improved performance</li>
              <li>Smaller bundle size</li>
            </ul>
            <p>Stay tuned for more detailed tutorials on each of these features!</p>
          `,
          author: "John Doe",
          author_bio:
            "Full-stack developer with 5+ years of experience in Vue.js and modern web technologies.",
          category: "Technology",
          created_at: "2024-01-15T10:00:00Z",
        };
      } catch (error) {
        console.error("Error fetching post:", error);
        this.error = true;
      } finally {
        this.loading = false;
      }
    },
    async fetchComments() {
      try {
        const postId = this.$route.params.id;

        // TODO: Implement API call to fetch comments
        // const response = await blogAPI.getComments(postId)
        // this.comments = response.data

        // Mock data for now
        this.comments = [
          {
            id: 1,
            content: "Great article! Vue 3 is really impressive.",
            author: "Jane Smith",
            created_at: "2024-01-15T11:00:00Z",
            user_id: 2,
          },
          {
            id: 2,
            content: "Looking forward to more Vue 3 tutorials!",
            author: "Mike Johnson",
            created_at: "2024-01-15T12:30:00Z",
            user_id: 3,
          },
        ];
      } catch (error) {
        console.error("Error fetching comments:", error);
      }
    },
    async fetchRelatedPosts() {
      try {
        const postId = this.$route.params.id;

        // TODO: Implement API call to fetch related posts
        // const response = await blogAPI.getRelatedPosts(postId)
        // this.relatedPosts = response.data

        // Mock data for now
        this.relatedPosts = [
          {
            id: 2,
            title: "Bootstrap 5 Best Practices",
            created_at: "2024-01-14T15:30:00Z",
          },
          {
            id: 3,
            title: "Modern JavaScript Features",
            created_at: "2024-01-13T09:15:00Z",
          },
        ];
      } catch (error) {
        console.error("Error fetching related posts:", error);
      }
    },
    async submitComment() {
      if (!this.newComment.trim()) return;

      try {
        this.submitting = true;
        const postId = this.$route.params.id;

        // TODO: Implement API call to create comment
        // const response = await blogAPI.createComment(postId, { content: this.newComment })
        // this.comments.unshift(response.data)

        // Mock: add comment to list
        this.comments.unshift({
          id: Date.now(),
          content: this.newComment,
          author: "Current User", // TODO: Get from auth state
          created_at: new Date().toISOString(),
          user_id: 1, // TODO: Get from auth state
        });

        this.newComment = "";
      } catch (error) {
        console.error("Error posting comment:", error);
      } finally {
        this.submitting = false;
      }
    },
    async deleteComment(commentId) {
      try {
        this.deleting = true;

        // TODO: Implement API call to delete comment
        // await blogAPI.deleteComment(commentId)

        // Remove from local list
        this.comments = this.comments.filter((c) => c.id !== commentId);
      } catch (error) {
        console.error("Error deleting comment:", error);
      } finally {
        this.deleting = false;
      }
    },
    canDeleteComment(comment) {
      // Check if current user can delete this comment
      return this.currentUser && comment.user_id === this.currentUser.id;
    },
    async toggleLike() {
      if (!this.isAuthenticated) return;

      try {
        this.likingPost = true;
        // TODO: Implement API call to toggle like
        // const response = await blogAPI.toggleLike(this.post.id)

        // Mock implementation
        this.isLiked = !this.isLiked;
        if (this.isLiked) {
          this.post.likes_count = (this.post.likes_count || 0) + 1;
        } else {
          this.post.likes_count = Math.max((this.post.likes_count || 0) - 1, 0);
        }
      } catch (error) {
        console.error("Error toggling like:", error);
      } finally {
        this.likingPost = false;
      }
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
