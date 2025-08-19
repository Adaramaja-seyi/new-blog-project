<template>
  <div class="card mb-4">
    <div class="card-body">
      <div class="row align-items-center">
        <div class="col-md-4 mb-3 mb-md-0">
          <label class="form-label">Status Filter</label>
          <select
            v-model="statusFilter"
            class="form-select"
            @change="$emit('update:status', statusFilter)"
          >
            <option value="">All Posts</option>
            <option value="published">Published</option>
            <option value="draft">Drafts</option>
          </select>
        </div>
        <div class="col-md-4 mb-3 mb-md-0">
          <label class="form-label">Category</label>
          <select
            v-model="categoryFilter"
            class="form-select"
            @change="$emit('update:category', categoryFilter)"
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
        <div class="col-md-4">
          <label class="form-label">Search</label>
          <div class="input-group">
            <span class="input-group-text">
              <i class="bi bi-search"></i>
            </span>
            <input
              type="text"
              class="form-control"
              placeholder="Search posts..."
              v-model="searchQuery"
              @input="$emit('update:search', searchQuery)"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "DashboardPostsFilters",
  props: {
    categories: Array,
    status: String,
    category: [String, Number],
    search: String,
  },
  data() {
    return {
      statusFilter: this.status || "",
      categoryFilter: this.category || "",
      searchQuery: this.search || "",
    };
  },
  watch: {
    status(val) {
      this.statusFilter = val;
    },
    category(val) {
      this.categoryFilter = val;
    },
    search(val) {
      this.searchQuery = val;
    },
  },
};
</script>
<style scoped>
/* Add filter-specific styles here if needed */
</style>
