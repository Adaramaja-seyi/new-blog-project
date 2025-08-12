<template>
  <div class="container">
    <div class="row">
      <!-- Profile Sidebar -->
      <div class="col-lg-4 mb-4">
        <div class="card">
          <div class="card-body text-center">
            <!-- Profile Avatar -->
            <div class="mb-3">
              <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px;">
                <i class="bi bi-person text-white" style="font-size: 3rem;"></i>
              </div>
            </div>
            
            <!-- Profile Info -->
            <h5 class="mb-1">{{ user?.name || 'User Name' }}</h5>
            <p class="text-muted mb-3">{{ user?.email || 'user@example.com' }}</p>
            
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
            <h6 class="mb-0">Quick Stats</h6>
          </div>
          <div class="card-body">
            <div class="row text-center">
              <div class="col-4">
                <h6 class="mb-1">{{ stats.totalPosts }}</h6>
                <small class="text-muted">Posts</small>
              </div>
              <div class="col-4">
                <h6 class="mb-1">{{ stats.totalViews }}</h6>
                <small class="text-muted">Views</small>
              </div>
              <div class="col-4">
                <h6 class="mb-1">{{ stats.totalComments }}</h6>
                <small class="text-muted">Comments</small>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Profile Content -->
      <div class="col-lg-8">
        <!-- Profile Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h1 class="mb-2">Profile Settings</h1>
            <p class="text-muted mb-0">Manage your account information and preferences</p>
          </div>
        </div>
        
        <!-- Profile Tabs -->
        <div class="card">
          <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="profileTabs" role="tablist">
              <li class="nav-item" role="presentation">
                <button 
                  class="nav-link active" 
                  id="profile-tab" 
                  data-bs-toggle="tab" 
                  data-bs-target="#profile" 
                  type="button" 
                  role="tab"
                >
                  Profile
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button 
                  class="nav-link" 
                  id="security-tab" 
                  data-bs-toggle="tab" 
                  data-bs-target="#security" 
                  type="button" 
                  role="tab"
                >
                  Security
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button 
                  class="nav-link" 
                  id="notifications-tab" 
                  data-bs-toggle="tab" 
                  data-bs-target="#notifications" 
                  type="button" 
                  role="tab"
                >
                  Notifications
                </button>
              </li>
            </ul>
          </div>
          
          <div class="card-body">
            <div class="tab-content" id="profileTabsContent">
              <!-- Profile Tab -->
              <div class="tab-pane fade show active" id="profile" role="tabpanel">
                <form @submit.prevent="updateProfile">
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label for="firstName" class="form-label">First Name</label>
                      <input
                        id="firstName"
                        v-model="profileForm.firstName"
                        type="text"
                        class="form-control"
                        :class="{ 'is-invalid': errors.firstName }"
                        required
                      />
                      <div v-if="errors.firstName" class="invalid-feedback">
                        {{ errors.firstName }}
                      </div>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="lastName" class="form-label">Last Name</label>
                      <input
                        id="lastName"
                        v-model="profileForm.lastName"
                        type="text"
                        class="form-control"
                        :class="{ 'is-invalid': errors.lastName }"
                        required
                      />
                      <div v-if="errors.lastName" class="invalid-feedback">
                        {{ errors.lastName }}
                      </div>
                    </div>
                  </div>
                  
                  <div class="mb-3">
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
                  
                  <div class="mb-3">
                    <label for="bio" class="form-label">Bio</label>
                    <textarea
                      id="bio"
                      v-model="profileForm.bio"
                      class="form-control"
                      rows="4"
                      placeholder="Tell us about yourself..."
                    ></textarea>
                  </div>
                  
                  <div class="mb-3">
                    <label for="website" class="form-label">Website</label>
                    <input
                      id="website"
                      v-model="profileForm.website"
                      type="url"
                      class="form-control"
                      placeholder="https://example.com"
                    />
                  </div>
                  
                  <button type="submit" class="btn btn-primary" :disabled="updating">
                    <span v-if="updating" class="spinner-border spinner-border-sm me-2" role="status"></span>
                    {{ updating ? 'Updating...' : 'Update Profile' }}
                  </button>
                </form>
              </div>
              
              <!-- Security Tab -->
              <div class="tab-pane fade" id="security" role="tabpanel">
                <form @submit.prevent="changePassword">
                  <div class="mb-3">
                    <label for="currentPassword" class="form-label">Current Password</label>
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
                    <label for="newPassword" class="form-label">New Password</label>
                    <input
                      id="newPassword"
                      v-model="passwordForm.newPassword"
                      type="password"
                      class="form-control"
                      :class="{ 'is-invalid': errors.newPassword }"
                      required
                    />
                    <div v-if="errors.newPassword" class="invalid-feedback">
                      {{ errors.newPassword }}
                    </div>
                    <div class="form-text">
                      Password must be at least 8 characters long
                    </div>
                  </div>
                  
                  <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Confirm New Password</label>
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
                  
                  <button type="submit" class="btn btn-primary" :disabled="changingPassword">
                    <span v-if="changingPassword" class="spinner-border spinner-border-sm me-2" role="status"></span>
                    {{ changingPassword ? 'Changing...' : 'Change Password' }}
                  </button>
                </form>
              </div>
              
              <!-- Notifications Tab -->
              <div class="tab-pane fade" id="notifications" role="tabpanel">
                <form @submit.prevent="updateNotifications">
                  <div class="mb-3">
                    <div class="form-check">
                      <input
                        id="emailNotifications"
                        v-model="notificationForm.emailNotifications"
                        type="checkbox"
                        class="form-check-input"
                      />
                      <label class="form-check-label" for="emailNotifications">
                        Email notifications for new comments
                      </label>
                    </div>
                  </div>
                  
                  <div class="mb-3">
                    <div class="form-check">
                      <input
                        id="weeklyDigest"
                        v-model="notificationForm.weeklyDigest"
                        type="checkbox"
                        class="form-check-input"
                      />
                      <label class="form-check-label" for="weeklyDigest">
                        Weekly digest of your blog activity
                      </label>
                    </div>
                  </div>
                  
                  <div class="mb-3">
                    <div class="form-check">
                      <input
                        id="marketingEmails"
                        v-model="notificationForm.marketingEmails"
                        type="checkbox"
                        class="form-check-input"
                      />
                      <label class="form-check-label" for="marketingEmails">
                        Marketing and promotional emails
                      </label>
                    </div>
                  </div>
                  
                  <button type="submit" class="btn btn-primary" :disabled="updatingNotifications">
                    <span v-if="updatingNotifications" class="spinner-border spinner-border-sm me-2" role="status"></span>
                    {{ updatingNotifications ? 'Updating...' : 'Update Notifications' }}
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Danger Zone -->
        <div class="card mt-4 border-danger">
          <div class="card-header bg-danger text-white">
            <h6 class="mb-0">Danger Zone</h6>
          </div>
          <div class="card-body">
            <p class="text-muted mb-3">
              Once you delete your account, there is no going back. Please be certain.
            </p>
            <button 
              @click="deleteAccount"
              class="btn btn-outline-danger"
              :disabled="deletingAccount"
            >
              <span v-if="deletingAccount" class="spinner-border spinner-border-sm me-2" role="status"></span>
              {{ deletingAccount ? 'Deleting...' : 'Delete Account' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { blogAPI } from '../api.js'

export default {
  name: 'Profile',
  data() {
    return {
      user: null,
      stats: {
        totalPosts: 0,
        totalViews: 0,
        totalComments: 0
      },
      profileForm: {
        firstName: '',
        lastName: '',
        email: '',
        bio: '',
        website: ''
      },
      passwordForm: {
        currentPassword: '',
        newPassword: '',
        confirmPassword: ''
      },
      notificationForm: {
        emailNotifications: true,
        weeklyDigest: false,
        marketingEmails: false
      },
      errors: {},
      updating: false,
      changingPassword: false,
      updatingNotifications: false,
      deletingAccount: false
    }
  },
  mounted() {
    this.fetchUserData()
  },
  methods: {
    async fetchUserData() {
      try {
        // TODO: Implement API call to fetch user data
        // const response = await blogAPI.getProfile()
        // this.user = response.data.user
        // this.stats = response.data.stats
        
        // Mock data for now
        this.user = {
          id: 1,
          name: 'John Doe',
          email: 'john@example.com',
          bio: 'Full-stack developer with 5+ years of experience.',
          website: 'https://johndoe.dev',
          created_at: '2024-01-01T00:00:00Z'
        }
        
        this.stats = {
          totalPosts: 2,
          totalViews: 1250,
          totalComments: 8
        }
        
        // Populate form with user data
        this.profileForm = {
          firstName: 'John',
          lastName: 'Doe',
          email: this.user.email,
          bio: this.user.bio || '',
          website: this.user.website || ''
        }
        
      } catch (error) {
        console.error('Error fetching user data:', error)
      }
    },
    async updateProfile() {
      // Reset errors
      this.errors = {}
      
      // Validate form
      if (!this.validateProfileForm()) {
        return
      }
      
      try {
        this.updating = true
        
        // TODO: Implement API call to update profile
        // const response = await blogAPI.updateProfile(this.profileForm)
        // this.user = response.data
        
        // Mock successful update
        console.log('Profile updated:', this.profileForm)
        
      } catch (error) {
        console.error('Error updating profile:', error)
        
        if (error.response?.data?.errors) {
          this.errors = error.response.data.errors
        }
      } finally {
        this.updating = false
      }
    },
    async changePassword() {
      // Reset errors
      this.errors = {}
      
      // Validate form
      if (!this.validatePasswordForm()) {
        return
      }
      
      try {
        this.changingPassword = true
        
        // TODO: Implement API call to change password
        // await blogAPI.changePassword(this.passwordForm)
        
        // Mock successful password change
        console.log('Password changed successfully')
        
        // Clear form
        this.passwordForm = {
          currentPassword: '',
          newPassword: '',
          confirmPassword: ''
        }
        
      } catch (error) {
        console.error('Error changing password:', error)
        
        if (error.response?.data?.errors) {
          this.errors = error.response.data.errors
        }
      } finally {
        this.changingPassword = false
      }
    },
    async updateNotifications() {
      try {
        this.updatingNotifications = true
        
        // TODO: Implement API call to update notifications
        // await blogAPI.updateNotifications(this.notificationForm)
        
        // Mock successful update
        console.log('Notifications updated:', this.notificationForm)
        
      } catch (error) {
        console.error('Error updating notifications:', error)
      } finally {
        this.updatingNotifications = false
      }
    },
    async deleteAccount() {
      if (!confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
        return
      }
      
      try {
        this.deletingAccount = true
        
        // TODO: Implement API call to delete account
        // await blogAPI.deleteAccount()
        
        // Mock successful deletion
        console.log('Account deleted successfully')
        
        // Redirect to home page
        this.$router.push('/')
        
      } catch (error) {
        console.error('Error deleting account:', error)
      } finally {
        this.deletingAccount = false
      }
    },
    validateProfileForm() {
      const errors = {}
      
      if (!this.profileForm.firstName.trim()) {
        errors.firstName = 'First name is required'
      }
      
      if (!this.profileForm.lastName.trim()) {
        errors.lastName = 'Last name is required'
      }
      
      if (!this.profileForm.email) {
        errors.email = 'Email is required'
      } else if (!this.isValidEmail(this.profileForm.email)) {
        errors.email = 'Please enter a valid email address'
      }
      
      this.errors = errors
      return Object.keys(errors).length === 0
    },
    validatePasswordForm() {
      const errors = {}
      
      if (!this.passwordForm.currentPassword) {
        errors.currentPassword = 'Current password is required'
      }
      
      if (!this.passwordForm.newPassword) {
        errors.newPassword = 'New password is required'
      } else if (this.passwordForm.newPassword.length < 8) {
        errors.newPassword = 'Password must be at least 8 characters'
      }
      
      if (!this.passwordForm.confirmPassword) {
        errors.confirmPassword = 'Please confirm your new password'
      } else if (this.passwordForm.newPassword !== this.passwordForm.confirmPassword) {
        errors.confirmPassword = 'Passwords do not match'
      }
      
      this.errors = errors
      return Object.keys(errors).length === 0
    },
    isValidEmail(email) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      return emailRegex.test(email)
    },
    formatDate(dateString) {
      if (!dateString) return 'Unknown'
      return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }
  }
}
</script>

<style scoped>
.card {
  border: 1px solid var(--border-color);
  border-radius: var(--border-radius-lg);
}

.nav-tabs .nav-link {
  color: var(--text-secondary);
  border: none;
  border-bottom: 2px solid transparent;
}

.nav-tabs .nav-link.active {
  color: var(--primary-color);
  border-bottom-color: var(--primary-color);
  background: none;
}

.form-control:focus {
  border-color: var(--primary-color);
  box-shadow: 0 0 0 0.2rem rgba(0, 0, 0, 0.1);
}

.btn-primary {
  background-color: var(--primary-color);
  border-color: var(--primary-color);
}

.btn-primary:hover {
  background-color: var(--secondary-color);
  border-color: var(--secondary-color);
}

.form-text {
  color: var(--text-muted);
  font-size: 0.875rem;
}
</style>

