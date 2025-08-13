<script>
import GlobalNavbar from "./components/GlobalNavbar.vue";
import Footer from "./components/Footer.vue";
import { useAuth } from "./stores/auth.js";

export default {
  name: "App",
  components: {
    GlobalNavbar,
    Footer,
  },
  data() {
    return {
      authInitialized: false,
    };
  },
  computed: {
    isDashboardRoute() {
      return this.$route.path.startsWith("/dashboard");
    },
    /*************  ✨ Windsurf Command ⭐  *************/
    /**
     * Returns true if the current route is either /login or /register
     * @returns {boolean}
     */
    /*******  ec33afa5-addf-49bc-9760-936d2c43e236  *******/
    isAuthRoute() {
      return this.$route.path === "/login" || this.$route.path === "/register";
    },
    isAuthenticated() {
      const auth = useAuth();
      return auth.isAuthenticated.value;
    },
  },
  async mounted() {
    // Initialize authentication on app start
    const auth = useAuth();
    await auth.initializeAuth();
    this.authInitialized = true;
  },
};
</script>

<template>
  <div id="app">
    <!-- Hide navbar and footer on login/register pages -->
    <template v-if="isAuthRoute">
      <router-view />
    </template>
    <!-- Hide navbar and footer on dashboard routes, only show if authenticated -->
    <template v-else-if="isDashboardRoute">
      <!-- <template v-if="isAuthenticated"> -->
        <router-view />
      <!-- </template> -->
    </template>
    <!-- Show navbar and footer everywhere else, regardless of authentication -->
    <template v-else>
      <GlobalNavbar />
      <main class="main-content">
        <router-view />
      </main>
      <Footer />
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
