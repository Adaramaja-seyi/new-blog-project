<template>
  <footer class="footer">
    <div class="container">
      <div class="row">
        <!-- Brand Section -->
        <div class="col-md-4 mb-3">
          <h5 class="mb-3">Blog Platform</h5>
          <p class="text-muted mb-0">
            A modern blogging platform built with Vue 3 and Bootstrap 5.
          </p>
        </div>
        
        <!-- Quick Links -->
        <div class="col-md-4 mb-3">
          <h6 class="mb-3">Quick Links</h6>
          <ul class="list-unstyled">
            <li class="mb-2">
              <router-link to="/" class="text-muted text-decoration-none">
                Home
              </router-link>
            </li>
            <li class="mb-2">
              <router-link to="/login" class="text-muted text-decoration-none">
                Login
              </router-link>
            </li>
            <li class="mb-2">
              <router-link to="/register" class="text-muted text-decoration-none">
                Register
              </router-link>
            </li>
          </ul>
        </div>
        
        <!-- Contact Info -->
        <div class="col-md-4 mb-3">
          <h6 class="mb-3">Contact</h6>
          <ul class="list-unstyled">
            <li class="mb-2 text-muted">
              <small>Email: contact@blogplatform.com</small>
            </li>
            <li class="mb-2 text-muted">
              <small>Follow us on social media</small>
            </li>
          </ul>
        </div>
      </div>
      
      <!-- Copyright -->
      <hr class="my-4">
      <div class="row align-items-center">
        <div class="col-md-6">
          <p class="text-muted mb-0">
            <small>&copy; {{ currentYear }} Blog Platform. All rights reserved.</small>
          </p>
        </div>
        <div class="col-md-6 text-md-end">
          <p class="text-muted mb-0">
            <small>
              Built with Vue 3, Bootstrap 5, and ❤️
            </small>
          </p>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'Footer',
  computed: {
    currentYear() {
      return new Date().getFullYear()
    }
  }
}
</script>

<style scoped>
.footer {
  background-color: var(--surface-color);
  border-top: 1px solid var(--border-color);
  padding: var(--spacing-xl) 0;
  margin-top: auto;
}

.footer h5,
.footer h6 {
  color: var(--text-primary);
  font-weight: 600;
}

.footer a {
  transition: color 0.2s ease;
}

.footer a:hover {
  color: var(--primary-color) !important;
}

.footer hr {
  border-color: var(--border-color);
}
</style>

