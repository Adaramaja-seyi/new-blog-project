<template>
  <div class="dashboard-comments">
    <DashboardCommentsHeader
      :selectedCount="selectedComments.length"
      @bulk-action="bulkAction"
    />
    <DashboardCommentsFilters
      :posts="posts"
      :status="statusFilter"
      :post="postFilter"
      :dateFrom="dateFrom"
      :dateTo="dateTo"
      :search="searchQuery"
      @update:status="onStatusChange"
      @update:post="onPostChange"
      @update:dateFrom="onDateFromChange"
      @update:dateTo="onDateToChange"
      @update:search="onSearchChange"
    />
    <DashboardCommentsTable
      :comments="paginatedComments"
      :selectedComments="selectedComments"
      :loading="loading"
      :allSelected="allSelected"
      @toggle-select-all="toggleSelectAll"
      @toggle-comment-select="toggleCommentSelect"
      @select-all="selectAll"
      @clear-selection="clearSelection"
      @approve-comment="approveComment"
      @mark-spam="markAsSpam"
      @delete-comment="deleteComment"
    />
    <DashboardCommentsPagination
      :total="filteredComments.length"
      :perPage="commentsPerPage"
      :current="currentPage"
      @change-page="changePage"
    />
    <DashboardCommentsStats :stats="stats" />
  </div>
</template>

<script>
import { blogAPI } from "@/api";

export default {
  name: "DashboardComments",
  data() {
    return {
      loading: false,
      comments: [],
      posts: [],
      selectedComments: [],
      statusFilter: "",
      postFilter: "",
      dateFrom: "",
      dateTo: "",
      searchQuery: "",
      currentPage: 1,
      commentsPerPage: 10,
      stats: {
        total: 0,
        approved: 0,
        pending: 0,
        spam: 0,
      },
    };
  },
  computed: {
    filteredComments() {
      let filtered = this.comments;

      if (this.statusFilter) {
        filtered = filtered.filter((comment) => comment.status === this.statusFilter);
      }

      if (this.postFilter) {
        filtered = filtered.filter((comment) => comment.post_id == this.postFilter);
      }

      if (this.dateFrom) {
        filtered = filtered.filter(
          (comment) => new Date(comment.created_at) >= new Date(this.dateFrom)
        );
      }

      if (this.dateTo) {
        filtered = filtered.filter(
          (comment) => new Date(comment.created_at) <= new Date(this.dateTo)
        );
      }

      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        filtered = filtered.filter(
          (comment) =>
            (comment.author_name && comment.author_name.toLowerCase().includes(query)) ||
            comment.content.toLowerCase().includes(query) ||
            (comment.post?.title && comment.post.title.toLowerCase().includes(query))
        );
      }

      return filtered;
    },
    paginatedComments() {
      const start = (this.currentPage - 1) * this.commentsPerPage;
      const end = start + this.commentsPerPage;
      return this.filteredComments.slice(start, end);
    },
    totalPages() {
      return Math.ceil(this.filteredComments.length / this.commentsPerPage);
    },
    allSelected() {
      return (
        this.paginatedComments.length > 0 &&
        this.paginatedComments.every((comment) =>
          this.selectedComments.includes(comment.id)
        )
      );
    },
  },
  mounted() {
    this.loadComments();
    this.loadPosts();
  },
  methods: {
    async loadComments() {
      this.loading = true;
      try {
        const filters = {};
        if (this.statusFilter) filters.status = this.statusFilter;
        if (this.postFilter) filters.post_id = this.postFilter;
        if (this.dateFrom) filters.date_from = this.dateFrom;
        if (this.dateTo) filters.date_to = this.dateTo;
        if (this.searchQuery) filters.search = this.searchQuery;

        const response = await blogAPI.getDashboardComments(filters);
        this.comments = response.data;

        this.updateStats();
      } catch (error) {
        console.error("Error loading comments:", error);
      } finally {
        this.loading = false;
      }
    },
    async loadPosts() {
      try {
        // Load posts for the filter dropdown
        const response = await blogAPI.getDashboardPosts();
        this.posts = response.data.map((post) => ({ id: post.id, title: post.title }));
      } catch (error) {
        console.error("Error loading posts:", error);
      }
    },
    updateStats() {
      this.stats = {
        total: this.comments.length,
        approved: this.comments.filter((c) => c.status === "approved").length,
        pending: this.comments.filter((c) => c.status === "pending").length,
        spam: this.comments.filter((c) => c.status === "spam").length,
      };
    },
    formatDate(dateString) {
      if (!dateString) return "";
      const date = new Date(dateString);
      return date.toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
      });
    },
    getStatusClass(status) {
      switch (status) {
        case "approved":
          return "bg-success";
        case "pending":
          return "bg-warning";
        case "spam":
          return "bg-danger";
        default:
          return "bg-secondary";
      }
    },
    async approveComment(commentId) {
      try {
        await blogAPI.updateCommentStatus(commentId, "approved");
        const comment = this.comments.find((c) => c.id === commentId);
        if (comment) {
          comment.status = "approved";
          this.updateStats();
        }
      } catch (error) {
        console.error("Error approving comment:", error);
        alert("Failed to approve comment. Please try again.");
      }
    },
    async markAsSpam(commentId) {
      try {
        await blogAPI.updateCommentStatus(commentId, "spam");
        const comment = this.comments.find((c) => c.id === commentId);
        if (comment) {
          comment.status = "spam";
          this.updateStats();
        }
      } catch (error) {
        console.error("Error marking comment as spam:", error);
        alert("Failed to mark comment as spam. Please try again.");
      }
    },
    async deleteComment(commentId) {
      if (confirm("Are you sure you want to delete this comment?")) {
        try {
          await blogAPI.deleteComment(commentId);
          this.comments = this.comments.filter((comment) => comment.id !== commentId);
          this.selectedComments = this.selectedComments.filter((id) => id !== commentId);
          this.updateStats();
        } catch (error) {
          console.error("Error deleting comment:", error);
          alert("Failed to delete comment. Please try again.");
        }
      }
    },
    selectAll() {
      this.selectedComments = this.paginatedComments.map((comment) => comment.id);
    },
    clearSelection() {
      this.selectedComments = [];
    },
    toggleSelectAll() {
      if (this.allSelected) {
        this.selectedComments = [];
      } else {
        this.selectedComments = this.paginatedComments.map((comment) => comment.id);
      }
    },
    async bulkAction(action) {
      if (!this.selectedComments.length) return;

      if (action === "approve") {
        try {
          await blogAPI.bulkUpdateComments({
            ids: this.selectedComments,
            status: "approved",
          });
          this.comments = this.comments.map((comment) => {
            if (this.selectedComments.includes(comment.id)) {
              return { ...comment, status: "approved" };
            }
            return comment;
          });
          this.selectedComments = [];
          this.updateStats();
        } catch (error) {
          console.error("Error approving comments:", error);
          alert("Failed to approve some comments. Please try again.");
        }
      } else if (action === "delete") {
        if (
          confirm(
            `Are you sure you want to delete ${this.selectedComments.length} comments?`
          )
        ) {
          try {
            await blogAPI.bulkDeleteComments({ ids: this.selectedComments });
            this.comments = this.comments.filter(
              (comment) => !this.selectedComments.includes(comment.id)
            );
            this.selectedComments = [];
            this.updateStats();
          } catch (error) {
            console.error("Error deleting comments:", error);
            alert("Failed to delete some comments. Please try again.");
          }
        }
      }
    },
  },
  watch: {
    statusFilter() {
      this.currentPage = 1;
      this.loadComments();
    },
    postFilter() {
      this.currentPage = 1;
      this.loadComments();
    },
    dateFrom() {
      this.currentPage = 1;
      this.loadComments();
    },
    dateTo() {
      this.currentPage = 1;
      this.loadComments();
    },
    searchQuery() {
      this.currentPage = 1;
      // Debounce search to avoid too many API calls
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => {
        this.loadComments();
      }, 500);
    },
  },
};
</script>

<style scoped lang="scss">
.dashboard-comments {
  .comment-author {
    .author-info {
      strong {
        font-size: 0.9rem;
      }

      small {
        font-size: 0.8rem;
      }
    }
  }

  .comment-content {
    p {
      font-size: 0.9rem;
      line-height: 1.4;
    }
  }

  .form-check-input {
    &:checked {
      background-color: var(--primary-blue);
      border-color: var(--primary-blue);
    }
  }

  .badge {
    font-size: 0.75rem;
    font-weight: 500;
  }
}

// Responsive adjustments
@media (max-width: 768px) {
  .dashboard-comments {
    .table {
      .table-row {
        .row {
          .col-1,
          .col-2,
          .col-3,
          .col-4 {
            margin-bottom: 0.5rem;
          }
        }
      }
    }
  }
}
</style>
