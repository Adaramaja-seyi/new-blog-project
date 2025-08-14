<template>
  <div class="card mb-4">
    <div class="card-body">
      <div class="row align-items-center">
        <div class="col-md-3 mb-3 mb-md-0">
          <label class="form-label">Status Filter</label>
          <select v-model="statusFilter" class="form-select" @change="$emit('update:status', statusFilter)">
            <option value="">All Comments</option>
            <option value="approved">Approved</option>
            <option value="pending">Pending</option>
            <option value="spam">Spam</option>
          </select>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
          <label class="form-label">Post Filter</label>
          <select v-model="postFilter" class="form-select" @change="$emit('update:post', postFilter)">
            <option value="">All Posts</option>
            <option v-for="post in posts" :key="post.id" :value="post.id">
              {{ post.title }}
            </option>
          </select>
        </div>
        <div class="col-md-4 mb-3 mb-md-0">
          <label class="form-label">Date Range</label>
          <div class="input-group">
            <input type="date" class="form-control" v-model="dateFromFilter" @change="$emit('update:dateFrom', dateFromFilter)" />
            <span class="input-group-text">to</span>
            <input type="date" class="form-control" v-model="dateToFilter" @change="$emit('update:dateTo', dateToFilter)" />
          </div>
        </div>
        <div class="col-md-2">
          <label class="form-label">Search</label>
          <div class="input-group">
            <span class="input-group-text">
              <i class="bi bi-search"></i>
            </span>
            <input type="text" class="form-control" placeholder="Search comments..." v-model="searchFilter" @input="$emit('update:search', searchFilter)" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "DashboardCommentsFilters",
  props: {
    posts: Array,
    status: String,
    post: [String, Number],
    dateFrom: String,
    dateTo: String,
    search: String
  },
  data() {
    return {
      statusFilter: this.status || "",
      postFilter: this.post || "",
      dateFromFilter: this.dateFrom || "",
      dateToFilter: this.dateTo || "",
      searchFilter: this.search || ""
    };
  },
  watch: {
    status(val) { this.statusFilter = val; },
    post(val) { this.postFilter = val; },
    dateFrom(val) { this.dateFromFilter = val; },
    dateTo(val) { this.dateToFilter = val; },
    search(val) { this.searchFilter = val; }
  }
};
</script>
