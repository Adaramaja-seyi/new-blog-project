<template>
  <div>
    <!-- Hero Section -->
    <section class="hero-section" v-if="!isAuthenticated">
      <div class="hero-background">
        <div class="floating-shapes">
          <div class="shape shape-1"></div>
          <div class="shape shape-2"></div>
          <div class="shape shape-3"></div>
        </div>
      </div>
      <div class="container">
        <div class="hero-content text-center" style="padding: 70px">
          <div class="hero-badge mb-3">
            <span class="badge-text">✨ Welcome to the Future of Blogging</span>
          </div>
          <h1 class="hero-title mb-3">
            Discover Stories That
            <span class="gradient-text">Inspire</span>
          </h1>
          <p class="hero-description lead mb-4">
            Join thousands of readers exploring insightful articles, expert
            tips, and captivating stories. Your journey of inspiration and
            growth starts here.
          </p>
          <div class="hero-stats d-flex justify-content-center gap-4 mb-4">
            <div class="stat-item">
              <div class="stat-number">10K+</div>
              <div class="stat-label">Readers</div>
            </div>
            <div class="stat-item">
              <div class="stat-number">500+</div>
              <div class="stat-label">Articles</div>
            </div>
            <div class="stat-item">
              <div class="stat-number">50+</div>
              <div class="stat-label">Categories</div>
            </div>
          </div>
          <button class="cta-button" @click="scrollToPosts('post-section')">
            <span>Start Exploring</span>
            <i class="bi bi-arrow-down"></i>
          </button>
        </div>
      </div>
    </section>

    <!-- Search and Filter Section -->
    <section class="search-filter-section" id="post-section">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">
            <span class="title-icon">📚</span>
            Explore Our Posts
            <span class="title-underline"></span>
          </h2>
          <p class="section-subtitle">
            Discover amazing content tailored just for you
          </p>
        </div>

        <div class="search-filter-container">
          <div class="row align-items-center">
            <div class="col-lg-8 col-md-7">
              <div class="search-box-wrapper">
                <div class="search-box">
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="bi bi-search search-icon"></i>
                    </span>
                    <input
                      type="text"
                      class="form-control"
                      placeholder="Search for articles, topics, authors..."
                      v-model="searchQuery"
                      @input="handleSearch"
                    />
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-5">
              <div class="filter-wrapper">
                <select
                  class="form-select category-select"
                  v-model="selectedCategory"
                  @change="handleCategoryChange"
                >
                  <option value="">🌟 All Categories</option>
                  <option
                    v-for="category in categories"
                    :key="category.id"
                    :value="category.id"
                  >
                    📁 {{ category.name }}
                  </option>
                </select>
              </div>
            </div>
          </div>

          <!-- Active Filters Display -->
          <div v-if="searchQuery || selectedCategory" class="active-filters">
            <span class="filter-label">Active filters:</span>
            <span v-if="searchQuery" class="filter-tag">
              <i class="bi bi-search"></i>
              "{{ searchQuery }}"
              <button @click="searchQuery = ''" class="filter-remove">×</button>
            </span>
            <span v-if="selectedCategory" class="filter-tag">
              <i class="bi bi-folder"></i>
              {{ selectedCategory }}
              <button @click="selectedCategory = ''" class="filter-remove">
                ×
              </button>
            </span>
            <button @click="clearFilters" class="clear-all-btn">
              Clear all
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Posts Grid Section -->
    <section class="posts-section">
      <div class="container">
        <!-- Loading State -->
        <div v-if="loading" class="loading-spinner text-center py-5">
          <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>

        <!-- Posts Grid -->
        <div v-else-if="filteredPosts.length > 0" class="row">
          <div
            v-for="post in filteredPosts"
            :key="post.id"
            class="col-lg-3 col-md-6 col-sm-12 mb-4"
          >
            <PostCard :post="post" />
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="empty-state text-center py-5">
          <div class="empty-icon mb-3">
            <i class="bi bi-search fs-1"></i>
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
import { useAuth } from "../stores/auth.js";

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
      searchQuery: "",
      selectedCategory: "",
      featuredPost: null,
    };
  },
  computed: {
    auth() {
      return useAuth();
    },
    isAuthenticated() {
      return this.auth?.isLoggedIn;
    },
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
        filtered = filtered.filter(
          (post) => post.category.id == this.selectedCategory
        );
      }

      return filtered;
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
        const response = await blogAPI.getPosts();

        this.posts = response.data.data;
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
    clearFilters() {
      this.searchQuery = "";
      this.selectedCategory = "";
      this.fetchPosts();
    },
    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
      });
    },

    scrollToPosts(sectionId) {
      const element = document.getElementById(sectionId);
      if (element) {
        element.scrollIntoView({ behavior: "smooth" });
      }
    },
  },
};
</script>

<style scoped lang="scss">
.hero-section {
  position: relative;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  min-height: 100vh;
  display: flex;
  align-items: center;
  overflow: hidden;
  color: white;

  .hero-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    opacity: 0.1;
  }

  .floating-shapes {
    position: absolute;
    width: 100%;
    height: 100%;

    .shape {
      position: absolute;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.1);
      animation: float 6s ease-in-out infinite;

      &.shape-1 {
        width: 80px;
        height: 80px;
        top: 20%;
        left: 10%;
        animation-delay: 0s;
      }

      &.shape-2 {
        width: 120px;
        height: 120px;
        top: 60%;
        right: 15%;
        animation-delay: 2s;
      }

      &.shape-3 {
        width: 60px;
        height: 60px;
        top: 10%;
        right: 30%;
        animation-delay: 4s;
      }
    }
  }

  .hero-content {
    position: relative;
    z-index: 2;
  }

  .hero-badge {
    .badge-text {
      background: rgba(255, 255, 255, 0.2);
      padding: 8px 20px;
      border-radius: 25px;
      font-size: 0.9rem;
      font-weight: 500;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.3);
    }
  }

  .hero-title {
    font-size: 3.2rem;
    font-weight: 800;
    line-height: 1.2;
    margin-bottom: 1.5rem;

    .gradient-text {
      background: linear-gradient(45deg, #ff6b35, #ffd700);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
  }

  .hero-description {
    max-width: 650px;
    margin: 0 auto 2rem;
    font-size: 1.2rem;
    opacity: 0.9;
    line-height: 1.6;
    background: linear-gradient(45deg, #ff6b35, #ffd700);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .hero-stats {
    margin-bottom: 2rem;

    .stat-item {
      text-align: center;

      .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #ffd89b;
        line-height: 1;
      }

      .stat-label {
        font-size: 0.9rem;
        opacity: 0.8;
        margin-top: 4px;
      }
    }
  }

  .cta-button {
    background: linear-gradient(45deg, #ff6b6b, #ee5a24);
    border: none;
    padding: 15px 35px;
    border-radius: 50px;
    color: white;
    font-weight: 600;
    font-size: 1.1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 8px 25px rgba(238, 90, 36, 0.3);
    display: inline-flex;
    align-items: center;
    gap: 10px;

    &:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 35px rgba(238, 90, 36, 0.4);
    }

    &:active {
      transform: translateY(-1px);
    }

    i {
      animation: bounce 2s infinite;
    }
  }
}

@keyframes float {
  0%,
  100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-20px);
  }
}

@keyframes bounce {
  0%,
  20%,
  50%,
  80%,
  100% {
    transform: translateY(0);
  }
  40% {
    transform: translateY(-5px);
  }
  60% {
    transform: translateY(-3px);
  }
}

// Enhanced Search Filter Section
.search-filter-section {
  padding: 80px 0 60px;
  background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
  border-bottom: 1px solid #e8ecf4;

  .section-header {
    text-align: center;
    margin-bottom: 50px;

    .section-title {
      font-size: 2.8rem;
      font-weight: 800;
      color: #2d3436;
      margin-bottom: 15px;
      position: relative;
      display: inline-flex;
      align-items: center;
      gap: 15px;

      .title-icon {
        font-size: 2.2rem;
        opacity: 0.8;
      }

      .title-underline {
        position: absolute;
        bottom: -8px;
        left: 50%;
        transform: translateX(-50%);
        width: 100px;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2);
        border-radius: 2px;
      }
    }

    .section-subtitle {
      font-size: 1.1rem;
      color: #636e72;
      font-weight: 400;
      margin: 0;
    }
  }

  .search-filter-container {
    max-width: 900px;
    margin: 0 auto;

    .search-box-wrapper {
      position: relative;
    }

    .search-box {
      .input-group {
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.1);
        border-radius: 30px;
        overflow: hidden;
        background: white;

        .input-group-text {
          background: white;
          border: none;
          padding: 18px 20px;
          border-right: 1px solid #e8ecf4;

          .search-icon {
            color: #667eea;
            font-size: 1.1rem;
          }
        }

        .form-control {
          border: none;
          padding: 18px 25px;
          font-size: 1rem;
          background: white;

          &:focus {
            box-shadow: none;
            outline: none;
          }

          &::placeholder {
            color: #a0a6b8;
            font-weight: 400;
          }
        }

        &:focus-within {
          box-shadow: 0 10px 40px rgba(102, 126, 234, 0.2);
          transform: translateY(-2px);
        }
      }
    }

    .filter-wrapper {
      .category-select {
        background: white;
        border: none;
        border-radius: 30px;
        padding: 18px 25px;
        font-size: 1rem;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.1);
        cursor: pointer;
        transition: all 0.3s ease;

        &:focus {
          box-shadow: 0 10px 40px rgba(102, 126, 234, 0.2);
          transform: translateY(-2px);
          outline: none;
        }

        option {
          padding: 10px;
          font-size: 1rem;
        }
      }
    }

    .active-filters {
      margin-top: 25px;
      padding: 20px;
      background: rgba(102, 126, 234, 0.05);
      border-radius: 20px;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 10px;

      .filter-label {
        font-weight: 600;
        color: #2d3436;
        margin-right: 10px;
        font-size: 0.9rem;
      }

      .filter-tag {
        background: #667eea;
        color: white;
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 5px;

        i {
          font-size: 0.8rem;
        }

        .filter-remove {
          background: none;
          border: none;
          color: white;
          cursor: pointer;
          margin-left: 5px;
          font-size: 1.2rem;
          line-height: 1;
          opacity: 0.8;
          transition: opacity 0.2s;

          &:hover {
            opacity: 1;
          }
        }
      }

      .clear-all-btn {
        background: #e74c3c;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.2s;

        &:hover {
          background: #c0392b;
        }
      }
    }
  }
}

// Enhanced Posts Section
.posts-section {
  padding: 60px 0 100px;
  background: #fafbfc;

  .results-summary {
    margin-bottom: 30px;
    padding: 0 5px;

    .results-text {
      font-size: 1rem;
      color: #636e72;
      font-weight: 500;
      background: white;
      padding: 12px 20px;
      border-radius: 25px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
      display: inline-block;
    }
  }
}

@keyframes wave {
  0%,
  40%,
  100% {
    transform: scaleY(0.4);
  }
  20% {
    transform: scaleY(1);
  }
}

// Mobile Responsiveness
@media (max-width: 768px) {
  .hero-badge {
    .badge-text {
      background: rgba(255, 255, 255, 0.2);
      padding: 4px 10px;
      border-radius: 25px;
      font-size: 0.4rem;
      font-weight: 500;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.3);
    }
  }
  .hero-section {
    min-height: 50vh;

    .hero-title {
      font-size: 2.2rem;
    }

    .hero-description {
      font-size: 1rem;
    }

    .hero-stats {
      .stat-item .stat-number {
        font-size: 1.5rem;
      }
    }

    .cta-button {
      padding: 12px 25px;
      font-size: 1rem;
    }
  }

  .search-filter-section {
    padding: 60px 0 40px;

    .section-header {
      margin-bottom: 40px;

      .section-title {
        font-size: 2.2rem;
        flex-direction: column;
        gap: 10px;
      }

      .section-subtitle {
        font-size: 1rem;
      }
    }

    .search-filter-container {
      .filter-wrapper {
        margin-top: 20px;
      }

      .active-filters {
        padding: 15px;
        margin-top: 20px;

        .filter-label {
          width: 100%;
          margin-bottom: 10px;
        }
      }
    }
  }

  .posts-section {
    padding: 40px 0 60px;
  }
}

@media (max-width: 576px) {
  .search-filter-section {
    .search-filter-container {
      .search-box .input-group {
        .input-group-text,
        .form-control {
          padding: 15px 18px;
        }
      }

      .filter-wrapper .category-select {
        padding: 15px 20px;
      }
    }
  }
}
</style>
