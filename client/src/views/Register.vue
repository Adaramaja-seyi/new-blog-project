<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm">
          <div class="card-body p-4">
            <div class="text-center mb-4">
              <h2 class="mb-2">Create Account</h2>
              <p class="text-muted">Join our community of writers and readers</p>
            </div>
            
            <!-- Registration Form -->
            <form @submit.prevent="handleRegister">
              <!-- Name Fields -->
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="firstName" class="form-label">First Name</label>
                  <input
                    id="firstName"
                    v-model="form.firstName"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': errors.firstName }"
                    placeholder="First name"
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
                    v-model="form.lastName"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': errors.lastName }"
                    placeholder="Last name"
                    required
                  />
                  <div v-if="errors.lastName" class="invalid-feedback">
                    {{ errors.lastName }}
                  </div>
                </div>
              </div>
              
              <!-- Email Field -->
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  class="form-control"
                  :class="{ 'is-invalid': errors.email }"
                  placeholder="Enter your email"
                  required
                />
                <div v-if="errors.email" class="invalid-feedback">
                  {{ errors.email }}
                </div>
              </div>
              
              <!-- Password Field -->
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input
                  id="password"
                  v-model="form.password"
                  type="password"
                  class="form-control"
                  :class="{ 'is-invalid': errors.password }"
                  placeholder="Create a password"
                  required
                />
                <div v-if="errors.password" class="invalid-feedback">
                  {{ errors.password }}
                </div>
                <div class="form-text">
                  Password must be at least 8 characters long
                </div>
              </div>
              
              <!-- Confirm Password Field -->
              <div class="mb-3">
                <label for="confirmPassword" class="form-label">Confirm Password</label>
                <input
                  id="confirmPassword"
                  v-model="form.confirmPassword"
                  type="password"
                  class="form-control"
                  :class="{ 'is-invalid': errors.confirmPassword }"
                  placeholder="Confirm your password"
                  required
                />
                <div v-if="errors.confirmPassword" class="invalid-feedback">
                  {{ errors.confirmPassword }}
                </div>
              </div>
              
              <!-- Terms and Conditions -->
              <div class="mb-3 form-check">
                <input
                  id="terms"
                  v-model="form.agreeToTerms"
                  type="checkbox"
                  class="form-check-input"
                  :class="{ 'is-invalid': errors.agreeToTerms }"
                  required
                />
                <label class="form-check-label" for="terms">
                  I agree to the 
                  <a href="#" class="text-decoration-none">Terms of Service</a> 
                  and 
                  <a href="#" class="text-decoration-none">Privacy Policy</a>
                </label>
                <div v-if="errors.agreeToTerms" class="invalid-feedback">
                  {{ errors.agreeToTerms }}
                </div>
              </div>
              
              <!-- Submit Button -->
              <button
                type="submit"
                class="btn btn-primary w-100 mb-3"
                :disabled="loading"
              >
                <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status"></span>
                {{ loading ? 'Creating Account...' : 'Create Account' }}
              </button>
              
              <!-- Error Message -->
              <div v-if="error" class="alert alert-danger" role="alert">
                {{ error }}
              </div>
            </form>
            
            <!-- Links -->
            <div class="text-center">
              <p class="mb-0">
                Already have an account?
                <router-link to="/login" class="text-decoration-none">
                  Sign in here
                </router-link>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { blogAPI } from '../api.js'

export default {
  name: 'Register',
  data() {
    return {
      form: {
        firstName: '',
        lastName: '',
        email: '',
        password: '',
        confirmPassword: '',
        agreeToTerms: false
      },
      errors: {},
      loading: false,
      error: ''
    }
  },
  methods: {
    async handleRegister() {
      // Reset errors
      this.errors = {}
      this.error = ''
      
      // Validate form
      if (!this.validateForm()) {
        return
      }
      
      try {
        this.loading = true
        
        // TODO: Implement API call to register
        // const response = await blogAPI.register({
        //   first_name: this.form.firstName,
        //   last_name: this.form.lastName,
        //   email: this.form.email,
        //   password: this.form.password,
        //   password_confirmation: this.form.confirmPassword
        // })
        
        // TODO: Store authentication token/user data
        // localStorage.setItem('auth_token', response.data.token)
        // localStorage.setItem('user', JSON.stringify(response.data.user))
        
        // Mock successful registration
        console.log('Registration successful:', this.form)
        
        // Redirect to dashboard
        this.$router.push('/dashboard')
        
      } catch (error) {
        console.error('Registration error:', error)
        
        // Handle different types of errors
        if (error.response) {
          const { status, data } = error.response
          
          if (status === 422) {
            // Validation errors
            this.errors = data.errors || {}
          } else if (status === 409) {
            // Email already exists
            this.error = 'An account with this email already exists'
          } else {
            // Other server errors
            this.error = data.message || 'An error occurred during registration'
          }
        } else {
          // Network or other errors
          this.error = 'Unable to connect to server. Please try again.'
        }
      } finally {
        this.loading = false
      }
    },
    validateForm() {
      const errors = {}
      
      // First name validation
      if (!this.form.firstName.trim()) {
        errors.firstName = 'First name is required'
      } else if (this.form.firstName.length < 2) {
        errors.firstName = 'First name must be at least 2 characters'
      }
      
      // Last name validation
      if (!this.form.lastName.trim()) {
        errors.lastName = 'Last name is required'
      } else if (this.form.lastName.length < 2) {
        errors.lastName = 'Last name must be at least 2 characters'
      }
      
      // Email validation
      if (!this.form.email) {
        errors.email = 'Email is required'
      } else if (!this.isValidEmail(this.form.email)) {
        errors.email = 'Please enter a valid email address'
      }
      
      // Password validation
      if (!this.form.password) {
        errors.password = 'Password is required'
      } else if (this.form.password.length < 8) {
        errors.password = 'Password must be at least 8 characters'
      } else if (!this.isValidPassword(this.form.password)) {
        errors.password = 'Password must contain at least one letter and one number'
      }
      
      // Confirm password validation
      if (!this.form.confirmPassword) {
        errors.confirmPassword = 'Please confirm your password'
      } else if (this.form.password !== this.form.confirmPassword) {
        errors.confirmPassword = 'Passwords do not match'
      }
      
      // Terms validation
      if (!this.form.agreeToTerms) {
        errors.agreeToTerms = 'You must agree to the terms and conditions'
      }
      
      this.errors = errors
      return Object.keys(errors).length === 0
    },
    isValidEmail(email) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      return emailRegex.test(email)
    },
    isValidPassword(password) {
      // At least one letter and one number
      const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/
      return passwordRegex.test(password)
    }
  }
}
</script>

<style scoped>
.card {
  border: 1px solid var(--border-color);
  border-radius: var(--border-radius-lg);
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

