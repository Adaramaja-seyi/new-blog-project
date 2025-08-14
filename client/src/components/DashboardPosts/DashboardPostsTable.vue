<template>
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Posts ({{ posts.length }})</h5>
      <div class="d-flex gap-2">
        <button @click="$emit('bulk-action', 'publish')" :disabled="!selectedPosts.length" class="btn btn-outline-success btn-sm">
          <i class="bi bi-check-circle me-1"></i>
          Publish Selected
        </button>
        <button @click="$emit('bulk-action', 'delete')" :disabled="!selectedPosts.length" class="btn btn-outline-danger btn-sm">
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
      <div v-else-if="posts.length > 0" class="table">
        <div class="table-header">
          <div class="row align-items-center">
            <div class="col-1">
              <input type="checkbox" class="form-check-input" :checked="allSelected" @change="$emit('toggle-select-all')" />
            </div>
            <div class="col-4">Title</div>
            <div class="col-2">Category</div>
            <div class="col-2">Status</div>
            <div class="col-1">Views</div>
            <div class="col-2">Actions</div>
          </div>
        </div>
        <div class="table-body">
          <div v-for="post in posts" :key="post.id" class="table-row">
            <div class="row align-items-center">
              <div class="col-1">
                <input type="checkbox" class="form-check-input" :value="post.id" :checked="selectedPosts.includes(post.id)" @change="$emit('toggle-post-select', post.id)" />
              </div>
              <div class="col-4">
                <h6 class="mb-1">{{ post.title }}</h6>
                <small class="text-muted">{{ formatDate(post.created_at) }}</small>
              </div>
              <div class="col-2">
                <span class="badge bg-light text-dark">{{ post.category?.name || "Uncategorized" }}</span>
              </div>
              <div class="col-2">
                <span class="badge" :class="getStatusClass(post.status)">{{ post.status }}</span>
              </div>
              <div class="col-1">
                <span class="text-muted">{{ post.views || 0 }}</span>
              </div>
              <div class="col-2">
                <div class="btn-group btn-group-sm">
                  <router-link :to="{ name: 'PostDetail', params: { slug: post.slug } }" class="btn btn-light" title="View">
                    <i class="bi bi-eye"></i>
                  </router-link>
                  <router-link :to="{ name: 'CreatePost', query: { edit: post.id } }" class="btn btn-light" title="Edit">
                    <i class="bi bi-pencil"></i>
                  </router-link>
                  <button @click="$emit('delete-post', post.id)" class="btn btn-light text-danger" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-else class="empty-state">
        <div class="empty-icon">
          <i class="bi bi-file-text"></i>
        </div>
        <h5>No posts found</h5>
        <p>Create your first blog post to get started!</p>
        <router-link to="/dashboard/create" class="btn btn-primary">Create Your First Post</router-link>
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
    allSelected: Boolean
  },
  methods: {
    formatDate(dateString) {
      if (!dateString) return "";
      const date = new Date(dateString);
      return date.toLocaleDateString("en-US", { year: "numeric", month: "short", day: "numeric" });
    },
    getStatusClass(status) {
      switch (status) {
        case "published": return "bg-success";
        case "draft": return "bg-warning";
        case "pending": return "bg-info";
        default: return "bg-secondary";
      }
    }
  }
};
</script>
<style scoped>
/* Add table-specific styles here if needed */
</style>
