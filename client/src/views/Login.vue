<template>
  <div class="container my-auto">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm">
          <div class="card-body p-4">
            <div class="text-center mb-4">
              <h2 class="mb-2">Welcome Back</h2>
              <p class="text-muted">Sign in to your account</p>
            </div>
            
            <!-- Login Form -->
            <form @submit.prevent="handleLogin">
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
                  placeholder="Enter your password"
                  required
                />
                <div v-if="errors.password" class="invalid-feedback">
                  {{ errors.password }}
                </div>
              </div>
              
              <!-- Remember Me -->
              <div class="mb-3 form-check">
                <input
                  id="remember"
                  v-model="form.remember"
                  type="checkbox"
                  class="form-check-input"
                />
                <label class="form-check-label" for="remember">
                  Remember me
                </label>
              </div>
              
              <!-- Submit Button -->
              <button
                type="submit"
                class="btn btn-primary w-100 mb-3"
                :disabled="loading"
              >
                <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status"></span>
                {{ loading ? 'Signing in...' : 'Sign In' }}
              </button>
              
              <!-- Error Message -->
              <div v-if="error" class="alert alert-danger" role="alert">
                {{ error }}
              </div>
            </form>
            
            <!-- Links -->
            <div class="text-center">
              <p class="mb-2">
                Don't have an account?
                <router-link to="/register" class="text-decoration-none">
                  Sign up here
                </router-link>
              </p>
              <p class="mb-0">
                <a href="#" class="text-decoration-none text-muted">
                  Forgot your password?
                </a>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useAuth } from '../stores/auth.js'

export default {
  name: 'Login',
  data() {
    return {
      form: {
        email: '',
        password: '',
        remember: false
      },
      errors: {},
      loading: false,
      error: ''
    }
  },
  methods: {
    async handleLogin() {
      // Reset errors
      this.errors = {}
      this.error = ''
      
      // Validate form
      if (!this.validateForm()) {
        return
      }
      
      try {
        this.loading = true
        
        const auth = useAuth()
        const result = await auth.login({
          email: this.form.email,
          password: this.form.password
        })
        
        if (result.success) {
          // Redirect to dashboard or intended page
          const redirectTo = this.$route.query.redirect || '/'
          this.$router.push(redirectTo)
        } else {
          this.error = result.message
          if (result.errors) {
            this.errors = result.errors
          }
        }
        
      } catch (error) {
        console.error('Login error:', error)
        this.error = 'An unexpected error occurred. Please try again.'
      } finally {
        this.loading = false
      }
    },
    validateForm() {
      const errors = {}
      
      // Email validation
      if (!this.form.email) {
        errors.email = 'Email is required'
      } else if (!this.isValidEmail(this.form.email)) {
        errors.email = 'Please enter a valid email address'
      }
      
      // Password validation
      if (!this.form.password) {
        errors.password = 'Password is required'
      } else if (this.form.password.length < 6) {
        errors.password = 'Password must be at least 6 characters'
      }
      
      this.errors = errors
      return Object.keys(errors).length === 0
    },
    isValidEmail(email) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      return emailRegex.test(email)
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
</style>

