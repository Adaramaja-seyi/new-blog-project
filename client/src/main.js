import { createApp } from "vue";
import { createRouter, createWebHistory } from "vue-router";
import App from "./App.vue";

// Import Bootstrap JS
import "bootstrap/dist/js/bootstrap.bundle.min.js";

// Import global SCSS (includes Bootstrap CSS)
import "./assets/scss/main.scss";
import "./assets/theme.scss";

// Import router configuration
import router from "./router";

// Import auth store
import { useAuth } from "./stores/auth.js";

// Create and mount the app
const app = createApp(App);
app.use(router);

// Initialize auth state
const auth = useAuth();
auth.initializeAuth();

app.mount("#app");
