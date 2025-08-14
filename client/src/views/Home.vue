<template>
  <div class="home-page">
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="container">
        <div class="hero-content text-center py-5">
          <h1 class="hero-title mb-3">Welcome to Zura Blog</h1>
          <p class="hero-description lead mb-4">
            Discover insightful articles, tips, and stories from our vibrant community.
            Whether you're here to learn, share, or connect, Zura Blog is your space for
            inspiration and growth.
          </p>
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
            <PostCard v-for="post in paginatedPosts" :key="post.id" :post="post" />
          </div>

          <!-- Pagination -->
          <div class="pagination-container">
            <nav v-if="totalPages > 1" aria-label="Posts pagination">
              <ul class="pagination justify-content-center">
                <li class="page-item" :class="{ disabled: currentPage === 1 }">
                  <a
                    class="page-link"
                    href="#"
                    @click.prevent="changePage(currentPage - 1)"
                  >
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
                  <a
                    class="page-link"
                    href="#"
                    @click.prevent="changePage(currentPage + 1)"
                  >
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
import PostCard from "../components/PostCard.vue";
import { blogAPI } from "../api.js";

export default {
  name: "Home",
  components: {
    PostCard,
  },
  data() {
    return {
      posts: [],
      categories: [],
      loading: true,
      currentPage: 1,
      totalPages: 1,
      postsPerPage: 9,
      searchQuery: "",
      selectedCategory: "",
      featuredPost: null,
    };
  },
  computed: {
    filteredPosts() {
      let filtered = this.posts;

      // Filter by search query
      if (this.searchQuery.trim()) {
        const query = this.searchQuery.toLowerCase();
        filtered = filtered.filter(
          (post) =>
            post.title.toLowerCase().includes(query) ||
            (post.excerpt && post.excerpt.toLowerCase().includes(query)) ||
            (post.user?.name && post.user.name.toLowerCase().includes(query)) ||
            (post.author && post.author.toLowerCase().includes(query))
        );
      }

      // Filter by category
      if (this.selectedCategory) {
        filtered = filtered.filter((post) => post.category.id == this.selectedCategory);
      }

      return filtered;
    },
    paginatedPosts() {
      const start = (this.currentPage - 1) * this.postsPerPage;
      const end = start + this.postsPerPage;
      return this.filteredPosts.slice(start, end);
    },
    totalPages() {
      return Math.ceil(this.filteredPosts.length / this.postsPerPage);
    },
    visiblePages() {
      const pages = [];
      const start = Math.max(1, this.currentPage - 2);
      const end = Math.min(this.totalPages, this.currentPage + 2);

      for (let i = start; i <= end; i++) {
        pages.push(i);
      }
      return pages;
    },
  },
  mounted() {
    this.fetchPosts();
    this.fetchCategories();
  },
  methods: {
    async fetchPosts() {
      try {
        this.loading = true;
        // Always fetch all posts, no filters
        const response = await blogAPI.getPosts();
        this.posts = response.data.data;
        // Set the most recent or most viewed post as featured
        if (this.posts.length > 0) {
          this.featuredPost =
            this.posts.find((post) => post.is_featured) || this.posts[0];
        }
      } catch (error) {
        console.error("Error fetching posts:", error);
        this.posts = [];
        this.featuredPost = null;
      } finally {
        this.loading = false;
      }
    },
    async fetchCategories() {
      try {
        const response = await blogAPI.getCategories();
        // Support both { data: [...] } and [...]
        if (Array.isArray(response.data)) {
          this.categories = response.data;
        } else if (response.data && Array.isArray(response.data.data)) {
          this.categories = response.data.data;
        } else {
          this.categories = [];
        }
      } catch (error) {
        console.error("Error fetching categories:", error);
        this.categories = [];
      }
    },
    handleSearch() {
      this.currentPage = 1; // Reset to first page when searching
    },
    handleCategoryChange() {
      this.currentPage = 1; // Reset to first page when changing category
    },
    changePage(page) {
      if (page >= 1 && page <= this.totalPages) {
        this.currentPage = page;
        // Scroll to top of posts section
        document.querySelector(".posts-section").scrollIntoView({
          behavior: "smooth",
        });
      }
    },
    clearFilters() {
      this.searchQuery = "";
      this.selectedCategory = "";
      this.currentPage = 1;
      this.fetchPosts();
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
