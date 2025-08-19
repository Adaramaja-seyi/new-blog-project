<template>
  <div class="dashboard-layout">
    <!-- Sidebar -->
    <DashboardSidebar
      :is-mobile="isMobile"
      :is-mobile-open="isMobileOpen"
      @close-sidebar="closeSidebar"
      @logout="handleLogout"
    />

    <!-- Main Content Area -->
    <div class="dashboard-main">
      <!-- Header -->
      <header class="dashboard-header">
        <div class="header-content">
          <div class="header-left">
            <button
              v-if="isMobile"
              @click="openSidebar"
              class="btn btn-link text-dark d-lg-none"
            >
              <i class="bi bi-list fs-4"></i>
            </button>
            <h1 class="page-title mb-0">{{ pageTitle }}</h1>
          </div>

          <div class="header-right">
            <!-- Search Bar -->
            <div class="search-container me-3">
              <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0">
                  <i class="bi bi-search"></i>
                </span>
                <input
                  type="text"
                  class="form-control border-start-0"
                  placeholder="Search..."
                  v-model="searchQuery"
                  @input="handleSearch"
                />
              </div>
            </div>

            <!-- User Menu -->
            <div class="dropdown">
              <button
                class="btn btn-link text-dark dropdown-toggle d-flex align-items-center"
                type="button"
                ref="dropdownButton"
                @click="toggleDropdown"
              >
                <div class="user-avatar me-2">
                  <img
                    v-if="auth.user?.profile_image"
                    :src="getImageUrl(auth.user?.profile_image)"
                    :alt="auth.user?.name || 'Profile_Image'"
                    style="
                      width: 32px;
                      height: 32px;
                      border-radius: 50%;
                      object-fit: cover;
                    "
                  />
                  <i
                    v-else
                    class="bi bi-person"
                    style="font-size: 1.5rem; color: #888"
                  ></i>
                </div>
                <span class="d-none d-md-inline">{{
                  auth.user?.name || "User"
                }}</span>
              </button>
              <ul class="dropdown-menu dropdown-menu-end" ref="dropdownMenu">
                <li>
                  <router-link
                    class="dropdown-item"
                    :to="`/dashboard/settings/${auth.user?.id}`"
                  >
                    <i class="bi bi-gear me-2"></i>
                    Settings
                  </router-link>
                </li>
                <li>
                  <router-link class="dropdown-item" to="/">
                    <i class="bi bi-house me-2"></i>
                    View Site
                  </router-link>
                </li>
                <li><hr class="dropdown-divider" /></li>
                <li>
                  <button
                    @click="handleLogout"
                    class="dropdown-item text-danger"
                  >
                    <i class="bi bi-box-arrow-right me-2"></i>
                    Logout
                  </button>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </header>

      <!-- Main Content -->
      <main class="dashboard-content">
        <slot></slot>
      </main>
    </div>

    <!-- Mobile Overlay -->
    <div
      v-if="isMobile && isMobileOpen"
      class="mobile-overlay"
      @click="closeSidebar"
    ></div>
  </div>
</template>

<script>
import DashboardSidebar from "./DashboardSidebar.vue";
import { useAuth } from "../stores/auth.js";
import { Dropdown } from "bootstrap";

export default {
  name: "DashboardLayout",
  components: {
    DashboardSidebar,
  },
  props: {
    pageTitle: {
      type: String,
      default: "Dashboard",
    },
  },
  data() {
    return {
      isMobile: false,
      isMobileOpen: false,
      searchQuery: "",
      user: null,
      dropdownInstance: null,
    };
  },

  computed: {
    auth() {
      return useAuth();
    },
  },

  watch: {
    auth: {
      handler(newAuth) {
        this.user = newAuth.user;
      },
      immediate: true,
    },
  },

  mounted() {
    this.dropdownInstance = new Dropdown(this.$refs.dropdownButton);

    this.getImageUrl = (path) => {
      return `http://localhost:8000${path}`;
    };
    this.checkScreenSize();
    this.loadUserData();
    window.addEventListener("resize", this.checkScreenSize);
  },
  beforeUnmount() {
    window.removeEventListener("resize", this.checkScreenSize);
  },
  methods: {
    toggleDropdown() {
      if (this.dropdownInstance) {
        this.dropdownInstance.toggle();
      }
    },
    getImageUrl(path) {
      return `http://localhost:8000${path}`;
    },
    checkScreenSize() {
      this.isMobile = window.innerWidth < 992;
      if (!this.isMobile) {
        this.isMobileOpen = false;
      }
    },
    openSidebar() {
      this.isMobileOpen = true;
    },
    closeSidebar() {
      this.isMobileOpen = false;
    },
    handleSearch() {
      this.$emit("search", this.searchQuery);
    },
    loadUserData() {
      const userData = localStorage.getItem("user");
      if (userData) {
        this.user = JSON.parse(userData);
      }
    },
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
.dashboard-layout {
  display: flex;
  min-height: 100vh;
  background-color: var(--bg-secondary);

  .dashboard-main {
    flex: 1;
    margin-left: 240px;
    display: flex;
    flex-direction: column;

    .dashboard-header {
      background-color: var(--bg-primary);
      border-bottom: 1px solid var(--border-light);
      padding: 1rem 2rem;
      box-shadow: var(--shadow-sm);
      position: sticky;
      top: 0;
      z-index: 100;

      .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;

        .header-left {
          display: flex;
          align-items: center;
          gap: 1rem;

          .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
          }
        }

        .header-right {
          display: flex;
          align-items: center;
          gap: 1rem;

          .search-container {
            width: 300px;

            .input-group {
              .input-group-text {
                border-color: var(--border-color);
                background-color: var(--bg-primary);
                color: var(--text-muted);
              }

              .form-control {
                border-color: var(--border-color);
                background-color: var(--bg-primary);

                &:focus {
                  border-color: var(--primary-blue);
                  box-shadow: 0 0 0 0.2rem rgba(77, 166, 255, 0.15);
                }
              }
            }
          }

          .dropdown {
            .btn-link {
              text-decoration: none;
              color: var(--text-primary);
              display: flex;
              align-items: center;
              gap: 0.5rem;

              &:hover {
                color: var(--primary-blue);
              }
            }

            .user-avatar {
              width: 32px;
              height: 32px;
              border-radius: 50%;
              background-color: var(--bg-secondary);
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
              border-radius: var(--border-radius);

              .dropdown-item {
                padding: 0.5rem 1rem;
                color: var(--text-primary);

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
        }
      }
    }

    .dashboard-content {
      flex: 1;
      padding: 2rem;
      background-color: var(--bg-secondary);
    }
  }
}

// Mobile overlay
.mobile-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 999;
}

// Responsive design
@media (max-width: 768px) {
  .dashboard-layout {
    .dashboard-main {
      margin-left: 0;

      .dashboard-header {
        padding: 1rem;

        .header-content {
          .header-right {
            .search-container {
              display: none;
            }
          }
        }
      }

      .dashboard-content {
        padding: 1rem;
      }
    }
  }
}

@media (max-width: 576px) {
  .dashboard-layout {
    .dashboard-main {
      .dashboard-header {
        .header-content {
          .header-right {
            .search-container {
              display: none;
            }
          }
        }
      }
    }
  }
}
</style>
