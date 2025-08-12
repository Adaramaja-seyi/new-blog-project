import { createRouter, createWebHistory } from 'vue-router'

// Import view components
import Home from '../views/Home.vue'
import PostDetail from '../views/PostDetail.vue'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import DashboardWrapper from '../components/DashboardWrapper.vue'
import DashboardHome from '../views/DashboardHome.vue'
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
        component: DashboardWrapper,
        meta: {
            requiresAuth: true,
            requiresAuthor: true
        },
        children: [
            {
                path: '',
                name: 'Dashboard',
                component: DashboardHome,
                meta: {
                    title: 'Dashboard Overview',
                    requiresAuth: true,
                    requiresAuthor: true
                }
            },
            {
                path: 'home',
                name: 'DashboardHome',
                component: DashboardHome,
                meta: {
                    title: 'Dashboard Home',
                    requiresAuth: true,
                    requiresAuthor: true
                }
            },
            {
                path: 'posts',
                name: 'DashboardPosts',
                component: () => import('../views/DashboardPosts.vue'),
                meta: {
                    title: 'My Posts',
                    requiresAuth: true,
                    requiresAuthor: true
                }
            },
            {
                path: 'drafts',
                name: 'DashboardDrafts',
                component: () => import('../views/DashboardPosts.vue'),
                meta: {
                    title: 'Drafts',
                    requiresAuth: true,
                    requiresAuthor: true
                }
            },
            {
                path: 'create',
                name: 'CreatePost',
                component: () => import('../views/CreatePost.vue'),
                meta: {
                    title: 'Create Post',
                    requiresAuth: true,
                    requiresAuthor: true
                }
            },
            {
                path: 'edit/:id',
                name: 'EditPost',
                component: () => import('../views/CreatePost.vue'),
                meta: {
                    title: 'Edit Post',
                    requiresAuth: true,
                    requiresAuthor: true
                }
            },
            {
                path: 'comments',
                name: 'DashboardComments',
                component: () => import('../views/DashboardComments.vue'),
                meta: {
                    title: 'Comments',
                    requiresAuth: true,
                    requiresAuthor: true
                }
            },
            {
                path: 'analytics',
                name: 'DashboardAnalytics',
                component: DashboardHome,
                meta: {
                    title: 'Analytics',
                    requiresAuth: true,
                    requiresAuthor: true
                }
            },
            {
                path: 'settings',
                name: 'DashboardSettings',
                component: Profile,
                meta: {
                    title: 'Settings',
                    requiresAuth: true,
                    requiresAuthor: true
                }
            },
            {
                path: 'profile',
                name: 'DashboardProfile',
                component: Profile,
                meta: {
                    title: 'Profile Settings',
                    requiresAuth: true,
                    requiresAuthor: true
                }
            }
        ]
    },
    {
        path: '/profile',
        name: 'Profile',
        component: Profile,
        meta: {
            title: 'Profile',
            requiresAuth: true
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
        const user = localStorage.getItem('user')
        if (!user) {
            next('/login')
            return
        }

        // Check if route requires author role
        if (to.meta.requiresAuthor) {
            try {
                const userData = JSON.parse(user)
                if (userData.role !== 'author') {
                    next('/')
                    return
                }
            } catch {
                next('/login')
                return
            }
        }
    }

    next()
})

export default router

