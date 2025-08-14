<template>
  <div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3" v-for="card in cards" :key="card.label">
      <div class="stats-card">
        <div class="stats-icon">
          <i :class="card.icon"></i>
        </div>
        <div class="stats-number">{{ card.value }}</div>
        <div class="stats-label">{{ card.label }}</div>
      </div>
    </div>
  </div>
</template>

<script>
import { blogAPI } from "../../api.js";

export default {
  name: "StatsCards",
  data() {
    return {
      stats: {
        totalPosts: 0,
        totalLikes: 0,
        totalComments: 0,
        totalViews: 0,
      },
      cards: [],
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
            totalPosts: s.total_posts || 0,
            totalLikes: s.total_likes || 0,
            totalComments: s.total_comments || 0,
            totalViews: s.total_views || 0,
          };
          this.cards = [
            { label: "Total Posts", value: this.stats.totalPosts, icon: "bi bi-file-text" },
            { label: "Total Likes", value: this.stats.totalLikes, icon: "bi bi-heart" },
            {
              label: "Total Comments",
              value: this.stats.totalComments,
              icon: "bi bi-chat-dots",
            },
            { label: "Total Views", value: this.stats.totalViews, icon: "bi bi-eye" },
          ];
        }
      } catch (e) {
        // handle error
      }
    },
  },
};
</script>

<style scoped>
.stats-card {
  /* ...existing styles... */
}
</style>
