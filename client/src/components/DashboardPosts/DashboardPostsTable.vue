<template>
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Posts ({{ posts.length }})</h5>
      <div class="d-flex gap-2">
        <button
          @click="$emit('bulk-action', 'publish')"
          :disabled="!selectedPosts.length"
          class="btn btn-outline-success btn-sm"
        >
          <i class="bi bi-check-circle me-1"></i>
          Publish Selected
        </button>
        <button
          @click="$emit('bulk-action', 'delete')"
          :disabled="!selectedPosts.length"
          class="btn btn-outline-danger btn-sm"
        >
          <i class="bi bi-trash me-1"></i>
          Delete Selected
        </button>
      </div>
    </div>
    <div class="card-body p-0">
      <div v-if="loading" class="loading-spinner">
        <div class="spinner-border" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
      <div v-else-if="posts.length > 0" class="table-responsive">
        <table class="table table-sm table-hover table-bordered align-middle">
          <thead class="table-light">
            <tr>
              <div class="col-1">
                <input
                  type="checkbox"
                  class="form-check-input"
                  :checked="allSelected"
                  @change="$emit('toggle-select-all')"
                />
              </div>
              <th scope="col">#</th>
              <th scope="col">Title</th>
              <th scope="col">Category</th>
              <th scope="col">Status</th>
              <th scope="col">Views</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(post, index) in posts" :key="post.id">
              <td>
                <input
                  type="checkbox"
                  class="form-check-input"
                  :checked="selectedPosts.includes(post.id)"
                  @change="$emit('toggle-post-select', post.id)"
                />
              </td>
              <td>{{ index + 1 }}</td>
              <td>
                <h6 class="mb-1">{{ post.title }}</h6>
                <small class="text-muted">{{
                  formatDate(post.created_at)
                }}</small>
              </td>
              <td>
                <span class="badge bg-light text-dark">
                  {{ post.category?.name || "Uncategorized" }}
                </span>
              </td>
              <td>
                <span class="badge" :class="getStatusClass(post.status)">{{
                  post.status
                }}</span>
              </td>
              <td>{{ post.views_count || 0 }}</td>
              <td>
                <div class="btn-group btn-group-sm">
                  <router-link
                    :to="{ name: 'PostDetail', params: { slug: post.slug } }"
                    class="btn btn-light"
                    title="View"
                  >
                    <i class="bi bi-eye"></i>
                  </router-link>
                  <router-link
                    :to="{ name: 'CreatePost', query: { edit: post.slug } }"
                    class="btn btn-light"
                    title="Edit"
                  >
                    <i class="bi bi-pencil"></i>
                  </router-link>
                  <button
                    @click="$emit('delete-post', post.slug)"
                    class="btn btn-light text-danger"
                    title="Delete"
                  >
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-else class="empty-state">
        <div class="empty-icon">
          <i class="bi bi-file-text"></i>
        </div>
        <h5>No posts found</h5>
        <p>Create your first blog post to get started!</p>
        <router-link to="/dashboard/create" class="btn btn-primary"
          >Create Your First Post</router-link
        >
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "DashboardPostsTable",
  props: {
    posts: Array,
    selectedPosts: Array,
    loading: Boolean,
    allSelected: Boolean,
  },
  methods: {
    formatDate(dateString) {
      if (!dateString) return "";
      const date = new Date(dateString);
      return date.toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
      });
    },
    getStatusClass(status) {
      switch (status) {
        case "published":
          return "bg-success";
        case "draft":
          return "bg-warning";
        case "pending":
          return "bg-info";
        default:
          return "bg-secondary";
      }
    },
  },
};
</script>
<style scoped>
/* Add table-specific styles here if needed */
</style>
