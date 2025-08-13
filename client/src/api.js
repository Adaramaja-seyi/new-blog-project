import axios from 'axios'

// Create axios instance with base configuration
const api = axios.create({
    baseURL: '/api', // This will be proxied to your backend
    withCredentials: true, // Important for handling cookies/sessions
    timeout: 10000, // 10 second timeout
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
})

// Request interceptor - add auth token if available
api.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('auth_token')
        if (token) {
            config.headers.Authorization = `Bearer ${token}`
        }
        return config
    },
    (error) => {
        return Promise.reject(error)
    }
)

// Response interceptor - handle common errors
api.interceptors.response.use(
    (response) => {
        return response
    },
    (error) => {
        // Handle common error responses
        if (error.response) {
            switch (error.response.status) {
                case 401:
                    // Unauthorized - redirect to login
                    // router.push('/login')
                    break
                case 403:
                    // Forbidden - show access denied message
                    break
                case 404:
                    // Not found - show 404 page
                    break
                case 500:
                    // Server error - show error message
                    break
            }
        }
        return Promise.reject(error)
    }
)

// API endpoints - add your backend endpoints here
export const blogAPI = {
    // Posts
    getPosts: (page = 1, limit = 10) => api.get(`/posts?page=${page}&limit=${limit}`),
    getPost: (id) => api.get(`/posts/${id}`),
    createPost: (data) => api.post('/posts', data),
    updatePost: (id, data) => api.put(`/posts/${id}`, data),
    deletePost: (id) => api.delete(`/posts/${id}`),

    // Auth
    login: (credentials) => api.post('/login', credentials),
    register: (userData) => api.post('/register', userData),
    logout: () => api.post('/logout'),
    getUserData: () => api.get('/user_data'),
    updateProfile: (data) => api.post('/update_profile', data),

    // Comments
    getComments: (postId) => api.get(`/posts/${postId}/comments`),
    createComment: (postId, data) => api.post(`/posts/${postId}/comments`, data),
    deleteComment: (commentId) => api.delete(`/comments/${commentId}`),

    // Likes
    likePost: (postId) => api.post(`/posts/${postId}/like`),
    unlikePost: (postId) => api.post(`/posts/${postId}/unlike`),

    // Dashboard
    getDashboardStats: () => api.get('/dashboard/stats')
}

export default api

