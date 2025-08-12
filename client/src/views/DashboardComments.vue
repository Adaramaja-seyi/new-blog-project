<template>
  <div class="dashboard-comments">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2>Comments</h2>
        <p class="text-muted mb-0">Moderate and manage comments on your posts</p>
      </div>
      <div class="d-flex gap-2">
        <button 
          @click="bulkAction('approve')" 
          :disabled="!selectedComments.length"
          class="btn btn-outline-success"
        >
          <i class="bi bi-check-circle me-2"></i>
          Approve Selected
        </button>
        <button 
          @click="bulkAction('delete')" 
          :disabled="!selectedComments.length"
          class="btn btn-outline-danger"
        >
          <i class="bi bi-trash me-2"></i>
          Delete Selected
        </button>
      </div>
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-md-3 mb-3 mb-md-0">
            <label class="form-label">Status Filter</label>
            <select v-model="statusFilter" class="form-select">
              <option value="">All Comments</option>
              <option value="approved">Approved</option>
              <option value="pending">Pending</option>
              <option value="spam">Spam</option>
            </select>
          </div>
          <div class="col-md-3 mb-3 mb-md-0">
            <label class="form-label">Post Filter</label>
            <select v-model="postFilter" class="form-select">
              <option value="">All Posts</option>
              <option v-for="post in posts" :key="post.id" :value="post.id">
                {{ post.title }}
              </option>
            </select>
          </div>
          <div class="col-md-4 mb-3 mb-md-0">
            <label class="form-label">Date Range</label>
            <div class="input-group">
              <input 
                type="date" 
                class="form-control"
                v-model="dateFrom"
              />
              <span class="input-group-text">to</span>
              <input 
                type="date" 
                class="form-control"
                v-model="dateTo"
              />
            </div>
          </div>
          <div class="col-md-2">
            <label class="form-label">Search</label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="bi bi-search"></i>
              </span>
              <input 
                type="text" 
                class="form-control"
                placeholder="Search comments..."
                v-model="searchQuery"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Comments Table -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Comments ({{ filteredComments.length }})</h5>
        <div class="d-flex gap-2">
          <button 
            @click="selectAll"
            class="btn btn-outline-primary btn-sm"
          >
            Select All
          </button>
          <button 
            @click="clearSelection"
            class="btn btn-outline-secondary btn-sm"
          >
            Clear Selection
          </button>
        </div>
      </div>
      <div class="card-body p-0">
        <div v-if="loading" class="loading-spinner">
          <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
        
        <div v-else-if="filteredComments.length > 0" class="table">
          <div class="table-header">
            <div class="row align-items-center">
              <div class="col-1">
                <input 
                  type="checkbox" 
                  class="form-check-input"
                  :checked="allSelected"
                  @change="toggleSelectAll"
                />
              </div>
              <div class="col-3">Author</div>
              <div class="col-4">Comment</div>
              <div class="col-2">Post</div>
              <div class="col-1">Status</div>
              <div class="col-1">Actions</div>
            </div>
          </div>
          <div class="table-body">
            <div 
              v-for="comment in paginatedComments" 
              :key="comment.id" 
              class="table-row"
            >
              <div class="row align-items-start">
                <div class="col-1">
                  <input 
                    type="checkbox" 
                    class="form-check-input"
                    :value="comment.id"
                    v-model="selectedComments"
                  />
                </div>
                <div class="col-3">
                  <div class="comment-author">
                    <div class="author-info">
                      <strong>{{ comment.author }}</strong>
                      <small class="text-muted d-block">{{ comment.email }}</small>
                      <small class="text-muted">{{ formatDate(comment.created_at) }}</small>
                    </div>
                  </div>
                </div>
                <div class="col-4">
                  <div class="comment-content">
                    <p class="mb-1">{{ comment.content }}</p>
                    <small class="text-primary-blue">on "{{ comment.post_title }}"</small>
                  </div>
                </div>
                <div class="col-2">
                  <div class="post-info">
                    <small class="text-muted">{{ comment.post_title }}</small>
                  </div>
                </div>
                <div class="col-1">
                  <span class="badge" :class="getStatusClass(comment.status)">
                    {{ comment.status }}
                  </span>
                </div>
                <div class="col-1">
                  <div class="btn-group btn-group-sm">
                    <button 
                      v-if="comment.status === 'pending'"
                      @click="approveComment(comment.id)"
                      class="btn btn-light"
                      title="Approve"
                    >
                      <i class="bi bi-check text-success"></i>
                    </button>
                    <button 
                      @click="markAsSpam(comment.id)"
                      class="btn btn-light"
                      title="Mark as Spam"
                    >
                      <i class="bi bi-shield-exclamation text-warning"></i>
                    </button>
                    <button 
                      @click="deleteComment(comment.id)"
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
            <i class="bi bi-chat-dots"></i>
          </div>
          <h5>No comments found</h5>
          <p>Comments will appear here once readers start engaging with your posts.</p>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="filteredComments.length > commentsPerPage" class="d-flex justify-content-center mt-4">
      <nav>
        <ul class="pagination">
          <li class="page-item" :class="{ disabled: currentPage === 1 }">
            <button @click="currentPage--" class="page-link">Previous</button>
          </li>
          <li 
            v-for="page in totalPages" 
            :key="page"
            class="page-item"
            :class="{ active: page === currentPage }"
          >
            <button @click="currentPage = page" class="page-link">{{ page }}</button>
          </li>
          <li class="page-item" :class="{ disabled: currentPage === totalPages }">
            <button @click="currentPage++" class="page-link">Next</button>
          </li>
        </ul>
      </nav>
    </div>

    <!-- Comment Statistics -->
    <div class="row mt-4">
      <div class="col-md-3">
        <div class="stats-card">
          <div class="stats-icon">
            <i class="bi bi-chat-dots"></i>
          </div>
          <div class="stats-number">{{ stats.total }}</div>
          <div class="stats-label">Total Comments</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stats-card">
          <div class="stats-icon">
            <i class="bi bi-check-circle"></i>
          </div>
          <div class="stats-number">{{ stats.approved }}</div>
          <div class="stats-label">Approved</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stats-card">
          <div class="stats-icon">
            <i class="bi bi-clock"></i>
          </div>
          <div class="stats-number">{{ stats.pending }}</div>
          <div class="stats-label">Pending</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stats-card">
          <div class="stats-icon">
            <i class="bi bi-shield-exclamation"></i>
          </div>
          <div class="stats-number">{{ stats.spam }}</div>
          <div class="stats-label">Spam</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DashboardComments',
  data() {
    return {
      loading: false,
      comments: [],
      posts: [],
      selectedComments: [],
      statusFilter: '',
      postFilter: '',
      dateFrom: '',
      dateTo: '',
      searchQuery: '',
      currentPage: 1,
      commentsPerPage: 10,
      stats: {
        total: 0,
        approved: 0,
        pending: 0,
        spam: 0
      }
    }
  },
  computed: {
    filteredComments() {
      let filtered = this.comments

      if (this.statusFilter) {
        filtered = filtered.filter(comment => comment.status === this.statusFilter)
      }

      if (this.postFilter) {
        filtered = filtered.filter(comment => comment.post_id === this.postFilter)
      }

      if (this.dateFrom) {
        filtered = filtered.filter(comment => new Date(comment.created_at) >= new Date(this.dateFrom))
      }

      if (this.dateTo) {
        filtered = filtered.filter(comment => new Date(comment.created_at) <= new Date(this.dateTo))
      }

      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase()
        filtered = filtered.filter(comment => 
          comment.author.toLowerCase().includes(query) ||
          comment.content.toLowerCase().includes(query) ||
          comment.post_title.toLowerCase().includes(query)
        )
      }

      return filtered
    },
    paginatedComments() {
      const start = (this.currentPage - 1) * this.commentsPerPage
      const end = start + this.commentsPerPage
      return this.filteredComments.slice(start, end)
    },
    totalPages() {
      return Math.ceil(this.filteredComments.length / this.commentsPerPage)
    },
    allSelected() {
      return this.paginatedComments.length > 0 && 
             this.paginatedComments.every(comment => this.selectedComments.includes(comment.id))
    }
  },
  mounted() {
    this.loadComments()
    this.loadPosts()
  },
  methods: {
    async loadComments() {
      this.loading = true
      try {
        // Mock data for now
        this.comments = [
          {
            id: 1,
            author: 'John Doe',
            email: 'john@example.com',
            content: 'Great article! Very helpful for beginners.',
            post_title: 'Getting Started with Vue 3',
            post_id: 1,
            status: 'approved',
            created_at: '2024-01-15T12:00:00Z'
          },
          {
            id: 2,
            author: 'Jane Smith',
            email: 'jane@example.com',
            content: 'I have a question about the setup process...',
            post_title: 'Getting Started with Vue 3',
            post_id: 1,
            status: 'pending',
            created_at: '2024-01-15T11:30:00Z'
          },
          {
            id: 3,
            author: 'Bob Johnson',
            email: 'bob@example.com',
            content: 'This is exactly what I was looking for!',
            post_title: 'Bootstrap 5 Best Practices',
            post_id: 2,
            status: 'approved',
            created_at: '2024-01-14T16:45:00Z'
          },
          {
            id: 4,
            author: 'Spam Bot',
            email: 'spam@bot.com',
            content: 'Buy cheap viagra now!',
            post_title: 'Modern Web Development Trends',
            post_id: 3,
            status: 'spam',
            created_at: '2024-01-13T08:20:00Z'
          }
        ]
        
        this.updateStats()
      } catch (error) {
        console.error('Error loading comments:', error)
      } finally {
        this.loading = false
      }
    },
    async loadPosts() {
      try {
        this.posts = [
          { id: 1, title: 'Getting Started with Vue 3' },
          { id: 2, title: 'Bootstrap 5 Best Practices' },
          { id: 3, title: 'Modern Web Development Trends' }
        ]
      } catch (error) {
        console.error('Error loading posts:', error)
      }
    },
    updateStats() {
      this.stats = {
        total: this.comments.length,
        approved: this.comments.filter(c => c.status === 'approved').length,
        pending: this.comments.filter(c => c.status === 'pending').length,
        spam: this.comments.filter(c => c.status === 'spam').length
      }
    },
    formatDate(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },
    getStatusClass(status) {
      switch (status) {
        case 'approved':
          return 'bg-success';
        case 'pending':
          return 'bg-warning';
        case 'spam':
          return 'bg-danger';
        default:
          return 'bg-secondary';
      }
    },
    async approveComment(commentId) {
      try {
        const comment = this.comments.find(c => c.id === commentId)
        if (comment) {
          comment.status = 'approved'
          this.updateStats()
        }
      } catch (error) {
        console.error('Error approving comment:', error)
      }
    },
    async markAsSpam(commentId) {
      try {
        const comment = this.comments.find(c => c.id === commentId)
        if (comment) {
          comment.status = 'spam'
          this.updateStats()
        }
      } catch (error) {
        console.error('Error marking comment as spam:', error)
      }
    },
    async deleteComment(commentId) {
      if (confirm('Are you sure you want to delete this comment?')) {
        try {
          this.comments = this.comments.filter(comment => comment.id !== commentId)
          this.selectedComments = this.selectedComments.filter(id => id !== commentId)
          this.updateStats()
        } catch (error) {
          console.error('Error deleting comment:', error)
        }
      }
    },
    selectAll() {
      this.selectedComments = this.paginatedComments.map(comment => comment.id)
    },
    clearSelection() {
      this.selectedComments = []
    },
    toggleSelectAll() {
      if (this.allSelected) {
        this.selectedComments = []
      } else {
        this.selectedComments = this.paginatedComments.map(comment => comment.id)
      }
    },
    async bulkAction(action) {
      if (!this.selectedComments.length) return

      if (action === 'approve') {
        this.comments = this.comments.map(comment => {
          if (this.selectedComments.includes(comment.id)) {
            return { ...comment, status: 'approved' }
          }
          return comment
        })
        this.selectedComments = []
        this.updateStats()
      } else if (action === 'delete') {
        if (confirm(`Are you sure you want to delete ${this.selectedComments.length} comments?`)) {
          this.comments = this.comments.filter(comment => !this.selectedComments.includes(comment.id))
          this.selectedComments = []
          this.updateStats()
        }
      }
    }
  },
  watch: {
    statusFilter() {
      this.currentPage = 1
    },
    postFilter() {
      this.currentPage = 1
    },
    dateFrom() {
      this.currentPage = 1
    },
    dateTo() {
      this.currentPage = 1
    },
    searchQuery() {
      this.currentPage = 1
    }
  }
}
</script>

<style scoped lang="scss">
.dashboard-comments {
  .comment-author {
    .author-info {
      strong {
        font-size: 0.9rem;
      }
      
      small {
        font-size: 0.8rem;
      }
    }
  }
  
  .comment-content {
    p {
      font-size: 0.9rem;
      line-height: 1.4;
    }
  }
  
  .form-check-input {
    &:checked {
      background-color: var(--primary-blue);
      border-color: var(--primary-blue);
    }
  }
  
  .badge {
    font-size: 0.75rem;
    font-weight: 500;
  }
}

// Responsive adjustments
@media (max-width: 768px) {
  .dashboard-comments {
    .table {
      .table-row {
        .row {
          .col-1, .col-2, .col-3, .col-4 {
            margin-bottom: 0.5rem;
          }
        }
      }
    }
  }
}
</style>
