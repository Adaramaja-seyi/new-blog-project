<template>
  <div class="dashboard-home">
    <!-- Welcome Section -->
    <WelcomeSection :user="user" />

    <!-- Stats Cards -->
    <StatsCards />

    <!-- Main Content Grid -->
    <div class="row">
      <!-- Recent Posts -->
      <div class="col-lg-8 mb-4">
        <RecentPostsTable @delete="deletePost" />
      </div>

      <!-- Recent Comments -->
      <div class="col-lg-4 mb-4">
        <RecentCommentsTable @approve="approveComment" @delete="deleteComment" />
      </div>
    </div>
  </div>
</template>

<script>
import { useAuth } from "../stores/auth.js";
import { usePosts } from "../stores/posts.js";
import { blogAPI } from "../api.js";
import WelcomeSection from "../components/DashboardHome/WelcomeSection.vue";
import StatsCards from "../components/DashboardHome/StatsCards.vue";
import RecentPostsTable from "../components/DashboardHome/RecentPostsTable.vue";
import RecentCommentsTable from "../components/DashboardHome/RecentCommentsTable.vue";
import EngagementChart from "../components/DashboardHome/EngagementChart.vue";

export default {
  name: "DashboardHome",
  components: {
    WelcomeSection,
    StatsCards,
    RecentPostsTable,
    RecentCommentsTable,
    EngagementChart,
  },
  data() {
    return {
      auth: null,
    };
  },
  computed: {
    user() {
      return this.auth?.user || null;
    },
  },
  mounted() {
    this.auth = useAuth();
  },
  methods: {
    async deletePost(postId) {
      // Optionally, you can emit an event or handle post deletion globally
      // Or leave this empty if handled in RecentPostsTable
    },
    async approveComment(commentId) {
      // Optionally, you can emit an event or handle comment approval globally
      // Or leave this empty if handled in RecentCommentsTable
    },
    async deleteComment(commentId) {
      // Optionally, you can emit an event or handle comment deletion globally
      // Or leave this empty if handled in RecentCommentsTable
    },
  },
};
</script>
