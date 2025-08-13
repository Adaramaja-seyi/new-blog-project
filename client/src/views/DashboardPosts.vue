<template>
  <div class="dashboard-posts">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2>My Posts</h2>
        <p class="text-muted mb-0">Manage your blog posts and drafts</p>
      </div>
      <router-link to="/dashboard/create" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>
        Create New Post
      </router-link>
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-md-4 mb-3 mb-md-0">
            <label class="form-label">Status Filter</label>
            <select v-model="statusFilter" class="form-select">
              <option value="">All Posts</option>
              <option value="published">Published</option>
              <option value="draft">Drafts</option>
              <option value="pending">Pending</option>
            </select>
          </div>
          <div class="col-md-4 mb-3 mb-md-0">
            <label class="form-label">Category</label>
            <select v-model="categoryFilter" class="form-select">
              <option value="">All Categories</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Search</label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="bi bi-search"></i>
              </span>
              <input 
                type="text" 
                class="form-control" 
                placeholder="Search posts..."
                v-model="searchQuery"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Posts Table -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Posts ({{ filteredPosts.length }})</h5>
        <div class="d-flex gap-2">
          <button 
            @click="bulkAction('publish')" 
            :disabled="!selectedPosts.length"
            class="btn btn-outline-success btn-sm"
          >
            <i class="bi bi-check-circle me-1"></i>
            Publish Selected
          </button>
          <button 
            @click="bulkAction('delete')" 
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
        
        <div v-else-if="filteredPosts.length > 0" class="table">
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
              <div class="col-4">Title</div>
              <div class="col-2">Category</div>
              <div class="col-2">Status</div>
              <div class="col-1">Views</div>
              <div class="col-2">Actions</div>
            </div>
          </div>
          <div class="table-body">
            <div 
              v-for="post in paginatedPosts" 
              :key="post.id" 
              class="table-row"
            >
              <div class="row align-items-center">
                <div class="col-1">
                  <input 
                    type="checkbox" 
                    class="form-check-input"
                    :value="post.id"
                    v-model="selectedPosts"
                  />
                </div>
                <div class="col-4">
                  <h6 class="mb-1">{{ post.title }}</h6>
                  <small class="text-muted">{{ formatDate(post.created_at) }}</small>
                </div>
                <div class="col-2">
                  <span class="badge bg-light text-dark">{{ post.category?.name || 'Uncategorized' }}</span>
                </div>
                <div class="col-2">
                  <span class="badge" :class="getStatusClass(post.status)">
                    {{ post.status }}
                  </span>
                </div>
                <div class="col-1">
                  <span class="text-muted">{{ post.views || 0 }}</span>
                </div>
                <div class="col-2">
                  <div class="btn-group btn-group-sm">
                    <router-link 
                      :to="{ name: 'PostDetail', params: { slug: post.slug } }"
                      class="btn btn-light"
                      title="View"
                    >
                      <i class="bi bi-eye"></i>
                    </router-link>
                    <router-link 
                      :to="{ name: 'CreatePost', query: { edit: post.id } }"
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
          <h5>No posts found</h5>
          <p>Create your first blog post to get started!</p>
          <router-link to="/dashboard/create" class="btn btn-primary">
            Create Your First Post
          </router-link>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="filteredPosts.length > postsPerPage" class="d-flex justify-content-center mt-4">
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
  </div>
</template>

<script>
import { blogAPI } from '@/api'
import { useCategoriesStore } from '@/stores/categories'

export default {
  name: 'DashboardPosts',
  data() {
    return {
      loading: false,
      posts: [],
      selectedPosts: [],
      statusFilter: '',
      categoryFilter: '',
      searchQuery: '',
      currentPage: 1,
      postsPerPage: 10,
      categories: []
    }
  },
  computed: {
    filteredPosts() {
      let filtered = this.posts

      if (this.statusFilter) {
        filtered = filtered.filter(post => post.status === this.statusFilter)
      }

      if (this.categoryFilter) {
        filtered = filtered.filter(post => post.category_id == this.categoryFilter)
      }

      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase()
        filtered = filtered.filter(post => 
          post.title.toLowerCase().includes(query) ||
          (post.excerpt && post.excerpt.toLowerCase().includes(query)) ||
          (post.content && post.content.toLowerCase().includes(query))
        )
      }

      return filtered
    },
    paginatedPosts() {
      const start = (this.currentPage - 1) * this.postsPerPage
      const end = start + this.postsPerPage
      return this.filteredPosts.slice(start, end)
    },
    totalPages() {
      return Math.ceil(this.filteredPosts.length / this.postsPerPage)
    },
    allSelected() {
      return this.paginatedPosts.length > 0 && 
             this.paginatedPosts.every(post => this.selectedPosts.includes(post.id))
    }
  },
  mounted() {
    this.loadPosts()
    this.loadCategories()
  },
  methods: {
    async loadPosts() {
      this.loading = true
      try {
        const filters = {}
        if (this.statusFilter) filters.status = this.statusFilter
        if (this.categoryFilter) filters.category_id = this.categoryFilter
        if (this.searchQuery) filters.search = this.searchQuery

        const response = await blogAPI.getDashboardPosts(filters)
        this.posts = response.data
      } catch (error) {
        console.error('Error loading posts:', error)
        // Handle error - could show a toast notification
      } finally {
        this.loading = false
      }
    },
    async loadCategories() {
      try {
        const categoriesStore = useCategoriesStore()
        await categoriesStore.fetchCategories()
        this.categories = categoriesStore.categories
      } catch (error) {
        console.error('Error loading categories:', error)
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
          await blogAPI.deletePost(postId)
          this.posts = this.posts.filter(post => post.id !== postId)
          this.selectedPosts = this.selectedPosts.filter(id => id !== postId)
        } catch (error) {
          console.error('Error deleting post:', error)
          alert('Failed to delete post. Please try again.')
        }
      }
    },
    toggleSelectAll() {
      if (this.allSelected) {
        this.selectedPosts = []
      } else {
        this.selectedPosts = this.paginatedPosts.map(post => post.id)
      }
    },
    async bulkAction(action) {
      if (!this.selectedPosts.length) return

      if (action === 'delete') {
        if (confirm(`Are you sure you want to delete ${this.selectedPosts.length} posts?`)) {
          try {
            await blogAPI.bulkDeletePosts({ ids: this.selectedPosts })
            this.posts = this.posts.filter(post => !this.selectedPosts.includes(post.id))
            this.selectedPosts = []
          } catch (error) {
            console.error('Error deleting posts:', error)
            alert('Failed to delete some posts. Please try again.')
          }
        }
      } else if (action === 'publish') {
        try {
          await blogAPI.bulkUpdatePosts({ 
            ids: this.selectedPosts, 
            status: 'published' 
          })
          this.posts = this.posts.map(post => {
            if (this.selectedPosts.includes(post.id)) {
              return { ...post, status: 'published' }
            }
            return post
          })
          this.selectedPosts = []
        } catch (error) {
          console.error('Error publishing posts:', error)
          alert('Failed to publish some posts. Please try again.')
        }
      }
    }
  },
  watch: {
    statusFilter() {
      this.currentPage = 1
      this.loadPosts()
    },
    categoryFilter() {
      this.currentPage = 1
      this.loadPosts()
    },
    searchQuery() {
      this.currentPage = 1
      // Debounce search to avoid too many API calls
      clearTimeout(this.searchTimeout)
      this.searchTimeout = setTimeout(() => {
        this.loadPosts()
      }, 500)
    }
  }
}
</script>

<style scoped lang="scss">
.dashboard-posts {
  .table {
    .table-row {
      &:hover {
        background-color: var(--bg-secondary);
      }
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
  .dashboard-posts {
    .table {
      .table-row {
        .row {
          .col-2, .col-1 {
            margin-bottom: 0.5rem;
          }
        }
      }
    }
  }
}
</style>
