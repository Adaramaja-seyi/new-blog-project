<template>
  <div class="dashboard-home">
    <!-- Welcome Section -->
    <div class="welcome-section mb-4">
      <h2>Welcome back, {{ user?.name || 'Author' }}!</h2>
      <p class="text-muted">Here's what's happening with your blog today.</p>
    </div>
    
    <!-- Stats Cards -->
    <div class="row mb-4">
      <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
          <div class="stats-icon">
            <i class="bi bi-file-text"></i>
          </div>
          <div class="stats-number">{{ stats.totalPosts }}</div>
          <div class="stats-label">Total Posts</div>
        </div>
      </div>
      
      <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
          <div class="stats-icon">
            <i class="bi bi-heart"></i>
          </div>
          <div class="stats-number">{{ stats.totalLikes }}</div>
          <div class="stats-label">Total Likes</div>
        </div>
      </div>
      
      <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
          <div class="stats-icon">
            <i class="bi bi-chat-dots"></i>
          </div>
          <div class="stats-number">{{ stats.totalComments }}</div>
          <div class="stats-label">Total Comments</div>
        </div>
      </div>
      
      <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
          <div class="stats-icon">
            <i class="bi bi-eye"></i>
          </div>
          <div class="stats-number">{{ stats.totalViews }}</div>
          <div class="stats-label">Total Views</div>
        </div>
      </div>
    </div>
    
    <!-- Main Content Grid -->
    <div class="row">
      <!-- Recent Posts -->
      <div class="col-lg-8 mb-4">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Recent Posts</h5>
            <router-link to="/dashboard/posts" class="btn btn-outline-primary btn-sm">
              View All
            </router-link>
          </div>
          <div class="card-body p-0">
            <div v-if="loading" class="loading-spinner">
              <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
            
            <div v-else-if="recentPosts.length > 0" class="table">
              <div class="table-header">
                <div class="row align-items-center">
                  <div class="col-6">Title</div>
                  <div class="col-2">Status</div>
                  <div class="col-2">Views</div>
                  <div class="col-2">Actions</div>
                </div>
              </div>
              <div class="table-body">
                <div 
                  v-for="post in recentPosts" 
                  :key="post.id" 
                  class="table-row"
                >
                  <div class="row align-items-center">
                    <div class="col-6">
                      <h6 class="mb-1">{{ post.title }}</h6>
                      <small class="text-muted">{{ formatDate(post.created_at) }}</small>
                    </div>
                    <div class="col-2">
                      <span class="badge" :class="getStatusClass(post.status)">
                        {{ post.status }}
                      </span>
                    </div>
                    <div class="col-2">
                      <span class="text-muted">{{ post.views || 0 }}</span>
                    </div>
                    <div class="col-2">
                      <div class="btn-group btn-group-sm">
                        <router-link 
                          :to="{ name: 'PostDetail', params: { id: post.id } }"
                          class="btn btn-light"
                          title="View"
                        >
                          <i class="bi bi-eye"></i>
                        </router-link>
                        <router-link 
                          :to="{ name: 'EditPost', params: { id: post.id } }"
                          class="btn btn-light"
                          title="Edit"
                        >
                          <i class="bi bi-pencil"></i>
                        </router-link>
                        <button 
                          @click="deletePost(post.id)"
                          class="btn btn-light text-danger"
                          title="Delete"
                        >
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
              <h5>No posts yet</h5>
              <p>Start writing your first blog post!</p>
              <router-link to="/dashboard/create" class="btn btn-primary">
                Create Your First Post
              </router-link>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Recent Comments -->
      <div class="col-lg-4 mb-4">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Recent Comments</h5>
            <router-link to="/dashboard/comments" class="btn btn-outline-primary btn-sm">
              View All
            </router-link>
          </div>
          <div class="card-body p-0">
            <div v-if="recentComments.length > 0" class="table">
              <div class="table-body">
                <div 
                  v-for="comment in recentComments" 
                  :key="comment.id" 
                  class="table-row"
                >
                  <div class="d-flex align-items-start">
                    <div class="flex-grow-1">
                      <div class="d-flex justify-content-between align-items-start mb-1">
                        <strong class="text-sm">{{ comment.author }}</strong>
                        <small class="text-muted">{{ formatDate(comment.created_at) }}</small>
                      </div>
                      <p class="text-sm mb-1">{{ truncateText(comment.content, 60) }}</p>
                      <small class="text-primary-blue">on "{{ comment.post_title }}"</small>
                      <div class="mt-2">
                        <button 
                          v-if="comment.status === 'pending'"
                          @click="approveComment(comment.id)"
                          class="btn btn-outline-success btn-sm me-1"
                          title="Approve"
                        >
                          <i class="bi bi-check"></i>
                        </button>
                        <button 
                          @click="deleteComment(comment.id)"
                          class="btn btn-outline-danger btn-sm"
                          title="Delete"
                        >
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
                <i class="bi bi-chat-dots"></i>
              </div>
              <h6>No comments yet</h6>
              <p>Comments will appear here once readers start engaging with your posts.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Chart Section -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">Post Engagement</h5>
          </div>
          <div class="card-body">
            <div class="chart-placeholder">
              <div class="chart-content">
                <i class="bi bi-graph-up chart-icon"></i>
                <h6>Engagement Analytics</h6>
                <p class="text-muted">Chart visualization will be displayed here</p>
                <div class="chart-stats">
                  <div class="row text-center">
                    <div class="col-4">
                      <div class="stat-item">
                        <div class="stat-number">2.4k</div>
                        <div class="stat-label">Monthly Views</div>
                      </div>
                    </div>
                    <div class="col-4">
                      <div class="stat-item">
                        <div class="stat-number">156</div>
                        <div class="stat-label">Comments</div>
                      </div>
                    </div>
                    <div class="col-4">
                      <div class="stat-item">
                        <div class="stat-number">89</div>
                        <div class="stat-label">Likes</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useAuth } from '../stores/auth.js'
import { blogAPI } from '../api.js'

export default {
  name: 'DashboardHome',
  data() {
    return {
      auth: null,
      loading: false,
      stats: {
        totalPosts: 0,
        totalViews: 0,
        totalComments: 0,
        totalLikes: 0
      },
      recentPosts: [],
      recentComments: []
    }
  },
  computed: {
    user() {
      return this.auth?.user.value || null
    }
  },
  mounted() {
    this.auth = useAuth()
    this.loadDashboardData()
  },
  methods: {
    async loadDashboardData() {
      this.loading = true
      try {
        // Load dashboard stats
        await this.loadStats()
        
        // Load recent posts and comments
        await Promise.all([
          this.loadRecentPosts(),
          this.loadRecentComments()
        ])
      } catch (error) {
        console.error('Error loading dashboard data:', error)
      } finally {
        this.loading = false
      }
    },

    async loadStats() {
      try {
        const response = await blogAPI.dashboard.getStats()
        if (response.data) {
          this.stats = {
            totalPosts: response.data.total_posts || 0,
            totalViews: response.data.total_views || 0,
            totalComments: response.data.total_comments || 0,
            totalLikes: response.data.total_likes || 0
          }
        }
      } catch (error) {
        console.error('Failed to load dashboard stats:', error)
      }
    },

    async loadRecentPosts() {
      try {
        const response = await blogAPI.dashboard.getRecentPosts()
        if (response.data) {
          this.recentPosts = response.data
        }
      } catch (error) {
        console.error('Failed to load recent posts:', error)
      }
    },

    async loadRecentComments() {
      try {
        const response = await blogAPI.dashboard.getRecentComments()
        if (response.data) {
          this.recentComments = response.data
        }
      } catch (error) {
        console.error('Failed to load recent comments:', error)
      }
    },
    formatDate(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    },
    truncateText(text, maxLength) {
      if (!text) return '';
      if (text.length <= maxLength) return text;
      return text.substring(0, maxLength) + '...';
    },
    getStatusClass(status) {
      switch (status) {
        case 'published':
          return 'bg-success';
        case 'draft':
          return 'bg-warning';
        case 'pending':
          return 'bg-info';
        default:
          return 'bg-secondary';
      }
    },
    async deletePost(postId) {
      if (confirm('Are you sure you want to delete this post?')) {
        try {
          const result = await this.postsStore.deletePost(postId)
          if (result.success) {
            // Refresh dashboard data
            await this.loadDashboardData()
          } else {
            console.error('Failed to delete post:', result.message)
          }
        } catch (error) {
          console.error('Error deleting post:', error)
        }
      }
    },
    async approveComment(commentId) {
      try {
        const comment = this.recentComments.find(c => c.id === commentId)
        if (comment) {
          comment.status = 'approved'
        }
      } catch (error) {
        console.error('Error approving comment:', error)
      }
    },
    async deleteComment(commentId) {
      if (confirm('Are you sure you want to delete this comment?')) {
        try {
          this.recentComments = this.recentComments.filter(comment => comment.id !== commentId)
          this.stats.totalComments--
        } catch (error) {
          console.error('Error deleting comment:', error)
        }
      }
    }
  }
}
</script>

<style scoped lang="scss">
.dashboard-home {
  .welcome-section {
    h2 {
      color: var(--text-primary);
      margin-bottom: 0.5rem;
      font-weight: 600;
    }
  }
  
  .text-sm {
    font-size: 0.875rem;
  }
  
  .chart-placeholder {
    min-height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;
    
    .chart-content {
      text-align: center;
      
      .chart-icon {
        font-size: 3rem;
        color: var(--text-muted);
        margin-bottom: 1rem;
      }
      
      .chart-stats {
        margin-top: 2rem;
        
        .stat-item {
          .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
          }
          
          .stat-label {
            font-size: 0.875rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
          }
        }
      }
    }
  }
}

// Responsive adjustments
@media (max-width: 768px) {
  .dashboard-home {
    .table {
      .table-row {
        .row {
          .col-2 {
            margin-bottom: 0.5rem;
          }
        }
      }
    }
  }
}
</style>
