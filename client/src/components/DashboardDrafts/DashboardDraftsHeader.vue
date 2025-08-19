<template>
  <div class="drafts-header mb-4">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h1 class="h3 mb-1">Drafts</h1>
        <p class="text-muted mb-0">Manage your unpublished posts and drafts</p>
      </div>
      <div class="d-flex gap-2">
        <router-link to="/dashboard/create" class="btn btn-primary">
          <i class="bi bi-plus-lg me-2"></i>
          Create New Draft
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "DashboardDraftsHeader",
};
</script>

<style scoped lang="scss">
.drafts-header {
  background: #fff;
  border-radius: 8px;
  padding: 1.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  border: 1px solid #e9ecef;
}

.btn {
  font-weight: 500;
  border-radius: 6px;
  padding: 0.5rem 1rem;
  transition: all 0.2s ease;

  &:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
  }
}

.btn-success {
  background-color: #198754;
  border-color: #198754;

  &:hover {
    background-color: #157347;
    border-color: #157347;
  }
}

.btn-danger {
  background-color: #dc3545;
  border-color: #dc3545;

  &:hover {
    background-color: #bb2d3b;
    border-color: #bb2d3b;
  }
}

.btn-primary {
  background-color: #0d6efd;
  border-color: #0d6efd;

  &:hover {
    background-color: #0b5ed7;
    border-color: #0b5ed7;
  }
}
</style>
