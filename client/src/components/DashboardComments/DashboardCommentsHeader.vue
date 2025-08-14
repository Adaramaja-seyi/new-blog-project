<template>
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2>Comments</h2>
      <p class="text-muted mb-0">Moderate and manage comments on your posts</p>
    </div>
    <div class="d-flex gap-2">
      <button @click="$emit('bulk-action', 'approve')" :disabled="!selectedCount" class="btn btn-outline-success">
        <i class="bi bi-check-circle me-2"></i>
        Approve Selected
      </button>
      <button @click="$emit('bulk-action', 'delete')" :disabled="!selectedCount" class="btn btn-outline-danger">
        <i class="bi bi-trash me-2"></i>
        Delete Selected
      </button>
    </div>
  </div>
</template>
<script>
export default {
  name: "DashboardCommentsHeader",
  props: {
    selectedCount: Number
  }
};
</script>
