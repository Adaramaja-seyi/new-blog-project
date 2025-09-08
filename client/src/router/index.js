import { createRouter, createWebHistory } from "vue-router";
import { useAuth } from "@/stores/auth";

// Import view components
import Home from "../views/Home.vue";
import PostDetail from "../views/PostDetail.vue";
import Login from "../views/Login.vue";
import Register from "../views/Register.vue";
import DashboardWrapper from "../components/DashboardWrapper.vue";
import DashboardHome from "../views/DashboardHome.vue";
import Profile from "../views/Profile.vue";

const routes = [
  {
    path: "/",
    name: "Home",
    component: Home,
    meta: { title: "Blog Platform - Feed" },
  },
  {
    path: "/feed",
    name: "Feed",
    component: Home,
    meta: { title: "Blog Platform - Feed" },
  },
  {
    path: "/post/:slug",
    name: "PostDetail",
    component: PostDetail,
    meta: { title: "Blog Post" },
  },
  {
    path: "/login",
    name: "Login",
    component: Login,
    meta: { title: "Login" },
  },
  {
    path: "/register", 
    name: "Register",
    component: Register,
    meta: { title: "Register" },
  },
  {
    path: "/dashboard",
    component: DashboardWrapper,
    meta: {
      requiresAuth: true,
    },
    children: [
      {
        path: "",
        name: "Dashboard",
        component: DashboardHome,
        meta: {
          title: "Dashboard Overview",
          requiresAuth: true,
        },
      },
      {
        path: "home",
        name: "DashboardHome",
        component: DashboardHome,
        meta: {
          title: "Dashboard Home",
          requiresAuth: true,
        },
      },
      {
        path: "posts",
        name: "DashboardPosts",
        component: () => import("../views/DashboardPosts.vue"),
        meta: {
          title: "My Posts",
          requiresAuth: true,
        },
      },
      {
        path: "drafts",
        name: "DashboardDrafts",
        component: () => import("../views/DashboardDrafts.vue"),
        meta: {
          title: "Drafts",
          requiresAuth: true,
        },
      },
      {
        path: "create",
        name: "CreatePost",
        component: () => import("../views/CreatePost.vue"),
        meta: {
          title: "Create Post",
          requiresAuth: true,
        },
      },
      {
        path: "edit/:slug",
        name: "EditPost",
        component: () => import("../views/CreatePost.vue"),
        meta: {
          title: "Edit Post",
          requiresAuth: true,
        },
      },
      {
        path: "analytics",
        name: "DashboardAnalytics",
        component: () => import("../views/Analytics.vue"),
        meta: {
          title: "Analytics",
          requiresAuth: true,
        },
      },
      {
        path: "settings/:id",
        name: "DashboardSettings",
        component: Profile,
        meta: {
          title: "Settings",
          requiresAuth: true,
        },
      },
      {
        path: "profile",
        name: "DashboardProfile",
        component: Profile,
        meta: {
          title: "Profile Settings",
          requiresAuth: true,
        },
      },
    ],
  },
  {
    path: "/profile",
    name: "Profile",
    component: Profile,
    meta: {
      title: "Profile",
      requiresAuth: true,
    },
  },
  // Public user profile (view by id)
  {
    path: "/user/:id",
    name: "UserProfile",
    component: Profile,
    meta: {
      title: "User Profile",
    },
  },
  // Category listing route (uses Home view and reads slug param)
  {
    path: "/category/:slug",
    name: "Category",
    component: Home,
    meta: {
      title: "Category",
    },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// import { useAuth } from "../stores/auth";

router.beforeEach((to, from, next) => {
  document.title = to.meta.title || "Blog Platform";

  const auth = useAuth();

  // Try restoring from localStorage if not already loaded
  if (!auth.user || !auth.token) {
    const savedUser = localStorage.getItem("user");
    const savedToken = localStorage.getItem("token");

    if (savedUser && savedToken) {
      auth.user = JSON.parse(savedUser);
      auth.token = savedToken;
    }
  }

  if (to.meta.requiresAuth && !auth.isLoggedIn) {
    // redirect to login if not authenticated
    next({ path: "/login", query: { redirect: to.fullPath } });
    return;
  }

  next();
});


export default router;
