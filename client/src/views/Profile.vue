<template>
  <div class="container">
    <div class="row">
      <!-- Profile Sidebar -->
      <div class="col-lg-4 my-4">
        <div class="card">
          <div class="card-body text-center">
            <!-- Profile Avatar -->
            <div class="mb-3">
              <div class="position-relative d-inline-block">
                <div
                  class="profile-avatar rounded-circle d-flex align-items-center justify-content-center mx-auto overflow-hidden"
                  :style="{
                    backgroundImage: user?.profile_image
                      ? `url(${getProfileImageUrl(user.profile_image)})`
                      : 'none',
                    backgroundColor: user?.profile_image
                      ? 'transparent'
                      : '#6c757d',
                  }"
                >
                  <i
                    v-if="!user?.profile_image"
                    class="bi bi-person text-white"
                    style="font-size: 3rem"
                  ></i>
                  <img
                    v-else
                    :src="getProfileImageUrl(user.profile_image)"
                    :alt="user?.name || 'Profile_Image'"
                    class="w-100 h-100 object-fit-cover"
                  />
                </div>

                <!-- Upload Button Overlay -->
                <button
                  @click="$refs.profileImageInput.click()"
                  class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle"
                  style="width: 32px; height: 32px; padding: 0"
                  title="Upload Profile Image"
                >
                  <i class="bi bi-camera"></i>
                </button>

                <!-- Hidden File Input -->
                <input
                  ref="profileImageInput"
                  type="file"
                  accept="image/*"
                  @change="handleProfileImageUpload"
                  style="display: none"
                />
              </div>

              <!-- Upload Progress -->
              <div v-if="uploadingImage" class="mt-2">
                <div class="progress" style="height: 4px">
                  <div
                    class="progress-bar progress-bar-striped progress-bar-animated"
                    :style="{ width: uploadProgress + '%' }"
                  ></div>
                </div>
                <small class="text-muted"
                  >Uploading... {{ uploadProgress }}%</small
                >
              </div>
            </div>

            <!-- Profile Info -->
            <h5 class="mb-1">{{ user?.name || "User Name" }}</h5>
            <p class="text-muted mb-3">
              {{ user?.email || "user@example.com" }}
            </p>

            <!-- Member Since -->
            <div class="border-top pt-3">
              <small class="text-muted">
                Member since {{ formatDate(user?.created_at) }}
              </small>
            </div>
          </div>
        </div>

        <!-- Quick Stats -->
        <div class="card mt-3">
          <div class="card-header">
            <h6 class="mb-0">
              <i class="bi bi-graph-up me-2"></i>
              Quick Stats
            </h6>
          </div>
          <div class="card-body">
            <div class="row text-center">
              <div class="col-4">
                <div class="stat-item">
                  <h4 class="mb-1 text-primary">{{ stats.totalPosts }}</h4>
                  <small class="text-muted">Posts</small>
                </div>
              </div>
              <div class="col-4">
                <div class="stat-item">
                  <h4 class="mb-1 text-success">{{ stats.totalViews }}</h4>
                  <small class="text-muted">Views</small>
                </div>
              </div>
              <div class="col-4">
                <div class="stat-item">
                  <h4 class="mb-1 text-info">{{ stats.totalComments }}</h4>
                  <small class="text-muted">Comments</small>
                </div>
              </div>
            </div>

            <!-- Additional Stats -->
            <div class="row text-center mt-3">
              <div class="col-6">
                <div class="stat-item">
                  <h6 class="mb-1 text-warning">{{ stats.totalLikes }}</h6>
                  <small class="text-muted">Likes</small>
                </div>
              </div>
              <div class="col-6">
                <div class="stat-item">
                  <h6 class="mb-1 text-secondary">
                    {{ stats.avgViewsPerPost }}
                  </h6>
                  <small class="text-muted">Avg Views</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Profile Content -->
      <div class="col-lg-8 my-4">
        <!-- Profile Header -->
        <div
          class="d-flex justify-content-between align-items-center mb-4"
          v-if="this.currentUser === user_id"
        >
          <div>
            <h1 class="mb-2">Profile Settings</h1>
            <p class="text-muted mb-0">
              Manage your account information and preferences
            </p>
          </div>
        </div>

        <!-- Profile Tabs -->
        <div class="card">
          <div class="card-header">
            <ul
              class="nav nav-tabs card-header-tabs"
              id="profileTabs"
              role="tablist"
            >
              <li class="nav-item" role="presentation">
                <button
                  class="nav-link active"
                  id="profile-tab"
                  data-bs-toggle="tab"
                  data-bs-target="#profile"
                  type="button"
                  role="tab"
                >
                  <i class="bi bi-person me-2"></i>
                  Profile
                </button>
              </li>
              <li
                class="nav-item"
                role="presentation"
                v-if="this.currentUser === this.user_id"
              >
                <button
                  class="nav-link"
                  id="security-tab"
                  data-bs-toggle="tab"
                  data-bs-target="#security"
                  type="button"
                  role="tab"
                >
                  <i class="bi bi-shield-lock me-2"></i>
                  Security
                </button>
              </li>
            </ul>
          </div>

          <div class="card-body">
            <div class="tab-content" id="profileTabsContent">
              <!-- Profile Tab -->
              <div
                class="tab-pane fade show active"
                id="profile"
                role="tabpanel"
              >
                <form @submit.prevent="updateProfile">
                  <div class="row">
                    <div
                      class="col-md-6 mb-3"
                      v-if="this.currentUser === user_id"
                    >
                      <label for="name" class="form-label">Full Name</label>
                      <input
                        id="name"
                        v-model="profileForm.name"
                        type="text"
                        class="form-control"
                        :class="{ 'is-invalid': errors.name }"
                        required
                      />
                      <div v-if="errors.firstName" class="invalid-feedback">
                        {{ errors.firstName }}
                      </div>
                    </div>

                    <div
                      class="col-md-6 mb-3"
                      v-if="this.currentUser === user_id"
                    >
                      <label for="email" class="form-label">Email</label>
                      <input
                        id="email"
                        v-model="profileForm.email"
                        type="email"
                        class="form-control"
                        :class="{ 'is-invalid': errors.email }"
                        required
                      />
                      <div v-if="errors.email" class="invalid-feedback">
                        {{ errors.email }}
                      </div>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label for="bio" class="form-label">Bio</label>
                    <textarea
                      id="bio"
                      v-model="profileForm.bio"
                      :disabled="currentUser !== user_id"
                      class="form-control"
                      rows="4"
                      placeholder="Tell us about yourself..."
                    ></textarea>
                  </div>

                  <button
                    type="submit"
                    class="btn btn-primary"
                    :disabled="updating"
                    v-if="this.currentUser === user_id"
                  >
                    <span
                      v-if="updating"
                      class="spinner-border spinner-border-sm me-2"
                      role="status"
                    ></span>
                    {{ updating ? "Updating..." : "Update Profile" }}
                  </button>
                </form>
              </div>

              <!-- Security Tab -->
              <div class="tab-pane fade" id="security" role="tabpanel">
                <form @submit.prevent="changePassword">
                  <div class="mb-3">
                    <label for="currentPassword" class="form-label"
                      >Current Password</label
                    >
                    <input
                      id="currentPassword"
                      v-model="passwordForm.currentPassword"
                      type="password"
                      class="form-control"
                      :class="{ 'is-invalid': errors.currentPassword }"
                      required
                    />
                    <div v-if="errors.currentPassword" class="invalid-feedback">
                      {{ errors.currentPassword }}
                    </div>
                  </div>

                  <div class="mb-3">
                    <label for="newPassword" class="form-label"
                      >New Password</label
                    >
                    <input
                      id="newPassword"
                      v-model="passwordForm.newPassword"
                      type="password"
                      class="form-control"
                      :class="{ 'is-invalid': errors.newPassword }"
                      required
                    />
                    <div class="form-text">
                      Password must be at least 8 characters long
                    </div>
                  </div>

                  <div class="mb-3">
                    <label for="confirmPassword" class="form-label"
                      >Confirm New Password</label
                    >
                    <input
                      id="confirmPassword"
                      v-model="passwordForm.confirmPassword"
                      type="password"
                      class="form-control"
                      :class="{ 'is-invalid': errors.confirmPassword }"
                      required
                    />
                    <div v-if="errors.confirmPassword" class="invalid-feedback">
                      {{ errors.confirmPassword }}
                    </div>
                  </div>

                  <button
                    type="submit"
                    class="btn btn-primary"
                    :disabled="changingPassword"
                  >
                    <span
                      v-if="changingPassword"
                      class="spinner-border spinner-border-sm me-2"
                      role="status"
                    ></span>
                    {{ changingPassword ? "Changing..." : "Change Password" }}
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>

        <!-- Danger Zone -->
        <div
          class="card my-4 border-danger"
          v-if="this.currentUser === user_id"
        >
          <div class="card-header bg-danger text-white">
            <h6 class="mb-0">
              <i class="bi bi-exclamation-triangle me-2"></i>
              Danger Zone
            </h6>
          </div>
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-md-8">
                <h6 class="text-danger mb-2">Delete Account</h6>
                <p class="text-muted mb-0">
                  Once you delete your account, there is no going back. This
                  will permanently delete all your posts, comments, and data.
                </p>
              </div>
              <div class="col-md-4 text-end">
                <button
                  @click="showDeleteAccountModal = true"
                  class="btn btn-outline-danger"
                  :disabled="deletingAccount"
                >
                  <span
                    v-if="deletingAccount"
                    class="spinner-border spinner-border-sm me-2"
                    role="status"
                  ></span>
                  {{ deletingAccount ? "Deleting..." : "Delete Account" }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Account Modal -->
    <div
      class="modal fade"
      id="deleteAccountModal"
      tabindex="-1"
      ref="deleteAccountModal"
      aria-labelledby="deleteAccountModalLabel"
      v-if="this.currentUser === user_id"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header border-danger">
            <h5 class="modal-title text-danger">
              <i class="bi bi-exclamation-triangle me-2"></i>
              Delete Account
            </h5>
            <button
              type="button"
              class="btn-close"
              @click="showDeleteAccountModal = false"
            ></button>
          </div>
          <div class="modal-body">
            <p class="text-danger fw-bold">This action cannot be undone!</p>
            <p>
              Are you absolutely sure you want to delete your account? This
              will:
            </p>
            <ul class="text-muted">
              <li>Permanently delete all your posts</li>
              <li>Remove all your comments</li>
              <li>Delete your profile and settings</li>
              <li>Remove all associated data</li>
            </ul>
            <div class="alert alert-warning">
              <i class="bi bi-exclamation-triangle me-2"></i>
              <strong>Warning:</strong> This action is irreversible and will
              immediately log you out.
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              @click="showDeleteAccountModal = false"
            >
              Cancel
            </button>
            <button
              type="button"
              class="btn btn-danger"
              @click="deleteAccount"
              :disabled="deletingAccount"
            >
              <span
                v-if="deletingAccount"
                class="spinner-border spinner-border-sm me-2"
                role="status"
              ></span>
              {{
                deletingAccount
                  ? "Deleting Account..."
                  : "Yes, Delete My Account"
              }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { blogAPI } from "../api.js";
import { useAuth } from "../stores/auth.js";
import { Modal } from "bootstrap";
import { useToast } from "vue-toastification";

export default {
  name: "Profile",
  setup() {
    const toast = useToast();
    return { toast };
  },
  data() {
    return {
      user: null,
      user_id: null,
      stats: {
        totalPosts: 0,
        totalViews: 0,
        totalComments: 0,
        totalLikes: 0,
        avgViewsPerPost: 0,
      },
      profileForm: {
        name: "",
        email: "",
        bio: "",
      },
      passwordForm: {
        currentPassword: "",
        newPassword: "",
        confirmPassword: "",
      },
      errors: {},
      updating: false,
      changingPassword: false,
      deletingAccount: false,
      showDeleteAccountModal: false,
      uploadingImage: false,
      uploadProgress: 0,
      refreshingStats: false,
      testingAPI: false,
      testingUpload: false,
    };
  },
  mounted() {
    this.user_id = this.$route.params.id;
    this.fetchUserData(this.user_id);
  },

  methods: {
    getProfileImageUrl(path) {
      if (!path) return null;
      // Change to your backend URL and port if needed
      return `http://localhost:8000${path}`;
    },
    async fetchUserData(user_id) {
      try {
        const response = await blogAPI.getUserData({ user_id });

        if (response.data?.success) {
          const { user } = response.data.data;
          this.user = user;

          // Calculate stats
          const posts = user?.posts || [];
          const totalViews = posts.reduce(
            (sum, p) => sum + (p.views_count || 0),
            0
          );
          const totalLikes = posts.reduce(
            (sum, p) => sum + (p.likes_count || 0),
            0
          );

          this.stats = {
            totalPosts: posts.length,
            totalViews: totalViews,
            totalComments: (user?.comments || []).length,
            totalLikes: totalLikes,
            avgViewsPerPost:
              posts.length > 0 ? Math.round(totalViews / posts.length) : 0,
          };

          this.profileForm = {
            name: user.name || "",
            email: user.email || "",
            bio: user.bio || "",
          };
        }
      } catch (error) {
        console.error("Error fetching user data:", error);
        this.toast.error("Error fetching user data");
      }
    },

    async handleProfileImageUpload(event) {
      const file = event.target.files[0];
      if (!file) return;

      // Validate file type and size
      if (!file.type.startsWith("image/")) {
        this.toast.info("Only image files are allowed");
        return;
      }

      if (file.size > 5 * 1024 * 1024) {
        // 5MB limit
        this.toast.info("Image size must be less than 5MB");
        return;
      }

      try {
        this.uploadingImage = true;
        this.uploadProgress = 0;

        // Simulate upload progress
        const progressInterval = setInterval(() => {
          if (this.uploadProgress < 90) {
            this.uploadProgress += Math.random() * 20;
          }
        }, 200);

        const formData = new FormData();
        formData.append("profile_image", file);

        const response = await blogAPI.updateProfile(formData);

        clearInterval(progressInterval);
        this.uploadProgress = 100;

        if (response.data?.success) {
          // Ensure profile_image is a valid path
          this.user.profile_image = response.data.data.user.profile_image;
          this.auth.user.profile_image = response.data.data.user.profile_image;
          localStorage.setItem("user", JSON.stringify(this.user));

          this.toast.success("Profile image updated successfully!");
         
        }
      } catch (error) {
        console.error("Error uploading profile image:", error);
        this.toast.error("Failed to upload profile image. Please try again.");
      } finally {
        setTimeout(() => {
          this.uploadingImage = false;
          this.uploadProgress = 0;
        }, 500);
        event.target.value = "";
      }
    },

    async updateProfile() {
      // Reset errors
      this.errors = {};

      // Validate form
      if (!this.validateProfileForm()) {
        return;
      }

      try {
        this.updating = true;
        const payload = new FormData();
        if (this.profileForm.name !== undefined)
          payload.append("name", this.profileForm.name);
        
        if (this.profileForm.email !== undefined)
          payload.append("email", this.profileForm.email);
        if (this.profileForm.bio !== undefined)
          payload.append("bio", this.profileForm.bio);

        const response = await blogAPI.updateProfile(payload);
        if (response.data?.success) {
          this.user = response.data.data.user;
          localStorage.setItem("user", JSON.stringify(this.user));
          this.toast.success("Profile updated successfully!");

          // Refresh stats after profile update
          setTimeout(() => {
            window.location.reload();
          }, 500);
        }
      } catch (error) {
        console.error("Error updating profile:", error);

        if (error.response?.data?.errors) {
          this.errors = error.response.data.errors;
        } else {
          this.toast.error("Failed to update profile. Please try again.");
        }
      } finally {
        this.updating = false;
      }
    },

    async changePassword() {
      // Reset errors
      this.errors = {};

      // Validate form
      if (!this.validatePasswordForm()) {
        return;
      }

      try {
        this.changingPassword = true;

        const response = await blogAPI.changePassword({
          current_password: this.passwordForm.currentPassword,
          new_password: this.passwordForm.newPassword,
          new_password_confirmation: this.passwordForm.confirmPassword,
        });

        if (response.data?.success) {
          this.toast.success("Password changed successfully!");

          // Clear form
          this.passwordForm = {
            currentPassword: "",
            newPassword: "",
            confirmPassword: "",
          };

          // Refresh stats after password change
          setTimeout(() => {
            this.$router.push({ name: "Login" });
          }, 1000);
        }
      } catch (error) {
        console.error("Error changing password:", error);

        if (error.response?.data?.errors) {
          this.errors = error.response.data.errors;
        } else {
          this.toast.error("Failed to change password. Please try again.");
        }
      } finally {
        this.changingPassword = false;
      }
    },

    async deleteAccount() {
      try {
        this.deletingAccount = true;

        const response = await blogAPI.deleteAccount();

        if (response.data?.success) {
          this.user = null;
          localStorage.removeItem("user");
          localStorage.removeItem("token");

          this.toast.info("Account deleted successfully!");

          // Redirect to home page after a short delay
          setTimeout(() => {
            this.$router.push({ name: "Register" });
          }, 1000);
        }
      } catch (error) {
        console.error("Error deleting account:", error);
        this.toast.error("Failed to delete account. Please try again.");
      } finally {
        this.deletingAccount = false;
        this.showDeleteAccountModal = false;
      }
    },

    validateProfileForm() {
      const errors = {};

      if (!this.profileForm.name.trim()) {
        errors.name = "Name is required";
      }

      if (!this.profileForm.email) {
        errors.email = "Email is required";
      } else if (!this.isValidEmail(this.profileForm.email)) {
        errors.email = "Please enter a valid email address";
      }

      this.errors = errors;
      return Object.keys(errors).length === 0;
    },

    validatePasswordForm() {
      const errors = {};

      if (!this.passwordForm.currentPassword) {
        errors.currentPassword = "Current password is required";
      }

      if (!this.passwordForm.newPassword) {
        errors.newPassword = "New password is required";
      } else if (this.passwordForm.newPassword.length < 8) {
        errors.newPassword = "Password must be at least 8 characters";
      }

      if (!this.passwordForm.confirmPassword) {
        errors.confirmPassword = "Please confirm your new password";
      } else if (
        this.passwordForm.newPassword !== this.passwordForm.confirmPassword
      ) {
        errors.confirmPassword = "Passwords do not match";
      }

      this.errors = errors;
      return Object.keys(errors).length === 0;
    },

    isValidEmail(email) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailRegex.test(email);
    },

    formatDate(dateString) {
      if (!dateString) return "Unknown";
      return new Date(dateString).toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
      });
    },
  },

  computed: {
    auth() {
      return useAuth();
    },
    isAuthenticated() {
      return this.auth?.isLoggedIn;
    },
    currentUser() {
      return this.auth?.user?.id || null;
    },
  },

  watch: {
    showDeleteAccountModal(newVal) {
      if (newVal) {
        // Show modal using Bootstrap
        const modal = new Modal(this.$refs.deleteAccountModal);
        modal.show();
      } else {
        // Hide modal using Bootstrap
        const modal = Modal.getInstance(this.$refs.deleteAccountModal);
        if (modal) {
          modal.hide();
        }
      }
    },
  },
};
</script>

<style scoped>
.profile-avatar {
  width: 100px;
  height: 100px;
  border: 3px solid #fff;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.stat-item {
  padding: 0.5rem;
  border-radius: 8px;
  transition: all 0.2s ease;
}

.stat-item:hover {
  background-color: rgba(0, 0, 0, 0.05);
  transform: translateY(-2px);
}

.card {
  border: 1px solid var(--border-color);
  border-radius: var(--border-radius-lg);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.nav-tabs .nav-link {
  color: var(--text-secondary);
  border: none;
  border-bottom: 2px solid transparent;
  transition: all 0.2s ease;
}

.nav-tabs .nav-link:hover {
  color: var(--primary-color);
  border-bottom-color: var(--primary-color);
}

.nav-tabs .nav-link.active {
  color: var(--primary-color);
  border-bottom-color: var(--primary-color);
  background: none;
  font-weight: 600;
}

.form-control:focus {
  border-color: var(--primary-color);
  box-shadow: 0 0 0 0.2rem rgba(0, 0, 0, 0.1);
}

.btn-primary {
  background-color: var(--primary-color);
  border-color: var(--primary-color);
  transition: all 0.2s ease;
}

.btn-primary:hover {
  background-color: var(--secondary-color);
  border-color: var(--secondary-color);
  transform: translateY(-1px);
}

.btn-outline-danger {
  transition: all 0.2s ease;
}

.btn-outline-danger:hover {
  transform: translateY(-1px);
}

.form-text {
  color: var(--text-muted);
  font-size: 0.875rem;
}

.modal-header.border-danger {
  border-bottom-color: #dc3545 !important;
}

.alert-warning {
  background-color: #fff3cd;
  border-color: #ffeaa7;
  color: #856404;
}

/* Toast Notifications */
.toast-notification {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 9999;
  padding: 12px 20px;
  border-radius: 8px;
  color: white;
  font-weight: 500;
  transform: translateX(100%);
  transition: transform 0.3s ease;
  max-width: 300px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.toast-notification.show {
  transform: translateX(0);
}

.toast-success {
  background-color: #28a745;
}

.toast-error {
  background-color: #dc3545;
}

.toast-content {
  display: flex;
  align-items: center;
}

/* Progress Bar Styling */
.progress {
  background-color: #e9ecef;
  border-radius: 10px;
  overflow: hidden;
}

.progress-bar {
  background-color: #007bff;
  transition: width 0.3s ease;
}

/* Enhanced Profile Avatar */
.profile-avatar {
  width: 120px;
  height: 120px;
  border: 4px solid #fff;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  transition: all 0.3s ease;
}

.profile-avatar:hover {
  transform: scale(1.05);
  box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
}

/* Enhanced Stats */
.stat-item {
  padding: 0.75rem;
  border-radius: 12px;
  transition: all 0.3s ease;
  border: 1px solid transparent;
}

.stat-item:hover {
  background-color: rgba(0, 0, 0, 0.05);
  transform: translateY(-3px);
  border-color: rgba(0, 0, 0, 0.1);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Enhanced Buttons */
.btn {
  border-radius: 8px;
  font-weight: 500;
  transition: all 0.3s ease;
}

.btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Enhanced Cards */
.card {
  border: 1px solid var(--border-color, #e9ecef);
  border-radius: 16px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
}

.card:hover {
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
  transform: translateY(-2px);
}
</style>
