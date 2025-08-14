import { ref, computed } from "vue";
import { blogAPI } from "../api.js";

// Reactive state
const user = ref(JSON.parse(localStorage.getItem("user") || "null"));
const token = ref(localStorage.getItem("auth_token") || null);
const isAuthenticated = computed(() => !!token.value && !!user.value);

// Initialize user data if token exists
const initializeAuth = async () => {
  if (token.value) {
    try {
      const response = await blogAPI.getUserData();
      if (response.data.success) {
        user.value = response.data.data.user;
      } else {
        // Token is invalid, clear it
        logout();
      }
    } catch (error) {
      console.error("Failed to initialize auth:", error);
      logout();
    }
  }
};

// Login function
const login = async (credentials) => {
  try {
    const response = await blogAPI.login(credentials);
    if (response.data.success) {
      const { user: userData, token: authToken } = response.data.data;
      user.value = userData;
      token.value = authToken;
      localStorage.setItem("auth_token", authToken);
      localStorage.setItem("user", JSON.stringify(userData));
      return { success: true, data: response.data.data };
    } else {
      return { success: false, message: response.data.message };
    }
  } catch (error) {
    console.error("Login error:", error);
    if (error.response?.data) {
      return {
        success: false,
        message: error.response.data.message || "Login failed",
        errors: error.response.data.errors,
      };
    }
    return { success: false, message: "Network error occurred" };
  }
};

// Register function
const register = async (userData) => {
  try {
    const response = await blogAPI.register(userData);
    if (response.data.success) {
      const { user: newUser, token: authToken } = response.data.data;
      user.value = newUser;
      token.value = authToken;
      localStorage.setItem("auth_token", authToken);
      localStorage.setItem("user", JSON.stringify(newUser));
      return { success: true, data: response.data.data };
    } else {
      return { success: false, message: response.data.message };
    }
  } catch (error) {
    console.error("Register error:", error);
    if (error.response?.data) {
      return {
        success: false,
        message: error.response.data.message || "Registration failed",
        errors: error.response.data.errors,
      };
    }
    return { success: false, message: "Network error occurred" };
  }
};

// Logout function
const logout = async () => {
  try {
    if (token.value) {
      await blogAPI.logout();
    }
  } catch (error) {
    console.error("Logout error:", error);
  } finally {
    user.value = null;
    token.value = null;
    localStorage.removeItem("auth_token");
    localStorage.removeItem("user");
  }
};

// Update profile function
const updateProfile = async (profileData) => {
  try {
    const response = await blogAPI.updateProfile(profileData);
    if (response.data.success) {
      user.value = response.data.data.user;
      localStorage.setItem("user", JSON.stringify(response.data.data.user));
      return { success: true, data: response.data.data };
    } else {
      return { success: false, message: response.data.message };
    }
  } catch (error) {
    console.error("Profile update error:", error);
    if (error.response?.data) {
      return {
        success: false,
        message: error.response.data.message || "Profile update failed",
        errors: error.response.data.errors,
      };
    }
    return { success: false, message: "Network error occurred" };
  }
};

// Export the auth store
export const useAuth = () => {
  return {
    user: computed(() => user.value),
    token: computed(() => token.value),
    isAuthenticated,
    login,
    register,
    logout,
    updateProfile,
    initializeAuth,
  };
};
