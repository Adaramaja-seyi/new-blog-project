import axios from "axios";
import { useAuth } from "./stores/auth";
import router from "@/router";


// Create axios instance with base configuration
const api = axios.create({
  baseURL: "/api",
  withCredentials: true,
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
});
// Request interceptor - runs before every request is sent
api.interceptors.request.use(
  (config) => {
    const auth = useAuth();
    if (auth?.token) {
      config.headers.Authorization = `Bearer ${auth.token}`;
    }
    
    if (config.data instanceof FormData) {
      config.headers["Content-Type"] = "multipart/form-data";
    } else {
      config.headers["Content-Type"] = "application/json";
    }

    return config;
  },
  (error) => Promise.reject(error)
);


// Response interceptor - handle common errors
api.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    if (error.response && error.response.status === 401) {
      localStorage.removeItem("token");
      localStorage.removeItem("user");

      
      const currentRoute = router.currentRoute.value;
      if (currentRoute.meta.requiresAuth) {
        router.push({ path: "/login", query: { redirect: currentRoute.fullPath } });
      }
    }
    return Promise.reject(error);
  }
);

// API endpoints 
export const blogAPI = {
  // Auth endpoints
  login: (credentials) => api.post("/login", credentials),
  register: (userData) => api.post("/register", userData),
  logout: () => api.post("/logout"),
  getUserData: (user_id) => api.post("/user_data/", { user_id }),

  updateProfile: (data) => api.post("/update_profile", data),
  changePassword: (data) => api.post("/change-password", data),
  deleteAccount: () => api.delete("/delete-account"),

  // Posts endpoints
  getPosts: (params = {}) => api.get("/posts", { params }),
  getPost: (slug) => api.get(`/posts/${slug}`),
  createPost: (formData) =>
    api.post("/posts", formData, {
      headers: { "Content-Type": "multipart/form-data" },
    }),

  updatePost: (slug, data) => api.post(`/posts/${slug}`, data, {
    headers: {
      'Content-Type': 'multipart/form-data',
    },
  }),
  deletePost: (slug) => api.delete(`/posts/${slug}`),
  uploadImage: (imageFile) => {
    const formData = new FormData();
    formData.append("image", imageFile);
    return api.post("/upload-image", formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });
  },
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
  getComments: (postIdOrSlug) => api.get(`/posts/${postIdOrSlug}/comments`),
  getComment: (id) => api.get(`/comments/${id}`),
  createComment: (postIdOrSlug, data) => api.post(`/posts/${postIdOrSlug}/comments`, data),
  updateComment: (id, data) => api.put(`/comments/${id}`, data),
  deleteComment: (id) => api.delete(`/comments/${id}`),
  approveComment: (id) => api.post(`/comments/${id}/approve`),
  rejectComment: (id) => api.post(`/comments/${id}/reject`),
  markCommentAsSpam: (id) => api.post(`/comments/${id}/spam`),
  getRecentComments: () => api.get('/comments/recent'),

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
  getPostComments: (slug) => api.get(`/posts/${slug}/comments`),
  getRelatedPosts: (slug) => api.get(`/posts/${slug}/related`),

  // Bulk operations
  bulkUpdatePosts: (data) => api.patch("/dashboard/posts/bulk-update", data),
  bulkDeletePosts: (data) =>
    api.delete("/dashboard/posts/bulk-delete", { data }),

  // Dashboard endpoints
  getDashboardStats: () => api.get("/dashboard/stats"),
  getDashboardPosts: (params = {}) => api.get("/dashboard/posts", { params }),
  getDashboardComments: (params = {}) =>
    api.get("/dashboard/comments", { params }),
  getAnalytics: (period = "30") => api.get(`/dashboard/analytics?period=${period}`),
};

export default api;
