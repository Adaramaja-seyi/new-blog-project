<template>
  <div class="row mt-4">
    <div class="col-md-3">
      <div class="stats-card">
        <div class="stats-icon">
          <i class="bi bi-chat-dots"></i>
        </div>
        <div class="stats-number">{{ stats.total }}</div>
        <div class="stats-label">Total Comments</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="stats-card">
        <div class="stats-icon">
          <i class="bi bi-check-circle"></i>
        </div>
        <div class="stats-number">{{ stats.approved }}</div>
        <div class="stats-label">Approved</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="stats-card">
        <div class="stats-icon">
          <i class="bi bi-clock"></i>
        </div>
        <div class="stats-number">{{ stats.pending }}</div>
        <div class="stats-label">Pending</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="stats-card">
        <div class="stats-icon">
          <i class="bi bi-exclamation-triangle"></i>
        </div>
        <div class="stats-number">{{ stats.spam }}</div>
        <div class="stats-label">Spam</div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "DashboardCommentsStats",
  props: {
    stats: {
      type: Object,
      default: () => ({
        total: 0,
        approved: 0,
        pending: 0,
        spam: 0
      })
    }
  }
};
</script>
<style scoped>
.stats-card {
  background: white;
  border-radius: 8px;
  padding: 1.5rem;
  text-align: center;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  border: 1px solid #e9ecef;
}

.stats-icon {
  font-size: 2rem;
  color: #6c757d;
  margin-bottom: 0.5rem;
}

.stats-number {
  font-size: 2rem;
  font-weight: bold;
  color: #212529;
}

.stats-label {
  font-size: 0.875rem;
  color: #6c757d;
  margin-top: 0.25rem;
}
</style>
