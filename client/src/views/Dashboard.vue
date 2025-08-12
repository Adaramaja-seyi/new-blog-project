<template>
  <div class="container">
    <!-- Dashboard Header -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h1 class="mb-2">Dashboard</h1>
            <p class="text-muted mb-0">Manage your blog posts and account</p>
          </div>
          <router-link to="/post/new" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>
            New Post
          </router-link>
        </div>
      </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row mb-4">
      <div class="col-md-3 mb-3">
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-shrink-0">
                <i class="bi bi-file-text fs-1 text-muted"></i>
              </div>
              <div class="flex-grow-1 ms-3">
                <h4 class="mb-1">{{ stats.totalPosts }}</h4>
                <p class="text-muted mb-0">Total Posts</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-shrink-0">
                <i class="bi bi-eye fs-1 text-muted"></i>
              </div>
              <div class="flex-grow-1 ms-3">
                <h4 class="mb-1">{{ stats.totalViews }}</h4>
                <p class="text-muted mb-0">Total Views</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-shrink-0">
                <i class="bi bi-chat-dots fs-1 text-muted"></i>
              </div>
              <div class="flex-grow-1 ms-3">
                <h4 class="mb-1">{{ stats.totalComments }}</h4>
                <p class="text-muted mb-0">Total Comments</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-shrink-0">
                <i class="bi bi-heart fs-1 text-muted"></i>
              </div>
              <div class="flex-grow-1 ms-3">
                <h4 class="mb-1">{{ stats.totalLikes }}</h4>
                <p class="text-muted mb-0">Total Likes</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Recent Posts -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Recent Posts</h5>
            <router-link to="/posts" class="btn btn-outline-primary btn-sm">
              View All
            </router-link>
          </div>
          <div class="card-body">
            <!-- Loading State -->
            <div v-if="loading" class="text-center py-4">
              <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
            
            <!-- Posts Table -->
            <div v-else-if="posts.length > 0">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Title</th>
                      <th>Status</th>
                      <th>Views</th>
                      <th>Comments</th>
                      <th>Created</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="post in posts" :key="post.id">
                      <td>
                        <router-link 
                          :to="{ name: 'PostDetail', params: { id: post.id } }"
                          class="text-decoration-none"
                        >
                          {{ post.title }}
                        </router-link>
                      </td>
                      <td>
                        <span 
                          class="badge"
                          :class="getStatusBadgeClass(post.status)"
                        >
                          {{ post.status }}
                        </span>
                      </td>
                      <td>{{ post.views }}</td>
                      <td>{{ post.comments_count }}</td>
                      <td>{{ formatDate(post.created_at) }}</td>
                      <td>
                        <div class="btn-group btn-group-sm">
                          <router-link 
                            :to="{ name: 'EditPost', params: { id: post.id } }"
                            class="btn btn-outline-primary"
                            title="Edit"
                          >
                            <i class="bi bi-pencil"></i>
                          </router-link>
                          <button 
                            @click="deletePost(post.id)"
                            class="btn btn-outline-danger"
                            title="Delete"
                            :disabled="deleting"
                          >
                            <i class="bi bi-trash"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            
            <!-- Empty State -->
            <div v-else class="text-center py-4">
              <i class="bi bi-file-text fs-1 text-muted mb-3"></i>
              <h5 class="text-muted">No posts yet</h5>
              <p class="text-muted">Start writing your first blog post!</p>
              <router-link to="/post/new" class="btn btn-primary">
                Create Your First Post
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Recent Comments -->
    <div class="row mt-4">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">Recent Comments</h5>
          </div>
          <div class="card-body">
            <!-- Loading State -->
            <div v-if="loadingComments" class="text-center py-4">
              <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
            
            <!-- Comments List -->
            <div v-else-if="comments.length > 0">
              <div 
                v-for="comment in comments" 
                :key="comment.id" 
                class="d-flex mb-3"
              >
                <div class="flex-shrink-0">
                  <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-person text-white"></i>
                  </div>
                </div>
                <div class="flex-grow-1 ms-3">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <h6 class="mb-1">{{ comment.author }}</h6>
                      <p class="mb-1">{{ comment.content }}</p>
                      <small class="text-muted">
                        On: 
                        <router-link 
                          :to="{ name: 'PostDetail', params: { id: comment.post_id } }"
                          class="text-decoration-none"
                        >
                          {{ comment.post_title }}
                        </router-link>
                      </small>
                    </div>
                    <small class="text-muted">{{ formatDate(comment.created_at) }}</small>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Empty State -->
            <div v-else class="text-center py-4">
              <i class="bi bi-chat-dots fs-1 text-muted mb-3"></i>
              <h5 class="text-muted">No comments yet</h5>
              <p class="text-muted">Comments on your posts will appear here.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { blogAPI } from '../api.js'

export default {
  name: 'Dashboard',
  data() {
    return {
      posts: [],
      comments: [],
      stats: {
        totalPosts: 0,
        totalViews: 0,
        totalComments: 0,
        totalLikes: 0
      },
      loading: true,
      loadingComments: true,
      deleting: false
    }
  },
  mounted() {
    this.fetchDashboardData()
  },
  methods: {
    async fetchDashboardData() {
      await Promise.all([
        this.fetchPosts(),
        this.fetchComments(),
        this.fetchStats()
      ])
    },
    async fetchPosts() {
      try {
        // TODO: Implement API call to fetch user's posts
        // const response = await blogAPI.getUserPosts()
        // this.posts = response.data
        
        // Mock data for now
        this.posts = [
          {
            id: 1,
            title: 'Getting Started with Vue 3',
            status: 'published',
            views: 1250,
            comments_count: 8,
            created_at: '2024-01-15T10:00:00Z'
          },
          {
            id: 2,
            title: 'Bootstrap 5 Best Practices',
            status: 'draft',
            views: 0,
            comments_count: 0,
            created_at: '2024-01-14T15:30:00Z'
          }
        ]
      } catch (error) {
        console.error('Error fetching posts:', error)
      } finally {
        this.loading = false
      }
    },
    async fetchComments() {
      try {
        // TODO: Implement API call to fetch recent comments
        // const response = await blogAPI.getRecentComments()
        // this.comments = response.data
        
        // Mock data for now
        this.comments = [
          {
            id: 1,
            author: 'Jane Smith',
            content: 'Great article! Very helpful for beginners.',
            post_id: 1,
            post_title: 'Getting Started with Vue 3',
            created_at: '2024-01-15T11:00:00Z'
          },
          {
            id: 2,
            author: 'Mike Johnson',
            content: 'Looking forward to more tutorials!',
            post_id: 1,
            post_title: 'Getting Started with Vue 3',
            created_at: '2024-01-15T12:30:00Z'
          }
        ]
      } catch (error) {
        console.error('Error fetching comments:', error)
      } finally {
        this.loadingComments = false
      }
    },
    async fetchStats() {
      try {
        // TODO: Implement API call to fetch user stats
        // const response = await blogAPI.getUserStats()
        // this.stats = response.data
        
        // Mock data for now
        this.stats = {
          totalPosts: 2,
          totalViews: 1250,
          totalComments: 8,
          totalLikes: 45
        }
      } catch (error) {
        console.error('Error fetching stats:', error)
      }
    },
    async deletePost(postId) {
      if (!confirm('Are you sure you want to delete this post?')) {
        return
      }
      
      try {
        this.deleting = true
        
        // TODO: Implement API call to delete post
        // await blogAPI.deletePost(postId)
        
        // Remove from local list
        this.posts = this.posts.filter(p => p.id !== postId)
        
        // Update stats
        this.stats.totalPosts--
        
      } catch (error) {
        console.error('Error deleting post:', error)
      } finally {
        this.deleting = false
      }
    },
    getStatusBadgeClass(status) {
      switch (status) {
        case 'published':
          return 'bg-success'
        case 'draft':
          return 'bg-warning'
        case 'archived':
          return 'bg-secondary'
        default:
          return 'bg-primary'
      }
    },
    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      })
    }
  }
}
</script>

<style scoped>
.card {
  border: 1px solid var(--border-color);
  border-radius: var(--border-radius-lg);
}

.table th {
  border-top: none;
  font-weight: 600;
  color: var(--text-primary);
}

.table td {
  vertical-align: middle;
}

.btn-group .btn {
  border-radius: var(--border-radius);
}

.badge {
  font-size: 0.75rem;
}
</style>

