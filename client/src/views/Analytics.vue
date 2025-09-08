<template>
  <div class="analytics-page">
    <!-- Page Header -->
    <div class="page-header mb-4">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h1 class="h3 mb-1">Analytics Dashboard</h1>
          <p class="text-muted mb-0">
            Track your blog performance and audience engagement
          </p>
        </div>
        <div class="d-flex gap-2">
          <select
            v-model="selectedPeriod"
            @change="loadAnalytics"
            class="form-select form-select-sm"
            style="width: auto"
          >
            <option value="7">Last 7 days</option>
            <option value="30">Last 30 days</option>
            <option value="90">Last 90 days</option>
            <option value="365">Last year</option>
          </select>
          <button
            @click="refreshData"
            class="btn btn-outline-primary btn-sm"
            :disabled="loading"
          >
            <i
              class="bi bi-arrow-clockwise"
              :class="{ 'spinner-border spinner-border-sm': loading }"
            ></i>
            Refresh
          </button>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Loading analytics...</span>
      </div>
    </div>

    <!-- Analytics Content -->
    <div v-else>
      <!-- Key Metrics Cards -->
      <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
          <div class="metric-card">
            <div class="metric-icon bg-primary">
              <i class="bi bi-eye"></i>
            </div>
            <div class="metric-content">
              <h3 class="metric-value">
                {{ formatNumber(analytics.totalViews) }}
              </h3>
              <p class="metric-label">Total Views</p>
              <small
                class="metric-change"
                :class="getChangeClass(analytics.viewsChange)"
              >
                <i :class="getChangeIcon(analytics.viewsChange)"></i>
                {{ formatPercentage(analytics.viewsChange) }}
              </small>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
          <div class="metric-card">
            <div class="metric-icon bg-success">
              <i class="bi bi-heart"></i>
            </div>
            <div class="metric-content">
              <h3 class="metric-value">
                {{ formatNumber(analytics.totalLikes) }}
              </h3>
              <p class="metric-label">Total Likes</p>
              <small
                class="metric-change"
                :class="getChangeClass(analytics.likesChange)"
              >
                <i :class="getChangeIcon(analytics.likesChange)"></i>
                {{ formatPercentage(analytics.likesChange) }}
              </small>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
          <div class="metric-card">
            <div class="metric-icon bg-info">
              <i class="bi bi-chat-dots"></i>
            </div>
            <div class="metric-content">
              <h3 class="metric-value">
                {{ formatNumber(analytics.totalComments) }}
              </h3>
              <p class="metric-label">Total Comments</p>
              <small
                class="metric-change"
                :class="getChangeClass(analytics.commentsChange)"
              >
                <i :class="getChangeIcon(analytics.commentsChange)"></i>
                {{ formatPercentage(analytics.commentsChange) }}
              </small>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
          <div class="metric-card">
            <div class="metric-icon bg-warning">
              <i class="bi bi-file-text"></i>
            </div>
            <div class="metric-content">
              <h3 class="metric-value">
                {{ formatNumber(analytics.totalPosts) }}
              </h3>
              <p class="metric-label">Total Posts</p>
              <small
                class="metric-change"
                :class="getChangeClass(analytics.postsChange)"
              >
                <i :class="getChangeIcon(analytics.postsChange)"></i>
                {{ formatPercentage(analytics.postsChange) }}
              </small>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts Row -->
      <div class="row mb-4">
        <!-- Engagement Over Time Chart -->
        <div class="col-lg-8 mb-4">
          <div class="chart-card">
            <div class="chart-header">
              <h5 class="mb-0">Engagement Over Time</h5>
              <div class="chart-legend">
                <span class="legend-item">
                  <span class="legend-color bg-primary"></span>
                  Views
                </span>
                <span class="legend-item">
                  <span class="legend-color bg-success"></span>
                  Likes
                </span>
                <span class="legend-item">
                  <span class="legend-color bg-info"></span>
                  Comments
                </span>
              </div>
            </div>
            <div class="chart-container">
              <EngagementChart :data="analytics.engagementData" />
            </div>
          </div>
        </div>

        <!-- Top Performing Posts -->
        <div class="col-lg-4 mb-4">
          <div class="chart-card">
            <div class="chart-header">
              <h5 class="mb-0">Top Performing Posts</h5>
            </div>
            <div class="top-posts-list">
              <div
                v-for="(post, index) in analytics.topPosts"
                :key="post.id"
                class="top-post-item"
              >
                <div class="post-rank">{{ index + 1 }}</div>
                <div class="post-info">
                  <h6 class="post-title">{{ post.title }}</h6>
                  <div class="post-stats">
                    <span class="stat-item">
                      <i class="bi bi-eye"></i>
                      {{ formatNumber(post.views_count) }}
                    </span>
                    <span class="stat-item">
                      <i class="bi bi-heart"></i>
                      {{ formatNumber(post.likes_count) }}
                    </span>
                    <span class="stat-item">
                      <i class="bi bi-chat"></i>
                      {{ formatNumber(post.comments_count) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Additional Analytics -->
      <div class="row">
        <!-- Category Performance -->
        <div class="col-lg-6 mb-4">
          <div class="chart-card">
            <div class="chart-header">
              <h5 class="mb-0">Performance by Category</h5>
            </div>
            <div class="chart-container">
              <CategoryChart :data="analytics.categoryData" />
            </div>
          </div>
        </div>

        <!-- Audience Insights -->
        <div class="col-lg-6 mb-4">
          <div class="chart-card">
            <div class="chart-header">
              <h5 class="mb-0">Audience Insights</h5>
            </div>
            <div class="insights-grid">
              <div class="insight-item">
                <div class="insight-icon bg-primary">
                  <i class="bi bi-people"></i>
                </div>
                <div class="insight-content">
                  <h6>Average Views per Post</h6>
                  <p class="insight-value">
                    {{ formatNumber(analytics.avgViewsPerPost) }}
                  </p>
                </div>
              </div>
              <div class="insight-item">
                <div class="insight-icon bg-success">
                  <i class="bi bi-graph-up"></i>
                </div>
                <div class="insight-content">
                  <h6>Engagement Rate</h6>
                  <p class="insight-value">
                    {{ formatPercentage(analytics.engagementRate) }}
                  </p>
                </div>
              </div>
              <div class="insight-item">
                <div class="insight-icon bg-info">
                  <i class="bi bi-calendar"></i>
                </div>
                <div class="insight-content">
                  <h6>Posts This Month</h6>
                  <p class="insight-value">{{ analytics.postsThisMonth }}</p>
                </div>
              </div>
              <div class="insight-item">
                <div class="insight-icon bg-warning">
                  <i class="bi bi-clock"></i>
                </div>
                <div class="insight-content">
                  <h6>Avg. Time on Page</h6>
                  <p class="insight-value">{{ analytics.avgTimeOnPage }}s</p>
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
import { blogAPI } from "../api.js";
import EngagementChart from "../components/Analytics/EngagementChart.vue";
import CategoryChart from "../components/Analytics/CategoryChart.vue";

export default {
  name: "Analytics",
  components: {
    EngagementChart,
    CategoryChart,
  },
  data() {
    return {
      loading: false,
      selectedPeriod: "30",
      analytics: {
        totalViews: 0,
        totalLikes: 0,
        totalComments: 0,
        totalPosts: 0,
        viewsChange: 0,
        likesChange: 0,
        commentsChange: 0,
        postsChange: 0,
        engagementData: [],
        topPosts: [],
        categoryData: [],
        avgViewsPerPost: 0,
        engagementRate: 0,
        postsThisMonth: 0,
        avgTimeOnPage: 0,
      },
    };
  },
  mounted() {
    this.loadAnalytics();
  },
  methods: {
    async loadAnalytics() {
      this.loading = true;
      try {
        const response = await blogAPI.getAnalytics(this.selectedPeriod);
        if (response.data && response.data.data) {
          this.analytics = { ...this.analytics, ...response.data.data };
        }
      } catch (error) {
        console.error("Error loading analytics:", error);
        this.loadMockData();
      } finally {
        this.loading = false;
      }
    },
    async refreshData() {
      await this.loadAnalytics();
    },
    loadMockData() {
      // Mock data for demonstration
      this.analytics = {
        totalViews: 15420,
        totalLikes: 892,
        totalComments: 234,
        totalPosts: 15,
        viewsChange: 12.5,
        likesChange: 8.3,
        commentsChange: -2.1,
        postsChange: 20.0,
        engagementData: this.generateMockEngagementData(),
        topPosts: this.generateMockTopPosts(),
        categoryData: this.generateMockCategoryData(),
        avgViewsPerPost: 1028,
        engagementRate: 7.3,
        postsThisMonth: 3,
        avgTimeOnPage: 145,
      };
    },
    generateMockEngagementData() {
      const days = 30;
      const data = [];
      for (let i = days - 1; i >= 0; i--) {
        const date = new Date();
        date.setDate(date.getDate() - i);
        data.push({
          date: date.toISOString().split("T")[0],
          views: Math.floor(Math.random() * 100) + 50,
          likes: Math.floor(Math.random() * 10) + 5,
          comments: Math.floor(Math.random() * 5) + 1,
        });
      }
      return data;
    },
    generateMockTopPosts() {
      return [
        {
          id: 1,
          title: "Getting Started with Vue.js",
          views_count: 2340,
          likes_count: 156,
          comments_count: 23,
        },
        {
          id: 2,
          title: "Advanced Laravel Techniques",
          views_count: 1890,
          likes_count: 98,
          comments_count: 15,
        },
        {
          id: 3,
          title: "CSS Grid Layout Guide",
          views_count: 1456,
          likes_count: 87,
          comments_count: 12,
        },
        {
          id: 4,
          title: "JavaScript Best Practices",
          views_count: 1234,
          likes_count: 76,
          comments_count: 8,
        },
        {
          id: 5,
          title: "Database Optimization Tips",
          views_count: 987,
          likes_count: 54,
          comments_count: 6,
        },
      ];
    },
    generateMockCategoryData() {
      return [
        { name: "Programming", views: 4560, posts: 8 },
        { name: "Design", views: 2340, posts: 4 },
        { name: "Tutorials", views: 1890, posts: 3 },
      ];
    },
    formatNumber(num) {
      if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + "M";
      } else if (num >= 1000) {
        return (num / 1000).toFixed(1) + "K";
      }
      return num.toString();
    },
    formatPercentage(value) {
      const sign = value >= 0 ? "+" : "";
      return `${sign}${value.toFixed(1)}%`;
    },
    getChangeClass(value) {
      return value >= 0 ? "text-success" : "text-danger";
    },
    getChangeIcon(value) {
      return value >= 0 ? "bi bi-arrow-up" : "bi bi-arrow-down";
    },
  },
};
</script>

<style scoped lang="scss">
.analytics-page {
  padding: 1.5rem;
}

.page-header {
  border-bottom: 1px solid #e9ecef;
  padding-bottom: 1rem;
}

.metric-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: transform 0.2s ease;

  &:hover {
    transform: translateY(-2px);
  }

  .metric-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
  }

  .metric-content {
    flex: 1;

    .metric-value {
      font-size: 2rem;
      font-weight: 700;
      margin: 0;
      line-height: 1;
    }

    .metric-label {
      color: #6c757d;
      margin: 0.25rem 0;
      font-size: 0.875rem;
    }

    .metric-change {
      font-size: 0.75rem;
      font-weight: 500;
    }
  }
}

.chart-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  height: 100%;

  .chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;

    .chart-legend {
      display: flex;
      gap: 1rem;

      .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #6c757d;

        .legend-color {
          width: 12px;
          height: 12px;
          border-radius: 2px;
        }
      }
    }
  }

  .chart-container {
    height: 300px;
  }
}

.top-posts-list {
  .top-post-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid #f8f9fa;

    &:last-child {
      border-bottom: none;
    }

    .post-rank {
      width: 32px;
      height: 32px;
      background: #f8f9fa;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
      font-size: 0.875rem;
    }

    .post-info {
      flex: 1;

      .post-title {
        margin: 0 0 0.5rem 0;
        font-size: 0.875rem;
        line-height: 1.3;
      }

      .post-stats {
        display: flex;
        gap: 1rem;

        .stat-item {
          display: flex;
          align-items: center;
          gap: 0.25rem;
          font-size: 0.75rem;
          color: #6c757d;
        }
      }
    }
  }
}

.insights-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;

  .insight-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;

    .insight-icon {
      width: 48px;
      height: 48px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1.25rem;
    }

    .insight-content {
      h6 {
        margin: 0;
        font-size: 0.875rem;
        color: #6c757d;
      }

      .insight-value {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 600;
      }
    }
  }
}

@media (max-width: 768px) {
  .analytics-page {
    padding: 1rem;
  }

  .insights-grid {
    grid-template-columns: 1fr;
  }

  .chart-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
}
</style>
