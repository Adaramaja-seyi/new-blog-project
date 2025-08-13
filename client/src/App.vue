<script>
import GlobalNavbar from './components/GlobalNavbar.vue'
import Footer from './components/Footer.vue'
import { useAuth } from './stores/auth.js'

export default {
  name: 'App',
  components: {
    GlobalNavbar,
    Footer
  },
  data() {
    return {
      authInitialized: false
    }
  },
  computed: {
    isDashboardRoute() {
      return this.$route.path.startsWith('/dashboard')
    },
    isAuthenticated() {
      const auth = useAuth()
      return auth.isAuthenticated.value
    }
  },
  async mounted() {
    // Initialize authentication on app start
    const auth = useAuth()
    await auth.initializeAuth()
    this.authInitialized = true
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
    
    <!-- Show dashboard layout for authenticated users -->
    <template v-else-if="isAuthenticated">
      <router-view />
    </template>
    
    <!-- Show loading or redirect for unauthenticated users -->
    <template v-else>
      <div class="loading-container">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-md-6 text-center">
              <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
              <p class="mt-3">Loading...</p>
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

.loading-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: var(--bg-secondary);
}
</style>
