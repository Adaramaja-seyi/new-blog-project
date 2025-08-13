import axios from 'axios';

const apiClient = axios.create({
    baseURL: '/api',
    withCredentials: true
});

// Add token interceptor
apiClient.interceptors.request.use(config => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Add global response interceptor for 401 Unauthorized
apiClient.interceptors.response.use(
    response => response,
    error => {
        if (error.response && error.response.status === 401) {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            // Try to redirect to login if router is available
            if (window.location.pathname !== '/login') {
                window.location.href = '/login';
            }
        }
        return Promise.reject(error);
    }
);

export default {
    createUsers(data) {
        return apiClient.post('/create_users', data);
    },
    login(data) {
        return apiClient.post('/login', data);
    },

    logout() {
        // Always clear token and redirect, even if API call fails
        return apiClient.post('/logout').catch(() => { }).finally(() => {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            if (window.location.pathname !== '/login') {
                window.location.href = '/login';
            }
        });
    },

    getUserData() {
        return apiClient.get('/user_data');
    },

    updateProfile(data) {
        return apiClient.post('/update_profile', data);
    },

    getPosts() {
        return apiClient.get('/posts');
    },
    createPost(data) {
        const postData = {
            title: data.title,
            content: data.content,
            is_published: Boolean(data.is_published)
        };
        return apiClient.post('/posts', postData);
    },
    getPost(id) {
        return apiClient.get(`/posts/${id}`);
    },
    updatePost(id, data) {
        const postData = {
            title: data.title,
            content: data.content,
            is_published: Boolean(data.is_published)
        };
        return apiClient.put(`/posts/${id}`, postData);
    },
    deletePost(id) {
        return apiClient.delete(`/posts/${id}`);
    },

    // New methods for Dashboard.vue
    getDashboardStats(params) {
        return apiClient.get('/dashboard/stats', { params });
    },
    getRecentPosts() {
        return apiClient.get('/posts/recent');
    },
    getRecentComments() {
        return apiClient.get('/comments/recent');
    },

    // Landing feed methods
    likePost(postId) {
        return apiClient.post(`posts/${postId}/like`, { post_id: postId });
    },
    unlikePost(postId) {
        return apiClient.post(`posts/${postId}/unlike`, { post_id: postId });
    },

    addComment(postId, data) {
        return apiClient.post(`posts/${postId}/comments`, data);
    },

    getComments(postId) {
        return apiClient.get(`posts/${postId}/comments`);
    },

    getPosts(params = {}) {
        return apiClient.get('/posts', { params });
    },

    // Missing methods for LandingFeed.vue
    getStories() {
        return apiClient.get('/stories');
    },

    getContacts() {
        return apiClient.get('/contacts');
    },

    // Additional methods for stories
    createStory(data) {
        return apiClient.post('/stories', data);
    },

    deleteStory(id) {
        return apiClient.delete(`/stories/${id}`);
    },

    // Additional methods for contacts
    addContact(data) {
        return apiClient.post('/contacts', data);
    },

    removeContact(id) {
        return apiClient.delete(`/contacts/${id}`);
    }
};
