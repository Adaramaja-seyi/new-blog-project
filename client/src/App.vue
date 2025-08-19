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

    isAuthRoute() {
      return this.$route.path === "/login" || this.$route.path === "/register";
    },
  },
  async mounted() {
    const auth = useAuth();
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

<style scoped></style>
