<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-white">
    <div class="container">
      <!-- Brand/Logo -->
      <router-link class="navbar-brand" to="/">
        <i class="bi bi-journal-text text-primary-blue me-2"></i>
        ZuraBlog
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
              Feed
            </router-link>
          </li>
        </ul>

        <!-- Authentication Section -->
        <ul class="navbar-nav">
          <template v-if="!isAuthenticated">
            <li class="nav-item">
              <router-link class="nav-link" to="/login"> Login </router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/register"> Register </router-link>
            </li>
          </template>
          <template v-else>
            <li class="nav-item">
              <router-link class="nav-link d-flex align-items-center" to="/dashboard">
                <div class="user-avatar me-2">
                  <i class="bi bi-person-circle"></i>
                </div>
                Dashboard
              </router-link>
            </li>
          </template>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script>
import { useAuth } from "../stores/auth.js";

export default {
  name: "GlobalNavbar",
  data() {
    return {
      auth: null,
    };
  },
  computed: {
    isAuthenticated() {
      if (!this.auth) return false;
      const isAuth = this.auth.isAuthenticated;
      return isAuth;
    },

    user() {
      return this.auth?.user.value || null;
    },
  },
  created() {
    this.auth = useAuth();
  },
  methods: {
    async handleLogout() {
      if (this.auth) {
        await this.auth.logout();
        this.$router.push("/login");
      }
    },
  },
};
</script>

<style scoped lang="scss">
.navbar {
  padding: 1rem 0;
  border-bottom: 1px solid var(--border-light);
  box-shadow: var(--shadow-sm);

  .navbar-brand {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    text-decoration: none;

    &:hover {
      color: var(--primary-blue);
    }
  }

  .nav-link {
    color: var(--text-secondary);
    font-weight: 500;
    transition: color 0.2s ease;

    &:hover {
      color: var(--primary-blue);
    }

    &.active {
      color: var(--primary-blue);
    }
  }

  .user-avatar {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;

    i {
      font-size: 1.25rem;
      color: var(--text-muted);
    }
  }

  .dropdown-menu {
    border: 1px solid var(--border-light);
    box-shadow: var(--shadow-md);
    border-radius: 0.5rem;
    margin-top: 0.5rem;

    .dropdown-item {
      color: var(--text-primary);
      padding: 0.5rem 1rem;

      &:hover {
        background-color: var(--bg-secondary);
        color: var(--primary-blue);
      }

      &.text-danger:hover {
        background-color: #f8d7da;
        color: #721c24;
      }
    }
  }
}

// Responsive adjustments
@media (max-width: 768px) {
  .navbar {
    .navbar-brand {
      font-size: 1.25rem;
    }

    .nav-link {
      padding: 0.5rem 0;
    }
  }
}
</style>
