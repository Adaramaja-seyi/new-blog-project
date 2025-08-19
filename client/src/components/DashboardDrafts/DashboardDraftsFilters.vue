<template>
  <div class="drafts-filters mb-4">
    <div class="card">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label for="categoryFilter" class="form-label">Category</label>
            <select
              id="categoryFilter"
              v-model="localCategory"
              class="form-select"
              @change="$emit('update:category', localCategory)"
            >
              <option value="">All Categories</option>
              <option
                v-for="category in categories"
                :key="category.id"
                :value="category.id"
              >
                {{ category.name }}
              </option>
            </select>
          </div>
          <div class="col-md-6">
            <label for="searchFilter" class="form-label">Search Drafts</label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="bi bi-search"></i>
              </span>
              <input
                id="searchFilter"
                v-model="localSearch"
                type="text"
                class="form-control"
                placeholder="Search by title, excerpt, or content..."
                @input="handleSearchInput"
              />
            </div>
          </div>
          <div class="col-md-2">
            <label class="form-label">&nbsp;</label>
            <button
              @click="clearFilters"
              class="btn btn-outline-secondary w-100"
              title="Clear Filters"
            >
              <i class="bi bi-x-lg me-1"></i>
              Clear
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "DashboardDraftsFilters",
  props: {
    categories: {
      type: Array,
      default: () => [],
    },
    category: {
      type: [String, Number],
      default: "",
    },
    search: {
      type: String,
      default: "",
    },
  },
  data() {
    return {
      localCategory: this.category,
      localSearch: this.search,
      searchTimeout: null,
    };
  },
  watch: {
    category(newVal) {
      this.localCategory = newVal;
    },
    search(newVal) {
      this.localSearch = newVal;
    },
  },
  methods: {
    handleSearchInput() {
      // Debounce search input
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => {
        this.$emit("update:search", this.localSearch);
      }, 300);
    },
    clearFilters() {
      this.localCategory = "";
      this.localSearch = "";
      this.$emit("update:category", "");
      this.$emit("update:search", "");
    },
  },
};
</script>

<style scoped lang="scss">
.drafts-filters {
  .card {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  }

  .form-label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 0.5rem;
  }

  .form-select,
  .form-control {
    border-radius: 6px;
    border: 1px solid #ced4da;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;

    &:focus {
      border-color: #0d6efd;
      box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
  }

  .input-group-text {
    background-color: #f8f9fa;
    border-color: #ced4da;
    color: #6c757d;
  }

  .btn-outline-secondary {
    border-color: #6c757d;
    color: #6c757d;

    &:hover {
      background-color: #6c757d;
      border-color: #6c757d;
      color: #fff;
    }
  }
}
</style>
