<template>
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Recent Posts</h5>
      <router-link to="/dashboard/posts" class="btn btn-outline-primary btn-sm">View All</router-link>
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
            <div class="col-6">Title</div>
            <div class="col-2">Status</div>
            <div class="col-2">Views</div>
            <div class="col-2">Actions</div>
          </div>
        </div>
        <div class="table-body">
          <div v-for="post in posts" :key="post.id" class="table-row">
            <div class="row align-items-center">
              <div class="col-6">
                <h6 class="mb-1">{{ post.title }}</h6>
                <small class="text-muted">{{ formatDate(post.created_at) }}</small>
              </div>
              <div class="col-2">
                <span class="badge" :class="getStatusClass(post.status)">{{ post.status }}</span>
              </div>
              <div class="col-2">
                <span class="text-muted">{{ post.views_count || 0 }}</span>
              </div>
              <div class="col-2">
                <div class="btn-group btn-group-sm">
                  <router-link :to="{ name: 'PostDetail', params: { slug: post.slug } }" class="btn btn-light" title="View">
                    <i class="bi bi-eye"></i>
                  </router-link>
                  <router-link :to="{ name: 'CreatePost', query: { edit: post.id } }" class="btn btn-light" title="Edit">
                    <i class="bi bi-pencil"></i>
                  </router-link>
                  <button @click="$emit('delete', post.id)" class="btn btn-light text-danger" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-else class="empty-state">
        <div class="empty-icon"><i class="bi bi-file-text"></i></div>
        <h5>No posts yet</h5>
        <p>Start writing your first blog post!</p>
        <router-link to="/dashboard/create" class="btn btn-primary">Create Your First Post</router-link>
      </div>
    </div>
  </div>
</template>

<script>
import { blogAPI } from '../../api.js'

export default {
  name: 'RecentPostsTable',
  emits: ['delete'],
  data() {
    return {
      posts: [],
      loading: false,
    }
  },
  mounted() {
    this.fetchPosts()
  },
  methods: {
    async fetchPosts() {
      this.loading = true
      try {
        const response = await blogAPI.getDashboardStats()
        if (response.data && response.data.data && response.data.data.recent_posts) {
          this.posts = response.data.data.recent_posts
        }
      } catch (e) {
        // handle error
      } finally {
        this.loading = false
      }
    },
    formatDate(dateString) {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
    },
    getStatusClass(status) {
      switch (status) {
        case 'published': return 'bg-success'
        case 'draft': return 'bg-warning'
        case 'pending': return 'bg-info'
        default: return 'bg-secondary'
      }
    },
  },
}
</script>

<style scoped lang="scss">
.text-sm {
  font-size: 0.875rem;
}
@media (max-width: 768px) {
  .table-row .row .col-2 {
    margin-bottom: 0.5rem;
  }
}
</style>
