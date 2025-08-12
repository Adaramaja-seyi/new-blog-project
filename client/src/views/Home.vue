<template>
  <div class="container">
    <!-- Hero Section -->
    <div class="row mb-5">
      <div class="col-lg-8 mx-auto text-center">
        <h1 class="display-4 mb-4">Welcome to Blog Platform</h1>
        <p class="lead text-muted">
          Discover amazing stories, insights, and ideas from our community of writers.
        </p>
      </div>
    </div>
    
    <!-- Blog Posts Section -->
    <div class="row">
      <div class="col-lg-8">
        <h2 class="mb-4">Latest Posts</h2>
        
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
        
        <!-- Posts List -->
        <div v-else-if="posts.length > 0">
          <div 
            v-for="post in posts" 
            :key="post.id" 
            class="card mb-4"
          >
            <div class="card-body">
              <h3 class="card-title">
                <router-link 
                  :to="{ name: 'PostDetail', params: { id: post.id } }"
                  class="text-decoration-none"
                >
                  {{ post.title }}
                </router-link>
              </h3>
              <p class="card-text text-muted">
                {{ post.excerpt }}
              </p>
              <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                  By {{ post.author }} • {{ formatDate(post.created_at) }}
                </small>
                <router-link 
                  :to="{ name: 'PostDetail', params: { id: post.id } }"
                  class="btn btn-outline-primary btn-sm"
                >
                  Read More
                </router-link>
              </div>
            </div>
          </div>
          
          <!-- Pagination -->
          <nav v-if="totalPages > 1" aria-label="Posts pagination">
            <ul class="pagination justify-content-center">
              <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <a class="page-link" href="#" @click.prevent="changePage(currentPage - 1)">
                  Previous
                </a>
              </li>
              <li 
                v-for="page in visiblePages" 
                :key="page"
                class="page-item"
                :class="{ active: page === currentPage }"
              >
                <a class="page-link" href="#" @click.prevent="changePage(page)">
                  {{ page }}
                </a>
              </li>
              <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                <a class="page-link" href="#" @click.prevent="changePage(currentPage + 1)">
                  Next
                </a>
              </li>
            </ul>
          </nav>
        </div>
        
        <!-- Empty State -->
        <div v-else class="text-center py-5">
          <h3 class="text-muted">No posts found</h3>
          <p class="text-muted">Check back later for new content!</p>
        </div>
      </div>
      
      <!-- Sidebar -->
      <div class="col-lg-4">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">Categories</h5>
          </div>
          <div class="card-body">
            <ul class="list-unstyled">
              <li v-for="category in categories" :key="category.id" class="mb-2">
                <a href="#" class="text-decoration-none text-muted">
                  {{ category.name }}
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { blogAPI } from '../api.js'

export default {
  name: 'Home',
  data() {
    return {
      posts: [],
      categories: [],
      loading: true,
      currentPage: 1,
      totalPages: 1,
      postsPerPage: 10
    }
  },
  computed: {
    visiblePages() {
      const pages = []
      const start = Math.max(1, this.currentPage - 2)
      const end = Math.min(this.totalPages, this.currentPage + 2)
      
      for (let i = start; i <= end; i++) {
        pages.push(i)
      }
      return pages
    }
  },
  mounted() {
    this.fetchPosts()
    this.fetchCategories()
  },
  methods: {
    async fetchPosts() {
      try {
        this.loading = true
        // TODO: Implement API call to fetch posts
        // const response = await blogAPI.getPosts(this.currentPage, this.postsPerPage)
        // this.posts = response.data.posts
        // this.totalPages = response.data.totalPages
        
        // Mock data for now
        this.posts = [
          {
            id: 1,
            title: 'Getting Started with Vue 3',
            excerpt: 'Learn the basics of Vue 3 and how to build modern web applications...',
            author: 'John Doe',
            created_at: '2024-01-15T10:00:00Z'
          },
          {
            id: 2,
            title: 'Bootstrap 5 Best Practices',
            excerpt: 'Discover the best practices for using Bootstrap 5 in your projects...',
            author: 'Jane Smith',
            created_at: '2024-01-14T15:30:00Z'
          }
        ]
        this.totalPages = 1
      } catch (error) {
        console.error('Error fetching posts:', error)
      } finally {
        this.loading = false
      }
    },
    async fetchCategories() {
      try {
        // TODO: Implement API call to fetch categories
        // const response = await blogAPI.getCategories()
        // this.categories = response.data
        
        // Mock data for now
        this.categories = [
          { id: 1, name: 'Technology' },
          { id: 2, name: 'Design' },
          { id: 3, name: 'Development' }
        ]
      } catch (error) {
        console.error('Error fetching categories:', error)
      }
    },
    changePage(page) {
      if (page >= 1 && page <= this.totalPages) {
        this.currentPage = page
        this.fetchPosts()
      }
    },
    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }
  }
}
</script>

<style scoped>
.card-title a {
  color: var(--text-primary);
  transition: color 0.2s ease;
}

.card-title a:hover {
  color: var(--primary-color);
}

.pagination .page-link {
  color: var(--primary-color);
  border-color: var(--border-color);
}

.pagination .page-item.active .page-link {
  background-color: var(--primary-color);
  border-color: var(--primary-color);
}
</style>

