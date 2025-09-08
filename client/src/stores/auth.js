// store/auth.js
import { defineStore } from "pinia";
import { blogAPI } from "../api.js";

export const useAuth = defineStore("auth", {
  
  state: () => ({
    user: JSON.parse(localStorage.getItem("user")) || null,
    token: localStorage.getItem("token") || null,
  }),

  getters: {
    isLoggedIn: (state) => !!state.user || !!state.token,
  },

  actions: {
    async login(credentials) {
      try {
        const response = await blogAPI.login(credentials);
        if (response.data.success) {
          const { user, token } = response.data.data;
          // console.log(user, 90);

          this.user = user;
          this.token = token;

          localStorage.setItem("user", JSON.stringify(user));
          localStorage.setItem("token", token);
          return { success: true, data: response.data.data };
        }
        return { success: false, message: response.data.message };
      } catch (error) {
        return {
          success: false,
          message: error.response?.data?.message || "Login failed",
        };
      }
    },

    async register(userData) {
      try {
        const response = await blogAPI.register(userData);
        if (response.data.success) {
          const { user, token } = response.data.data;
          this.user = user;
          this.token = token;
          return { success: true, data: response.data.data };
        }
        return { success: false, message: response.data.message };
      } catch (error) {
        return {
          success: false,
          message: error.response?.data?.message || "Registration failed",
        };
      }
    },

    async logout() {
      try {
        await blogAPI.logout();
        // Clear user data and token
        this.user = null;
        this.token = null;
        localStorage.removeItem("user");
        localStorage.removeItem("token");
      } catch (error) {
        console.error("Logout error:", error);
      } finally {
        this.user = null;
        this.token = null;
      }
    },

    async updateProfile(profileData) {
      try {
        const response = await blogAPI.updateProfile(profileData);
        if (response.data.success) {
          this.user = response.data.data; // update reactive user
          return { success: true, data: response.data.data };
        }
        return { success: false, message: response.data.message };
      } catch (error) {
        return {
          success: false,
          message: error.response?.data?.message || "Profile update failed",
        };
      }
    },
  },
});
