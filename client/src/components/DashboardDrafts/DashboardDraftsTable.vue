<template>
  <div class="drafts-table">
    <div class="card">
      <div class="card-body p-0">
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="mt-2 text-muted">Loading drafts...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="posts.length === 0" class="text-center py-5">
          <i class="bi bi-file-earmark-text display-1 text-muted"></i>
          <h4 class="mt-3 text-muted">No drafts found</h4>
          <p class="text-muted">You haven't created any drafts yet.</p>
          <router-link to="/dashboard/create" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>
            Create Your First Draft
          </router-link>
        </div>

        <!-- Drafts Table -->
        <div v-else class="table-responsive">
          <table class="table table-sm table-hover table-bordered align-middle">
            <thead class="table-light">
              <tr>
                <th scope="col">#</th>
                <th scope="col">Title</th>
                <th scope="col">Excerpt</th>
                <th scope="col">Image</th>
                <th scope="col">Category</th>
                <th scope="col">Last Modified</th>
                <th scope="col">Status</th>
                <th scope="col">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(post, index) in posts" :key="post.id">
                <td>{{ index + 1 }}</td>
                <td class="post-info">
                  <h6 class="mb-1 text-truncate">
                    {{ post.title || "Untitled Draft" }}
                  </h6>
                </td>
                <td>
                  <small class="text-muted">{{
                    post.excerpt || "No excerpt"
                  }}</small>
                </td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="post-thumbnail me-3" v-if="post.featured_image">
                      <img
                        :src="getImageUrl(post.featured_image)"
                        :alt="post.title"
                        class="rounded"
                        width="40"
                        height="40"
                        style="object-fit: cover"
                      />
                    </div>
                  </div>
                </td>
                <td>
                  <span v-if="post.category" class="badge bg-light text-dark">
                    {{ post.category.name }}
                  </span>
                  <span v-else class="text-muted small">No category</span>
                </td>
                <td>
                  <span class="text-muted small">
                    {{ formatDate(post.updated_at) }}
                  </span>
                </td>
                <td>
                  <span class="badge bg-warning text-dark">
                    <i class="bi bi-pencil-square me-1"></i>
                    Draft
                  </span>
                </td>
                <td>
                  <div class="btn-group" role="group">
                    <button
                      @click="$emit('publish-post', post.id)"
                      class="btn btn-sm btn-success"
                      title="Publish Draft"
                    >
                      <i class="bi bi-send"></i>
                    </button>
                    <router-link
                      :to="{ name: 'EditPost', params: { slug: post.slug } }"
                      class="btn btn-sm btn-outline-primary"
                      title="Edit Draft"
                    >
                      <i class="bi bi-pencil"></i>
                    </router-link>
                    <button
                      @click="$emit('delete-post', post.slug)"
                      class="btn btn-sm btn-outline-danger"
                      title="Delete Draft"
                    >
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "DashboardDraftsTable",
  props: {
    posts: {
      type: Array,
      default: () => [],
    },
    selectedPosts: {
      type: Array,
      default: () => [],
    },
    loading: {
      type: Boolean,
      default: false,
    },
    allSelected: {
      type: Boolean,
      default: false,
    },
  },
  emits: ["toggle-post-select", "delete-post", "publish-post", "bulk-action"],
  methods: {
    getImageUrl(path) {
      return `http://localhost:8000${path}`;
    },
    formatDate(dateString) {
      if (!dateString) return "Unknown";

      const date = new Date(dateString);
      const now = new Date();
      const diffTime = Math.abs(now - date);
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

      if (diffDays === 1) {
        return "Today";
      } else if (diffDays === 2) {
        return "Yesterday";
      } else if (diffDays <= 7) {
        return `${diffDays - 1} days ago`;
      } else {
        return date.toLocaleDateString("en-US", {
          year: "numeric",
          month: "short",
          day: "numeric",
        });
      }
    },
  },
};
</script>

<style scoped lang="scss">
.drafts-table {
  .card {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  }

  .table {
    margin-bottom: 0;

    th {
      border-top: none;
      border-bottom: 2px solid #dee2e6;
      font-weight: 600;
      color: #495057;
      font-size: 0.875rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    td {
      border-top: 1px solid #f8f9fa;
      vertical-align: middle;
      padding: 1rem 0.75rem;
    }

    tbody tr {
      transition: background-color 0.15s ease-in-out;

      &:hover {
        background-color: #f8f9fa;
      }
    }
  }

  .post-thumbnail img {
    border: 1px solid #e9ecef;
  }

  .post-info h6 {
    font-weight: 600;
    color: #212529;
    line-height: 1.2;
  }

  .badge {
    font-size: 0.75rem;
    font-weight: 500;
    padding: 0.375rem 0.75rem;
  }

  .btn-group {
    .btn {
      border-radius: 0;

      &:first-child {
        border-top-left-radius: 0.375rem;
        border-bottom-left-radius: 0.375rem;
      }

      &:last-child {
        border-top-right-radius: 0.375rem;
        border-bottom-right-radius: 0.375rem;
      }
    }
  }

  .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
  }

  .form-check-input {
    cursor: pointer;
  }
}
</style>
