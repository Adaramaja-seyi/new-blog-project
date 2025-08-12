<script>
import GlobalNavbar from './components/GlobalNavbar.vue'
import Footer from './components/Footer.vue'

export default {
  name: 'App',
  components: {
    GlobalNavbar,
    Footer
  },
  computed: {
    isDashboardRoute() {
      return this.$route.path.startsWith('/dashboard')
    },
    isAuthor() {
      const user = localStorage.getItem('user')
      if (!user) return false
      
      try {
        const userData = JSON.parse(user)
        return userData.role === 'author'
      } catch {
        return false
      }
    }
  }
}
</script>

<template>
  <div id="app">
    <!-- Show regular layout for non-dashboard routes -->
    <template v-if="!isDashboardRoute">
      <!-- Global Navigation Bar -->
      <GlobalNavbar />
      
      <!-- Main Content Area -->
      <main class="main-content">
        <router-view />
      </main>
      
      <!-- Global Footer -->
      <Footer />
    </template>
    
    <!-- Show dashboard layout for dashboard routes (authors only) -->
    <template v-else-if="isAuthor">
      <router-view />
    </template>
    
    <!-- Redirect non-authors trying to access dashboard -->
    <template v-else>
      <div class="unauthorized-access">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-md-6 text-center">
              <h2>Access Denied</h2>
              <p>You don't have permission to access the dashboard.</p>
              <router-link to="/" class="btn btn-primary">
                Go to Home
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<style scoped>
.main-content {
  min-height: calc(100vh - 200px); /* Adjust based on navbar + footer height */
  padding: 2rem 0;
}

.unauthorized-access {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: var(--bg-secondary);
}
</style>
