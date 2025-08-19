<template>
  <div class="dashboard-drafts">
    <DashboardDraftsHeader />
    <DashboardDraftsFilters
      :categories="categories"
      :category="categoryFilter"
      :search="searchQuery"
      @update:category="onCategoryChange"
      @update:search="onSearchChange"
    />

    <DashboardDraftsTable
      :posts="paginatedPosts"
      :selectedPosts="selectedPosts"
      :loading="loading"
      :allSelected="allSelected"
      @toggle-select-all="toggleSelectAll"
      @toggle-post-select="togglePostSelect"
      @delete-post="deletePost"
      @publish-post="publishPost"
      @bulk-action="bulkAction"
    />
    <DashboardDraftsPagination
      :total="filteredPosts?.length || 0"
      :perPage="postsPerPage"
      :current="currentPage"
      @change-page="changePage"
    />
  </div>
</template>

<script>
import DashboardDraftsHeader from "@/components/DashboardDrafts/DashboardDraftsHeader.vue";
import DashboardDraftsFilters from "@/components/DashboardDrafts/DashboardDraftsFilters.vue";
import DashboardDraftsTable from "@/components/DashboardDrafts/DashboardDraftsTable.vue";
import DashboardDraftsPagination from "@/components/DashboardDrafts/DashboardDraftsPagination.vue";
import { blogAPI } from "../api.js";
import { useToast } from "vue-toastification";

export default {
  setup() {
    const toast = useToast();
    return { toast };
  },
  name: "DashboardDrafts",
  components: {
    DashboardDraftsHeader,
    DashboardDraftsFilters,
    DashboardDraftsTable,
    DashboardDraftsPagination,
  },
  data() {
    return {
      posts: [],
      categories: [],
      selectedPosts: [],
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
      // Only show draft posts
      filtered = filtered.filter((post) => post.status === "draft");

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

        await this.loadDrafts();
        await this.loadCategories();
      } catch (error) {
        console.error("Error initializing dashboard drafts:", error);
      }
    },
    async loadDrafts() {
      this.loading = true;
      try {
        const filters = { status: "draft" }; // Only load draft posts
        if (this.categoryFilter) filters.category_id = this.categoryFilter;
        if (this.searchQuery) filters.search = this.searchQuery;

        const response = await blogAPI.getDashboardPosts(filters);

        if (response.data && response.data.success) {
          this.posts = response.data.data || [];
        } else if (response.data && Array.isArray(response.data)) {
          this.posts = response.data;
        } else {
          this.posts = [];
        }
      } catch (error) {
        console.error("Error loading drafts:", error);
        this.posts = [];
      } finally {
        this.loading = false;
      }
    },
    async loadCategories() {
      try {
        const response = await blogAPI.getCategories();
        // Ensure we extract just the data array, not the whole response object
        this.categories = response.data?.data || response.data || [];
      } catch (error) {
        console.error("Error loading categories:", error);
        this.categories = [];
      }
    },
    async deletePost(postSlug) {
      if (confirm("Are you sure you want to delete this draft?")) {
        try {
          await blogAPI.deletePost(postSlug);
          this.selectedPosts = this.selectedPosts.filter(
            (id) => id !== postSlug
          );
          this.loadDrafts(); // Reload drafts after deletion
        } catch (error) {
          console.error("Error deleting draft:", error);
          this.toast.error("Failed to delete draft. Please try again.");
        }
      }
    },
    async publishPost(postId) {
      try {
        // Get the post data first
        const post = this.posts.find((p) => p.id === postId);
        if (!post) {
          this.toast("Post not found");
          return;
        }
        console.log(post,99)
        const formData = new FormData();
        formData.append("title", post.title || "");
        formData.append("content", post.content || "");
        formData.append("excerpt", post.excerpt || "");
        formData.append("category_id", post.category.id || "");
        formData.append("tag_id", post.tag_id || "");
        formData.append("status", "published");
        formData.append("is_published", "yes");
        
        formData.append("_method", "PUT");
        await blogAPI.updatePost(post.slug, formData);
        this.loadDrafts();
        this.toast.success("Post published successfully!");
      } catch (error) {
        console.error("Error publishing post:", error);
        this.toast.error("Failed to publish post. Please try again.");
      }
    },
    handleFilterChange(filters) {
      this.categoryFilter = filters.category;
      this.searchQuery = filters.search;
      this.currentPage = 1; // Reset to first page when filtering
      this.loadDrafts();
    },
    handlePageChange(page) {
      this.currentPage = page;
    },
    handlePostToggle(postId) {
      const index = this.selectedPosts.indexOf(postId);
      if (index > -1) {
        this.selectedPosts.splice(index, 1);
      } else {
        this.selectedPosts.push(postId);
      }
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
        this.toast.info("Please select drafts to delete");
        return;
      }
      if (
        !confirm(
          `Are you sure you want to delete ${this.selectedPosts.length} drafts?`
        )
      ) {
        return;
      }
      try {
        await blogAPI.bulkDeletePosts({ ids: this.selectedPosts });
        this.selectedPosts = [];
        this.loadDrafts(); // Reload drafts after deletion
      } catch (error) {
        console.error("Error deleting drafts:", error);
        this.toast.error("Error deleting drafts. Please try again.");
      }
    },
    async handleBulkPublish() {
      if (this.selectedPosts.length === 0) {
        this.toast.info("Please select drafts to publish");
        return;
      }
      if (
        !confirm(
          `Are you sure you want to publish ${this.selectedPosts.length} drafts?`
        )
      ) {
        return;
      }
      try {
        // Get all selected posts and update their status
        const selectedPostData = this.posts.filter((post) =>
          this.selectedPosts.includes(post.id)
        );

        for (const post of selectedPostData) {
          await blogAPI.updatePost(post.id, { ...post, status: "published" });
        }

        this.selectedPosts = [];
        this.loadDrafts(); // Reload drafts after publishing
        this.toast.success("Drafts published successfully!");
      } catch (error) {
        console.error("Error publishing drafts:", error);
        this.toast.error("Error publishing drafts. Please try again.");
      }
    },
    isPostSelected(postId) {
      return this.selectedPosts.includes(postId);
    },
    // Methods expected by child components
    onCategoryChange(categoryId) {
      this.categoryFilter = categoryId;
      this.currentPage = 1;
      this.loadDrafts();
    },
    onSearchChange(query) {
      this.searchQuery = query;
      this.currentPage = 1;
      this.loadDrafts();
    },
    toggleSelectAll() {
      this.handleToggleAll();
    },
    togglePostSelect(postId) {
      this.handlePostToggle(postId);
    },
    bulkAction(action, value = null) {
      if (action === "delete") {
        this.handleBulkDelete();
      } else if (action === "publish") {
        this.handleBulkPublish();
      }
    },
    changePage(page) {
      this.handlePageChange(page);
    },
  },
};
</script>

<style scoped>
.dashboard-drafts {
  padding: 1rem;
}

.draft-status {
  background-color: #ffc107;
  color: #000;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
  font-size: 0.875rem;
  font-weight: 500;
}
</style>
