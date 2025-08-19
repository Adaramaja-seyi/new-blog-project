<template>
  <div class="dashboard-posts">
    <DashboardPostsHeader />
    <DashboardPostsFilters
      :categories="categories"
      :status="statusFilter"
      :category="categoryFilter"
      :search="searchQuery"
      @update:status="onStatusChange"
      @update:category="onCategoryChange"
      @update:search="onSearchChange"
    />

    <DashboardPostsTable
      :posts="paginatedPosts"
      :selectedPosts="selectedPosts"
      :loading="loading"
      :allSelected="allSelected"
      @toggle-select-all="toggleSelectAll"
      @toggle-post-select="togglePostSelect"
      @delete-post="deletePost"
      @bulk-action="bulkAction"
    />
    <DashboardPostsPagination
      :total="filteredPosts?.length || 0"
      :perPage="postsPerPage"
      :current="currentPage"
      @change-page="changePage"
    />
  </div>
</template>

<script>
import DashboardPostsHeader from "@/components/DashboardPosts/DashboardPostsHeader.vue";
import DashboardPostsFilters from "@/components/DashboardPosts/DashboardPostsFilters.vue";
import DashboardPostsTable from "@/components/DashboardPosts/DashboardPostsTable.vue";
import DashboardPostsPagination from "@/components/DashboardPosts/DashboardPostsPagination.vue";
import { blogAPI } from "../api.js";

export default {
  name: "DashboardPosts",
  components: {
    DashboardPostsHeader,
    DashboardPostsFilters,
    DashboardPostsTable,
    DashboardPostsPagination,
  },
  data() {
    return {
      posts: [],
      categories: [],
      selectedPosts: [],
      statusFilter: "",
      categoryFilter: "",
      searchQuery: "",
      currentPage: 1,
      postsPerPage: 10,
      loading: false,
    };
  },
  computed: {
    filteredPosts() {
      let filtered = this.posts;
      if (!Array.isArray(filtered)) {
        return [];
      }
      if (this.statusFilter) {
        filtered = filtered.filter((post) => post.status === this.statusFilter);
      }
      if (this.categoryFilter) {
        filtered = filtered.filter(
          (post) => post.category_id == this.categoryFilter
        );
      }
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        filtered = filtered.filter(
          (post) =>
            post.title.toLowerCase().includes(query) ||
            (post.excerpt && post.excerpt.toLowerCase().includes(query)) ||
            (post.content && post.content.toLowerCase().includes(query))
        );
      }
      return filtered;
    },
    paginatedPosts() {
      if (!Array.isArray(this.filteredPosts)) {
        return [];
      }
      const start = (this.currentPage - 1) * this.postsPerPage;
      const end = start + this.postsPerPage;
      return this.filteredPosts.slice(start, end);
    },
    allSelected() {
      return (
        this.paginatedPosts.length > 0 &&
        this.paginatedPosts.every((post) =>
          this.selectedPosts.includes(post.id)
        )
      );
    },
  },
  mounted() {
    this.initializePage();
  },
  methods: {
    async initializePage() {
      try {
        // Check if user is authenticated
        const token = localStorage.getItem("token");
        if (!token) {
          console.error("No auth token found");
          this.$router.push("/login");
          return;
        }

        await this.loadPosts();
        await this.loadCategories();
      } catch (error) {
        console.error("Error initializing dashboard posts:", error);
      }
    },
    async loadPosts() {
      this.loading = true;
      try {
        const filters = {};
        if (this.statusFilter) filters.status = this.statusFilter;
        if (this.categoryFilter) filters.category_id = this.categoryFilter;
        if (this.searchQuery) filters.search = this.searchQuery;

        const response = await blogAPI.getDashboardPosts(filters);

        // Handle different response structures
        if (response.data && response.data.success) {
          this.posts = response.data.data || [];
        } else if (response.data && Array.isArray(response.data)) {
          this.posts = response.data;
        } else {
          this.posts = [];
        }
      } catch (error) {
        console.error("Error loading posts:", error);
        this.posts = [];
      } finally {
        this.loading = false;
      }
    },
    async loadCategories() {
      try {
        const response = await blogAPI.getCategories();
        this.categories = response.data?.data || response.data || [];
      } catch (error) {
        console.error("Error loading categories:", error);
        this.categories = [];
      }
    },
    async deletePost(postSlug) {
      if (confirm("Are you sure you want to delete this post?")) {
        try {
          await blogAPI.deletePost(postSlug);
          this.selectedPosts = this.selectedPosts.filter(
            (id) => id !== postSlug
          );
          this.loadPosts();
        } catch (error) {
          console.error("Error deleting post:", error);
          alert("Failed to delete post. Please try again.");
        }
      }
    },
    handleFilterChange(filters) {
      this.statusFilter = filters.status;
      this.categoryFilter = filters.category;
      this.searchQuery = filters.search;
      this.currentPage = 1; // Reset to first page when filtering
      this.loadPosts();
    },
    handlePageChange(page) {
      this.currentPage = page;
    },
    handleToggleAll() {
      if (this.allSelected) {
        // Deselect all visible posts
        this.selectedPosts = this.selectedPosts.filter(
          (postId) => !this.paginatedPosts.some((post) => post.id === postId)
        );
      } else {
        // Select all visible posts
        this.paginatedPosts.forEach((post) => {
          if (!this.selectedPosts.includes(post.id)) {
            this.selectedPosts.push(post.id);
          }
        });
      }
    },
    async handleBulkDelete() {
      if (this.selectedPosts.length === 0) {
        alert("Please select posts to delete");
        return;
      }
      if (
        !confirm(
          `Are you sure you want to delete ${this.selectedPosts.length} post(s)?`
        )
      ) {
        return;
      }
      try {
        await blogAPI.bulkDeletePosts({ ids: this.selectedPosts });
        this.selectedPosts = [];
        this.loadPosts(); // Reload posts after deletion
      } catch (error) {
        console.error("Error deleting posts:", error);
        alert("Error deleting posts. Please try again.");
      }
    },
    async handleBulkUpdate(status) {
      if (this.selectedPosts.length === 0) {
        alert("Please select posts to update");
        return;
      }
      try {
        const postIds = this.posts
          .filter(
            (post) =>
              this.selectedPosts.includes(post.id) &&
              post.status !== "published"
          )
          .map((post) => post.id);

        if (postIds.length === 0) {
          alert("All selected posts are already published");
          return;
        }

        if (
          !confirm(
            `Are you sure you want to publish ${postIds.length} post(s)?`
          )
        ) {
          return;
        }

        await blogAPI.bulkUpdatePosts({
          ids: postIds,
          status,
        });
        this.selectedPosts = [];
        this.loadPosts(); // Reload posts after update
      } catch (error) {
        console.error("Error updating posts:", error);
        alert("Error updating posts. Please try again.");
      }
    },
    isPostSelected(postId) {
      return this.selectedPosts.includes(postId);
    },
    // Methods expected by child components
    onStatusChange(status) {
      this.statusFilter = status;
      this.currentPage = 1;
      this.loadPosts();
    },
    onCategoryChange(categoryId) {
      this.categoryFilter = categoryId;
      this.currentPage = 1;
      this.loadPosts();
    },
    onSearchChange(query) {
      this.searchQuery = query;
      this.currentPage = 1;
      this.loadPosts();
    },
    toggleSelectAll() {
      this.handleToggleAll();
    },
    togglePostSelect(postId) {
      const index = this.selectedPosts.indexOf(postId);
      if (index > -1) {
        this.selectedPosts.splice(index, 1);
      } else {
        this.selectedPosts.push(postId); // Keep id for selection
      }
    },
    bulkAction(action) {
      if (action === "delete") {
        this.handleBulkDelete();
      } else if (action === "publish") {
        this.handleBulkUpdate("published");
      }
    },
    changePage(page) {
      this.handlePageChange(page);
    },
  },
};
</script>

<style scoped>
.dashboard-posts-page {
  padding: 1rem;
}
</style>
