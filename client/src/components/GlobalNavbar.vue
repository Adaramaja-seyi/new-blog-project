<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-white">
    <div class="container">
      <!-- Brand/Logo -->
      <router-link class="navbar-brand" to="/">
        Blog Platform
      </router-link>
      
      <!-- Mobile Toggle Button -->
      <button 
        class="navbar-toggler" 
        type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#navbarNav"
        aria-controls="navbarNav" 
        aria-expanded="false" 
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>
      
      <!-- Navigation Links -->
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <router-link class="nav-link" to="/" exact-active-class="active">
              Home
            </router-link>
          </li>
          <!-- TODO: Add more navigation items as needed -->
        </ul>
        
        <!-- Authentication Section -->
        <ul class="navbar-nav">
          <!-- Show when user is NOT authenticated -->
          <template v-if="!isAuthenticated">
            <li class="nav-item">
              <router-link class="nav-link" to="/login">
                Login
              </router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/register">
                Register
              </router-link>
            </li>
          </template>
          
          <!-- Show when user IS authenticated -->
          <template v-else>
            <li class="nav-item dropdown">
              <a 
                class="nav-link dropdown-toggle" 
                href="#" 
                role="button" 
                data-bs-toggle="dropdown" 
                aria-expanded="false"
              >
                {{ user?.name || 'User' }}
              </a>
              <ul class="dropdown-menu">
                <li>
                  <router-link class="dropdown-item" to="/dashboard">
                    Dashboard
                  </router-link>
                </li>
                <li>
                  <router-link class="dropdown-item" to="/profile">
                    Profile
                  </router-link>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <a class="dropdown-item" href="#" @click.prevent="handleLogout">
                    Logout
                  </a>
                </li>
              </ul>
            </li>
          </template>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script>
import { blogAPI } from '../api.js'

export default {
  name: 'GlobalNavbar',
  data() {
    return {
      isAuthenticated: false, // TODO: Connect to auth state management
      user: null // TODO: Connect to user state management
    }
  },
  mounted() {
    // TODO: Check authentication status on component mount
    this.checkAuthStatus()
  },
  methods: {
    async checkAuthStatus() {
      try {
        // TODO: Implement authentication check
        // const response = await blogAPI.getProfile()
        // this.isAuthenticated = true
        // this.user = response.data
      } catch (error) {
        this.isAuthenticated = false
        this.user = null
      }
    },
    async handleLogout() {
      try {
        // TODO: Implement logout functionality
        // await blogAPI.logout()
        // this.isAuthenticated = false
        // this.user = null
        // this.$router.push('/login')
      } catch (error) {
        console.error('Logout failed:', error)
      }
    }
  }
}
</script>

<style scoped>
.navbar {
  padding: 1rem 0;
}

.navbar-brand {
  font-size: 1.5rem;
  font-weight: 700;
}

.dropdown-menu {
  border: 1px solid var(--border-color);
  box-shadow: var(--shadow);
}

.dropdown-item {
  color: var(--text-secondary);
  
  &:hover {
    background-color: var(--surface-color);
    color: var(--primary-color);
  }
}
</style>

