<template>
  <div class="dashboard-sidebar" :class="{ show: isMobileOpen }">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
      <div class="sidebar-logo">
        <i class="bi bi-journal-text"></i>
        <a href="/">ZuraBlog</a>
      </div>
      <button 
        v-if="isMobile" 
        @click="$emit('close-sidebar')" 
        class="btn-close ms-auto"
        aria-label="Close sidebar"
      ></button>
    </div>
    
    <!-- Sidebar Navigation -->
    <nav class="sidebar-nav">
      <ul class="nav flex-column">
        <li class="nav-item" v-for="item in menuItems" :key="item.path">
          <router-link 
            :to="item.path" 
            class="nav-link"
            :class="{ active: $route.path === item.path }"
            @click="isMobile && $emit('close-sidebar')"
          >
            <i :class="item.icon"></i>
            <span>{{ item.label }}</span>
          </router-link>
        </li>
      </ul>
    </nav>
    
    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
      <div class="user-info">
        <div class="user-avatar">
          <i class="bi bi-person-circle"></i>
        </div>
        <div class="user-details">
          <div class="user-name">{{ user?.name || 'User' }}</div>
          <div class="user-role">{{ user?.role || 'Author' }}</div>
        </div>
      </div>
      <button @click="logout" class="btn btn-outline-primary btn-sm w-100">
        <i class="bi bi-box-arrow-right me-2"></i>
        Logout
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DashboardSidebar',
  props: {
    isMobile: {
      type: Boolean,
      default: false
    },
    isMobileOpen: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      user: null
    }
  },
  computed: {
    menuItems() {
      return [
        {
          path: '/dashboard',
          label: 'Dashboard Overview',
          icon: 'bi bi-speedometer2'
        },
        {
          path: '/dashboard/posts',
          label: 'My Posts',
          icon: 'bi bi-file-text'
        },
        {
          path: '/dashboard/drafts',
          label: 'Drafts',
          icon: 'bi bi-file-earmark-text'
        },
        {
          path: '/dashboard/comments',
          label: 'Comments',
          icon: 'bi bi-chat-dots'
        },
        {
          path: '/dashboard/analytics',
          label: 'Analytics',
          icon: 'bi bi-graph-up'
        },
        {
          path: '/dashboard/settings',
          label: 'Settings',
          icon: 'bi bi-gear'
        }
      ]
    }
  },
  mounted() {
    this.loadUserData()
  },
  methods: {
    loadUserData() {
      // Load user data from localStorage
      const userData = localStorage.getItem('user')
      if (userData) {
        this.user = JSON.parse(userData)
      }
    },
    logout() {
      // Clear user data from localStorage
      localStorage.removeItem('user')
      localStorage.removeItem('token')
      
      // Redirect to login page
      this.$router.push('/login')
      
      // Emit logout event
      this.$emit('logout')
    }
  }
}
</script>

<style scoped lang="scss">
.dashboard-sidebar {
  width: 240px;
  background-color: var(--bg-primary);
  color: var(--text-primary);
  position: fixed;
  height: 100vh;
  overflow-y: auto;
  z-index: 1000;
  border-right: 1px solid var(--border-light);
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
  
  .sidebar-header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-light);
    
    .sidebar-logo {
      display: flex;
      align-items: center;
      font-size: 1.25rem;
      font-weight: 700;
      
      i {
        font-size: 1.5rem;
        color: var(--primary-blue);
        margin-right: 0.75rem;
      }
    }
  }
  
  .sidebar-nav {
    flex: 1;
    padding: 1rem 0;
    
    .nav-item {
      .nav-link {
        color: var(--text-secondary);
        padding: 0.75rem 1.5rem;
        display: flex;
        align-items: center;
        transition: all 0.2s ease;
        text-decoration: none;
        border-radius: 0;
        margin: 0.25rem 0;
        
        &:hover {
          background-color: var(--bg-secondary);
          color: var(--primary-blue);
        }
        
        &.active {
          background-color: var(--primary-blue);
          color: var(--primary-white);
        }
        
        i {
          margin-right: 0.75rem;
          width: 1.25rem;
          font-size: 1.1rem;
        }
        
        span {
          font-weight: 500;
          font-size: 0.9rem;
        }
      }
    }
  }
  
  .sidebar-footer {
    padding: 1.5rem;
    border-top: 1px solid var(--border-light);
    
    .user-info {
      display: flex;
      align-items: center;
      margin-bottom: 1rem;
      
      .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: var(--bg-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
        
        i {
          font-size: 1.25rem;
          color: var(--text-muted);
        }
      }
      
      .user-details {
        flex: 1;
        
        .user-name {
          font-weight: 600;
          font-size: 0.9rem;
          color: var(--text-primary);
        }
        
        .user-role {
          font-size: 0.8rem;
          color: var(--text-muted);
          text-transform: capitalize;
        }
      }
    }
  }
}

// Mobile responsive
@media (max-width: 768px) {
  .dashboard-sidebar {
    transform: translateX(-100%);
    transition: transform 0.3s ease;
    
    &.show {
      transform: translateX(0);
    }
  }
}

// Scrollbar styling for sidebar
.dashboard-sidebar::-webkit-scrollbar {
  width: 4px;
}

.dashboard-sidebar::-webkit-scrollbar-track {
  background: var(--bg-primary);
}

.dashboard-sidebar::-webkit-scrollbar-thumb {
  background: var(--border-color);
  border-radius: 2px;
}

.dashboard-sidebar::-webkit-scrollbar-thumb:hover {
  background: var(--primary-blue);
}
</style>
