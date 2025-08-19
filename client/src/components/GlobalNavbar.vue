<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-white modern-navbar">
    <div class="container">
      <!-- Brand/Logo -->
      <router-link class="navbar-brand brand-logo" to="/">
        <div class="brand-container">
          <div class="brand-icon">
            <i class="bi bi-pen-fill brand-icon"></i>
          </div>
          <span class="brand-text">ZuraBlog</span>
        </div>
      </router-link>

      <!-- Mobile Toggle Button -->
      <button
        class="navbar-toggler custom-toggler"
        type="button"
        aria-controls="navbarNav"
        aria-label="Toggle navigation"
        @click="toggleCollapse"
      >
        <span class="custom-toggler-icon">
          <span></span>
          <span></span>
          <span></span>
        </span>
      </button>

      <!-- Navigation Links -->
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto nav-links">
          <li class="nav-item">
            <router-link class="nav-link" to="/" exact-active-class="active">
              <i class="bi bi-house-door me-1"></i>
              Feed
            </router-link>
          </li>
        </ul>

        <!-- Authentication Section -->
        <ul class="navbar-nav auth-section">
          <template v-if="!auth.isLoggedIn">
            <li class="nav-item">
              <router-link class="nav-link login-link" :to="{ name: 'Login' }">
                <i class="bi bi-box-arrow-in-right me-1"></i>
                Login
              </router-link>
            </li>
            <li class="nav-item">
              <router-link
                class="nav-link register-btn"
                :to="{ name: 'Register' }"
              >
                <i class="bi bi-person-plus me-1"></i>
                Register
              </router-link>
            </li>
          </template>
          <template v-else>
            <li class="nav-item dropdown user-dropdown">
              <router-link class="nav-link user-profile-link" to="/dashboard">
                <div class="user-info">
                  <div class="user-avatar">
                    <img
                      v-if="auth.user?.profile_image"
                      :src="getImageUrl(auth.user?.profile_image)"
                      :alt="auth.user?.name || 'Profile_Image'"
                      class="profile-image"
                    />
                    <div v-else class="profile-placeholder">
                      {{ getInitials(auth.user?.name) }}
                    </div>
                  </div>
                  <div class="user-details">
                    <span class="user-name" v-if="auth && auth.user">
                      {{ auth.user.name }}
                    </span>
                    <small class="user-role">Dashboard</small>
                  </div>
                </div>
              </router-link>
            </li>
            <li class="nav-item">
              <button class="nav-link logout-btn" @click="handleLogout">
                <i class="bi bi-box-arrow-right me-1"></i>
                Logout
              </button>
            </li>
          </template>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script>
import { useAuth } from "../stores/auth.js";
import { Collapse } from "bootstrap";

export default {
  name: "GlobalNavbar",
  data() {
    return {
      collapseInstance: null,
    };
  },
  computed: {
    auth() {
      return useAuth();
    },
  },
  methods: {
    async handleLogout() {
      if (this.auth) {
        await this.auth.logout();
        this.$router.push("/");
      }
    },
    toggleCollapse() {
      if (this.collapseInstance) {
        this.collapseInstance.toggle();
      }
    },
    getImageUrl(path) {
      return `http://localhost:8000${path}`;
    },
    getInitials(name) {
      if (!name) return "U";
      return name
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase()
        .slice(0, 2);
    },
  },
  mounted() {
    const collapseElement = document.getElementById("navbarNav");
    if (collapseElement) {
      this.collapseInstance = new Collapse(collapseElement, { toggle: false });
    }
  },
  beforeUnmount() {
    if (this.collapseInstance) {
      this.collapseInstance.dispose();
      this.collapseInstance = null;
    }
  },
};
</script>

<style scoped lang="scss">
:root {
  --primary-blue: #3b82f6;
  --primary-blue-dark: #2563eb;
  --secondary-blue: #1e40af;
  --accent-color: #06b6d4;
  --text-primary: #1f2937;
  --text-secondary: #6b7280;
  --text-muted: #9ca3af;
  --bg-secondary: #f9fafb;
  --border-light: #e5e7eb;
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
    0 2px 4px -1px rgba(0, 0, 0, 0.06);
  --gradient-primary: linear-gradient(135deg, #3b82f6 0%, #06b6d4 100%);
}

.modern-navbar {
  padding: 1rem 0;
  background: rgba(255, 255, 255, 0.95) !important;
  backdrop-filter: blur(10px);
  border-bottom: 1px solid var(--border-light);
  box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
  position: sticky;
  top: 0;
  z-index: 1030;
  transition: all 0.3s ease;

  .container {
    position: relative;
  }

  // Brand Styling
  .brand-logo {
    text-decoration: none;
    transition: transform 0.2s ease;

    &:hover {
      transform: translateY(-1px);
    }

    .brand-container {
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .brand-icon {
      width: 40px;
      height: 40px;
      background: var(--gradient-primary);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
      box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .brand-text {
      font-size: 1.5rem;
      font-weight: 700;
      background: var(--gradient-primary);
      -webkit-background-clip: text;
      background-clip: text;
    }
  }

  // Custom Toggler
  .custom-toggler {
    border: none;
    padding: 0.5rem;
    background: none;

    &:focus {
      box-shadow: none;
    }

    .custom-toggler-icon {
      display: flex;
      flex-direction: column;
      width: 24px;
      height: 18px;
      justify-content: space-between;

      span {
        display: block;
        height: 2px;
        width: 100%;
        background: var(--text-primary);
        border-radius: 1px;
        transition: all 0.3s ease;
      }
    }

    &:hover .custom-toggler-icon span {
      background: var(--primary-blue);
    }
  }

  // Navigation Links
  .nav-links {
    .nav-link {
      color: var(--text-secondary);
      font-weight: 500;
      font-size: 0.95rem;
      padding: 0.75rem 1rem;
      border-radius: 8px;
      transition: all 0.2s ease;
      display: flex;
      align-items: center;
      position: relative;

      &:hover {
        color: var(--primary-blue);
        background: rgba(59, 130, 246, 0.05);
        transform: translateY(-1px);
      }

      &.active {
        color: var(--primary-blue);
        background: rgba(59, 130, 246, 0.1);
        font-weight: 600;

        &::after {
          content: "";
          position: absolute;
          bottom: 0;
          left: 50%;
          transform: translateX(-50%);
          width: 20px;
          height: 2px;
          background: var(--primary-blue);
          border-radius: 1px;
        }
      }

      i {
        opacity: 0.7;
      }
    }
  }

  // Authentication Section
  .auth-section {
    gap: 0.5rem;

    .login-link {
      color: var(--text-secondary);
      font-weight: 500;
      padding: 0.75rem 1rem;
      border-radius: 8px;
      transition: all 0.2s ease;
      display: flex;
      align-items: center;

      &:hover {
        color: var(--primary-blue);
        background: rgba(59, 130, 246, 0.05);
      }
    }

    .register-btn {
      background: var(--gradient-primary);
      font-weight: 600;
      padding: 0.75rem 1.5rem;
      border-radius: 12px;
      text-decoration: none;
      display: flex;
      align-items: center;
      box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
      transition: all 0.2s ease;

      &:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
      }
    }

    .logout-btn {
      background: none;
      border: 1px solid #ef4444;
      color: #ef4444;
      font-weight: 500;
      padding: 0.75rem 1rem;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.2s ease;
      display: flex;
      align-items: center;

      &:hover {
        background: #ef4444;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
      }
    }
  }

  // User Profile Section
  .user-profile-link {
    text-decoration: none;
    color: inherit;
    padding: 0.5rem 1rem;
    border-radius: 12px;
    transition: all 0.2s ease;

    &:hover {
      background: rgba(59, 130, 246, 0.05);
      transform: translateY(-1px);
    }
  }

  .user-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  .user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    position: relative;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);

    .profile-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .profile-placeholder {
      width: 100%;
      height: 100%;
      background: var(--gradient-primary);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 600;
      font-size: 0.875rem;
    }
  }

  .user-details {
    display: flex;
    flex-direction: column;
    align-items: flex-start;

    .user-name {
      font-weight: 600;
      color: var(--text-primary);
      font-size: 0.9rem;
      line-height: 1.2;
      max-width: 120px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .user-role {
      color: var(--text-muted);
      font-size: 0.75rem;
      line-height: 1;
    }
  }
}

// Responsive Design
@media (max-width: 991px) {
  .modern-navbar {
    .navbar-collapse {
      margin-top: 1rem;
      padding-top: 1rem;
      border-top: 1px solid var(--border-light);
    }

    .nav-links,
    .auth-section {
      flex-direction: column;
      width: 100%;
      gap: 0.5rem;

      .nav-item {
        width: 100%;
      }

      .nav-link {
        width: 100%;
        justify-content: flex-start;
      }
    }

    .user-profile-link {
      width: 100%;
    }

    .auth-section {
      margin-top: 1rem;
      padding-top: 1rem;
      border-top: 1px solid var(--border-light);
    }
  }
}

@media (max-width: 768px) {
  .modern-navbar {
    padding: 0.75rem 0;

    .brand-text {
      font-size: 1.25rem;
    }

    .brand-icon {
      width: 36px;
      height: 36px;
      font-size: 1.1rem;
    }

    .user-details .user-name {
      max-width: 100px;
    }
  }
}

// Scroll Effect
.modern-navbar.scrolled {
  background: rgba(255, 255, 255, 0.98) !important;
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.15);
}

// Animation keyframes
@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.navbar-collapse.show {
  animation: slideDown 0.3s ease;
}
</style>
