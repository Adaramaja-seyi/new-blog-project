<template>
  <div class="drafts-pagination mt-4">
    <div class="d-flex justify-content-between align-items-center">
      <div class="pagination-info">
        <span class="text-muted small">
          Showing {{ startItem }} to {{ endItem }} of {{ total }} drafts
        </span>
      </div>

      <nav v-if="totalPages > 1" aria-label="Drafts pagination">
        <ul class="pagination pagination-sm mb-0">
          <!-- Previous Page -->
          <li class="page-item" :class="{ disabled: current === 1 }">
            <button
              class="page-link"
              @click="$emit('change-page', current - 1)"
              :disabled="current === 1"
              aria-label="Previous page"
            >
              <i class="bi bi-chevron-left"></i>
            </button>
          </li>

          <!-- Page Numbers -->
          <li
            v-for="page in visiblePages"
            :key="page"
            class="page-item"
            :class="{ active: page === current }"
          >
            <button
              v-if="page !== '...'"
              class="page-link"
              @click="$emit('change-page', page)"
            >
              {{ page }}
            </button>
            <span v-else class="page-link disabled">...</span>
          </li>

          <!-- Next Page -->
          <li class="page-item" :class="{ disabled: current === totalPages }">
            <button
              class="page-link"
              @click="$emit('change-page', current + 1)"
              :disabled="current === totalPages"
              aria-label="Next page"
            >
              <i class="bi bi-chevron-right"></i>
            </button>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>

<script>
export default {
  name: "DashboardDraftsPagination",
  props: {
    total: {
      type: Number,
      default: 0,
    },
    perPage: {
      type: Number,
      default: 10,
    },
    current: {
      type: Number,
      default: 1,
    },
  },
  emits: ["change-page"],
  computed: {
    totalPages() {
      return Math.ceil(this.total / this.perPage);
    },
    startItem() {
      return this.total === 0 ? 0 : (this.current - 1) * this.perPage + 1;
    },
    endItem() {
      return Math.min(this.current * this.perPage, this.total);
    },
    visiblePages() {
      const pages = [];
      const totalPages = this.totalPages;
      const current = this.current;

      if (totalPages <= 7) {
        // Show all pages if 7 or fewer
        for (let i = 1; i <= totalPages; i++) {
          pages.push(i);
        }
      } else {
        // Always show first page
        pages.push(1);

        if (current > 4) {
          pages.push("...");
        }

        // Show pages around current
        const start = Math.max(2, current - 1);
        const end = Math.min(totalPages - 1, current + 1);

        for (let i = start; i <= end; i++) {
          pages.push(i);
        }

        if (current < totalPages - 3) {
          pages.push("...");
        }

        // Always show last page
        if (totalPages > 1) {
          pages.push(totalPages);
        }
      }

      return pages;
    },
  },
};
</script>

<style scoped lang="scss">
.drafts-pagination {
  .pagination-info {
    font-size: 0.875rem;
  }

  .pagination {
    .page-item {
      .page-link {
        border: 1px solid #dee2e6;
        color: #0d6efd;
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        transition: all 0.15s ease-in-out;

        &:hover {
          background-color: #e9ecef;
          border-color: #dee2e6;
          color: #0a58ca;
        }

        &:focus {
          box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
      }

      &.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
      }

      &.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: #fff;
        border-color: #dee2e6;
      }
    }
  }
}
</style>
