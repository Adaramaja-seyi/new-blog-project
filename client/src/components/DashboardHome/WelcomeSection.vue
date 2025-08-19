<template>
  <div class="welcome-section mb-4">
    <h2>Welcome back, {{ user?.name || 'Author' }}!</h2>
    <p class="text-muted">Here's what's happening with your blog today.</p>
  </div>
</template>

<script>
export default {
  name: "WelcomeSection",
  props: {
    user: {
      type: Object,
      required: false,
      default: null
    }
  }
};
</script>

<style scoped>
.welcome-section h2 {
  color: var(--text-primary);
  margin-bottom: 0.5rem;
  font-weight: 600;
}
</style>
