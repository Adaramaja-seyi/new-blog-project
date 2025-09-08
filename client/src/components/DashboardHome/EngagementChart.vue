<template>
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
                  <div class="stat-number">{{ stats.totalViews }}</div>
                  <div class="stat-label">Monthly Views</div>
                </div>
              </div>
              <div class="col-4">
                <div class="stat-item">
                  <div class="stat-number">{{ stats.totalComments }}</div>
                  <div class="stat-label">Comments</div>
                </div>
              </div>
              <div class="col-4">
                <div class="stat-item">
                  <div class="stat-number">{{ stats.totalLikes }}</div>
                  <div class="stat-label">Likes</div>
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
import { blogAPI } from "../../api.js";

export default {
  name: "EngagementChart",
  data() {
    return {
      stats: {
        totalViews: 0,
        totalComments: 0,
        totalLikes: 0,
      },
    };
  },
  mounted() {
    this.fetchStats();
  },
  methods: {
    async fetchStats() {
      try {
        const response = await blogAPI.getDashboardStats();
        if (response.data && response.data.data && response.data.data.stats) {
          const s = response.data.data.stats;
          this.stats = {
            totalViews: s.total_views || 0,
            totalComments: s.total_comments || 0,
            totalLikes: s.total_likes || 0,
          };
        }
      } catch (e) {
        // handle error
      }
    },
  },
}; 
</script>

<style scoped lang="scss">
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
</style>
