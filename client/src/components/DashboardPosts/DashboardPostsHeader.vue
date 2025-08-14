<template>
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2>My Posts</h2>
      <p class="text-muted mb-0">Manage your blog posts and drafts</p>
    </div>
    <router-link to="/dashboard/create" class="btn btn-primary">
      <i class="bi bi-plus-circle me-2"></i>
      Create New Post
    </router-link>
  </div>
</template>
<script>
export default {
  name: "DashboardPostsHeader"
};
</script>
<style scoped>
/* Add header-specific styles here if needed */
</style>
