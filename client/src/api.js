import axios from "axios";

// Create axios instance with base configuration
const api = axios.create({
  baseURL: "/api", // This will be proxied to your backend
  withCredentials: true, // Important for handling cookies/sessions
  timeout: 10000, // 10 second timeout
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
});

// Request interceptor - add auth token if available
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem("auth_token");
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor - handle common errors
api.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    // Handle common error responses
    if (error.response) {
      switch (error.response.status) {
        case 401:
          // Unauthorized - redirect to login
          // router.push('/login')
          break;
        case 403:
          // Forbidden - show access denied message
          break;
        case 404:
          // Not found - show 404 page
          break;
        case 500:
          // Server error - show error message
          break;
      }
    }
    return Promise.reject(error);
  }
);

// API endpoints - add your backend endpoints here
export const blogAPI = {
  // Auth endpoints
  login: (credentials) => api.post("/login", credentials),
  register: (userData) => api.post("/register", userData),
  logout: () => api.post("/logout"),
  getUserData: () => api.get("/user_data"),
  updateProfile: (data) => api.post("/update_profile", data),

  // Posts endpoints
  getPosts: (params = {}) => api.get("/posts", { params }),
  getPost: (id) => api.get(`/posts/${id}`),
  createPost: (data) => api.post("/posts", data),
  updatePost: (id, data) => api.put(`/posts/${id}`, data),
  deletePost: (id) => api.delete(`/posts/${id}`),
  searchPosts: (query) => api.get(`/posts/search/${query}`),
  getPostsByTag: (tag) => api.get(`/posts/tag/${tag}`),
  getPostsByStatus: (status) => api.get(`/posts/status/${status}`),

  // Categories endpoints
  getCategories: () => api.get("/categories"),
  getCategory: (id) => api.get(`/categories/${id}`),
  createCategory: (data) => api.post("/categories", data),
  updateCategory: (id, data) => api.put(`/categories/${id}`, data),
  deleteCategory: (id) => api.delete(`/categories/${id}`),

  // Tags endpoints
  getTags: () => api.get("/tags"),
  getTag: (id) => api.get(`/tags/${id}`),
  createTag: (data) => api.post("/tags", data),
  updateTag: (id, data) => api.put(`/tags/${id}`, data),
  deleteTag: (id) => api.delete(`/tags/${id}`),
  searchTags: (query) => api.get("/tags/search", { params: { q: query } }),

  // Comments endpoints
  getComments: (postId) => api.get(`/posts/${postId}/comments`),
  getComment: (id) => api.get(`/comments/${id}`),
  createComment: (postId, data) => api.post(`/posts/${postId}/comments`, data),
  updateComment: (id, data) => api.put(`/comments/${id}`, data),
  deleteComment: (id) => api.delete(`/comments/${id}`),
  approveComment: (id) => api.post(`/comments/${id}/approve`),
  rejectComment: (id) => api.post(`/comments/${id}/reject`),
  markCommentAsSpam: (id) => api.post(`/comments/${id}/spam`),

  // Likes endpoints
  likePost: (postId) => api.post(`/posts/${postId}/like`),
  unlikePost: (postId) => api.delete(`/posts/${postId}/like`),
  checkLikeStatus: (postId) => api.get(`/posts/${postId}/like-status`),
  likeComment: (commentId) => api.post(`/comments/${commentId}/like`),
  unlikeComment: (commentId) => api.post(`/comments/${commentId}/unlike`),

  // Comments management
  updateCommentStatus: (id, status) =>
    api.patch(`/comments/${id}/status`, { status }),

  // Public post endpoints
  getPost: (slug) => api.get(`/posts/${slug}`),
  getPostComments: (slug) => api.get(`/posts/${slug}/comments`),
  getRelatedPosts: (slug) => api.get(`/posts/${slug}/related`),

  // Bulk operations
  bulkUpdatePosts: (data) => api.patch("/dashboard/posts/bulk-update", data),
  bulkDeletePosts: (data) =>
    api.delete("/dashboard/posts/bulk-delete", { data }),
  bulkUpdateComments: (data) =>
    api.patch("/dashboard/comments/bulk-update", data),
  bulkDeleteComments: (data) =>
    api.delete("/dashboard/comments/bulk-delete", { data }),

  // Dashboard endpoints
  getDashboardStats: () => api.get("/dashboard/stats"),
  getDashboardPosts: (params = {}) => api.get("/dashboard/posts", { params }),
  getDashboardComments: (params = {}) =>
    api.get("/dashboard/comments", { params }),
};

export default api;
