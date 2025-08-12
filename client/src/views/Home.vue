<template>
  <div class="home-page">
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="container">
        <div v-if="featuredPost" class="hero-content">
          <div class="row align-items-center">
            <div class="col-lg-6">
              <div class="hero-text">
                <div class="category-tag mb-3">{{ featuredPost.category || 'Featured' }}</div>
                <h1 class="hero-title">{{ featuredPost.title }}</h1>
                <p class="hero-subtitle">{{ featuredPost.excerpt }}</p>
                <div class="hero-meta mb-4">
                  <span class="hero-author">
                    <i class="bi bi-person-circle me-1"></i>
                    {{ featuredPost.author }}
                  </span>
                  <span class="hero-date">
                    <i class="bi bi-calendar3 me-1"></i>
                    {{ formatDate(featuredPost.created_at) }}
                  </span>
                </div>
                <router-link 
                  :to="{ name: 'PostDetail', params: { id: featuredPost.id } }"
                  class="btn btn-primary btn-lg"
                >
                  Read More
                  <i class="bi bi-arrow-right ms-2"></i>
                </router-link>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="hero-image">
                <img 
                  :src="featuredPost.featured_image || '/placeholder-hero.jpg'" 
                  :alt="featuredPost.title"
                  class="img-fluid rounded"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Search and Filter Section -->
    <section class="search-filter-section">
      <div class="container">
        <div class="row">
          <div class="col-lg-8">
            <div class="search-box">
              <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0">
                  <i class="bi bi-search"></i>
                </span>
                <input 
                  type="text" 
                  class="form-control border-start-0" 
                  placeholder="Search posts..."
                  v-model="searchQuery"
                  @input="handleSearch"
                />
              </div>
            </div>
          </div>
          <div class="col-lg-4">
            <select 
              class="form-select" 
              v-model="selectedCategory"
              @change="handleCategoryChange"
            >
              <option value="">All Categories</option>
              <option 
                v-for="category in categories" 
                :key="category.id" 
                :value="category.id"
              >
                {{ category.name }}
              </option>
            </select>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Posts Grid Section -->
    <section class="posts-section">
      <div class="container">
        <!-- Loading State -->
        <div v-if="loading" class="loading-spinner">
          <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
        
        <!-- Posts Grid -->
        <div v-else-if="filteredPosts.length > 0">
          <div class="post-grid">
            <PostCard 
              v-for="post in paginatedPosts" 
              :key="post.id" 
              :post="post"
            />
          </div>
          
          <!-- Pagination -->
          <div class="pagination-container">
            <nav v-if="totalPages > 1" aria-label="Posts pagination">
              <ul class="pagination justify-content-center">
                <li class="page-item" :class="{ disabled: currentPage === 1 }">
                  <a class="page-link" href="#" @click.prevent="changePage(currentPage - 1)">
                    <i class="bi bi-chevron-left"></i>
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
                    <i class="bi bi-chevron-right"></i>
                  </a>
                </li>
              </ul>
            </nav>
          </div>
        </div>
        
        <!-- Empty State -->
        <div v-else class="empty-state">
          <div class="empty-icon">
            <i class="bi bi-search"></i>
          </div>
          <h3>No posts found</h3>
          <p>Try adjusting your search or filter criteria.</p>
          <button @click="clearFilters" class="btn btn-outline-primary">
            Clear Filters
          </button>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import PostCard from '../components/PostCard.vue'
import { blogAPI } from '../api.js'

export default {
  name: 'Home',
  components: {
    PostCard
  },
  data() {
    return {
      posts: [],
      categories: [],
      loading: true,
      currentPage: 1,
      totalPages: 1,
      postsPerPage: 9,
      searchQuery: '',
      selectedCategory: '',
      featuredPost: null
    }
  },
  computed: {
    filteredPosts() {
      let filtered = this.posts
      
      // Filter by search query
      if (this.searchQuery.trim()) {
        const query = this.searchQuery.toLowerCase()
        filtered = filtered.filter(post => 
          post.title.toLowerCase().includes(query) ||
          post.excerpt.toLowerCase().includes(query) ||
          post.author.toLowerCase().includes(query)
        )
      }
      
      // Filter by category
      if (this.selectedCategory) {
        filtered = filtered.filter(post => post.category_id == this.selectedCategory)
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
        // const response = await blogAPI.getPosts()
        // this.posts = response.data.posts
        // this.featuredPost = this.posts[0] // Set first post as featured
        
        // Mock data for now
        this.posts = [
          {
            id: 1,
            title: 'Getting Started with Vue 3: A Comprehensive Guide',
            excerpt: 'Learn the basics of Vue 3 and how to build modern web applications with the Composition API, improved performance, and better TypeScript support.',
            author: 'John Doe',
            category: 'Technology',
            category_id: 1,
            featured_image: 'https://images.unsplash.com/photo-1555066932-e78dd8fb77bb?w=800',
            created_at: '2024-01-15T10:00:00Z',
            views: 1250,
            comments_count: 23,
            likes_count: 89
          },
          {
            id: 2,
            title: 'Bootstrap 5 Best Practices for Modern Web Design',
            excerpt: 'Discover the best practices for using Bootstrap 5 in your projects, including responsive design, custom theming, and performance optimization.',
            author: 'Jane Smith',
            category: 'Design',
            category_id: 2,
            featured_image: 'https://images.unsplash.com/photo-1547658719-da2b51169166?w=800',
            created_at: '2024-01-14T15:30:00Z',
            views: 890,
            comments_count: 15,
            likes_count: 67
          },
          {
            id: 3,
            title: 'The Future of Web Development: Trends to Watch in 2024',
            excerpt: 'Explore the latest trends in web development, from AI-powered tools to new frameworks and technologies that will shape the industry.',
            author: 'Mike Johnson',
            category: 'Development',
            category_id: 3,
            featured_image: 'https://images.unsplash.com/photo-1518709268805-4e9042af2176?w=800',
            created_at: '2024-01-13T09:15:00Z',
            views: 2100,
            comments_count: 45,
            likes_count: 156
          },
          {
            id: 4,
            title: 'Building Scalable APIs with Node.js and Express',
            excerpt: 'Learn how to build robust and scalable APIs using Node.js and Express, including authentication, validation, and database integration.',
            author: 'Sarah Wilson',
            category: 'Development',
            category_id: 3,
            featured_image: 'https://images.unsplash.com/photo-1555066932-e78dd8fb77bb?w=800',
            created_at: '2024-01-12T14:20:00Z',
            views: 980,
            comments_count: 18,
            likes_count: 72
          },
          {
            id: 5,
            title: 'CSS Grid vs Flexbox: When to Use Each',
            excerpt: 'Understand the differences between CSS Grid and Flexbox, and learn when to use each layout system for optimal web design.',
            author: 'Alex Brown',
            category: 'Design',
            category_id: 2,
            featured_image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=800',
            created_at: '2024-01-11T11:45:00Z',
            views: 750,
            comments_count: 12,
            likes_count: 54
          },
          {
            id: 6,
            title: 'Introduction to React Hooks: A Beginner\'s Guide',
            excerpt: 'Master React Hooks with this comprehensive guide covering useState, useEffect, useContext, and custom hooks for modern React development.',
            author: 'Emily Davis',
            category: 'Technology',
            category_id: 1,
            featured_image: 'https://images.unsplash.com/photo-1633356122544-f134324a6cee?w=800',
            created_at: '2024-01-10T16:30:00Z',
            views: 1650,
            comments_count: 28,
            likes_count: 98
          }
        ]
        
        this.featuredPost = this.posts[0] // Set first post as featured
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
    handleSearch() {
      this.currentPage = 1 // Reset to first page when searching
    },
    handleCategoryChange() {
      this.currentPage = 1 // Reset to first page when changing category
    },
    changePage(page) {
      if (page >= 1 && page <= this.totalPages) {
        this.currentPage = page
        // Scroll to top of posts section
        document.querySelector('.posts-section').scrollIntoView({ 
          behavior: 'smooth' 
        })
      }
    },
    clearFilters() {
      this.searchQuery = ''
      this.selectedCategory = ''
      this.currentPage = 1
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

<style scoped lang="scss">
.home-page {
  .hero-section {
    background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%);
    padding: var(--section-spacing) 0;
    margin-bottom: var(--section-spacing);
    
    .hero-content {
      .hero-text {
        .hero-title {
          font-size: 2.5rem;
          font-weight: 700;
          margin-bottom: 1rem;
          color: var(--text-primary);
          line-height: 1.2;
        }
        
        .hero-subtitle {
          font-size: 1.1rem;
          color: var(--text-secondary);
          margin-bottom: 1.5rem;
          line-height: 1.6;
        }
        
        .hero-meta {
          display: flex;
          gap: 1.5rem;
          font-size: 0.9rem;
          color: var(--text-muted);
          
          .hero-author,
          .hero-date {
            display: flex;
            align-items: center;
            
            i {
              font-size: 1rem;
            }
          }
        }
      }
      
      .hero-image {
        img {
          width: 100%;
          height: 400px;
          object-fit: cover;
          box-shadow: var(--shadow-lg);
        }
      }
    }
  }
  
  .search-filter-section {
    background-color: var(--bg-secondary);
    padding: var(--component-spacing);
    border-radius: 0.75rem;
    margin-bottom: var(--card-spacing);
    
    .search-box {
      .input-group {
        .input-group-text {
          border-color: var(--border-color);
          color: var(--text-muted);
        }
        
        .form-control {
          border-color: var(--border-color);
          
          &:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.2rem rgba(61, 169, 252, 0.25);
          }
        }
      }
    }
    
    .form-select {
      border-color: var(--border-color);
      
      &:focus {
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 0.2rem rgba(61, 169, 252, 0.25);
      }
    }
  }
  
  .posts-section {
    .post-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
      gap: var(--card-spacing);
      margin-bottom: var(--section-spacing);
    }
    
    .pagination-container {
      margin-top: var(--card-spacing);
    }
  }
}

// Responsive design
@media (max-width: 768px) {
  .home-page {
    .hero-section {
      .hero-content {
        .hero-text {
          .hero-title {
            font-size: 2rem;
          }
        }
        
        .hero-image {
          margin-top: 2rem;
          
          img {
            height: 300px;
          }
        }
      }
    }
    
    .post-grid {
      grid-template-columns: 1fr;
    }
  }
}
</style>

