import { createRouter, createWebHistory } from 'vue-router'

// Import view components (these will be created)
import Home from '../views/Home.vue'
import PostDetail from '../views/PostDetail.vue'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import Dashboard from '../views/Dashboard.vue'
import Profile from '../views/Profile.vue'

const routes = [
    {
        path: '/',
        name: 'Home',
        component: Home,
        meta: { title: 'Blog Platform - Home' }
    },
    {
        path: '/post/:id',
        name: 'PostDetail',
        component: PostDetail,
        meta: { title: 'Blog Post' }
    },
    {
        path: '/login',
        name: 'Login',
        component: Login,
        meta: { title: 'Login' }
    },
    {
        path: '/register',
        name: 'Register',
        component: Register,
        meta: { title: 'Register' }
    },
    {
        path: '/dashboard',
        name: 'Dashboard',
        component: Dashboard,
        meta: {
            title: 'Dashboard',
            requiresAuth: true // Add authentication guard
        }
    },
    {
        path: '/profile',
        name: 'Profile',
        component: Profile,
        meta: {
            title: 'Profile',
            requiresAuth: true // Add authentication guard
        }
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

// Navigation guard for authentication
router.beforeEach((to, from, next) => {
    // Update page title
    document.title = to.meta.title || 'Blog Platform'

    // Check if route requires authentication
    if (to.meta.requiresAuth) {
        // TODO: Implement authentication check
        // const isAuthenticated = checkAuthStatus()
        // if (!isAuthenticated) {
        //   next('/login')
        //   return
        // }
    }

    next()
})

export default router

