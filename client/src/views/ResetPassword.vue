<!-- ResetPassword.vue -->
<template>
  <div class="forgot-bg">
    <div class="forgot-container">
      <h1 class="forgot-title">Set a new password</h1>
      <p class="forgot-subtitle">
        Enter your new password below. Your email is shown for confirmation.
      </p>
      <div class="forgot-card">
        <h2>Reset your password</h2>
        <form @submit.prevent="submit">
          <label for="email" class="forgot-label">Email address</label>
          <div class="forgot-input-group">
            <span class="forgot-icon">@</span>
            <input
              :value="email"
              id="email"
              type="email"
              required
              class="forgot-input"
              readonly
            />
          </div>
          <label for="otp" class="forgot-label">OTP Code</label>
          <div class="forgot-input-group">
            <input
              v-model="otp"
              id="otp"
              type="text"
              placeholder="Enter OTP"
              required
              class="forgot-input"
              maxlength="6"
            />
          </div>
          <label for="password" class="forgot-label">New Password</label>
          <div class="forgot-input-group">
            <input
              v-model="password"
              id="password"
              type="password"
              placeholder="New password"
              required
              class="forgot-input"
            />
          </div>
          <label for="password_confirmation" class="forgot-label"
            >Confirm Password</label
          >
          <div class="forgot-input-group">
            <input
              v-model="password_confirmation"
              id="password_confirmation"
              type="password"
              placeholder="Confirm password"
              required
              class="forgot-input"
            />
          </div>
          <button type="submit" class="forgot-btn" :disabled="loading">
            <span v-if="loading" class="spinner"></span>
            {{ loading ? "Resetting..." : "Reset Password" }}
          </button>
          <div v-if="message" class="forgot-message">{{ message }}</div>
          <div v-if="error" class="forgot-error">{{ error }}</div>
        </form>
        <p class="forgot-back">
          Remembered your password?
          <router-link to="/login">Back to sign in</router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    email: {
      type: String,
      required: true,
    },
    // token prop removed, not needed for OTP flow
  },
  data() {
    return {
      otp: "",
      password: "",
      password_confirmation: "",
      message: "",
      error: "",
      loading: false,
    };
  },
  methods: {
    async submit() {
      this.message = "";
      this.error = "";
      this.loading = true;
      try {
        const res = await fetch("/api/reset-password", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            email: this.email,
            otp: this.otp,
            password: this.password,
            password_confirmation: this.password_confirmation,
          }),
        });
        const data = await res.json();
        if (res.ok) {
          this.message = data.message;
          setTimeout(() => {
            this.$router.push({ name: "Login" });
          }, 1800);
        } else {
          this.error = data.message || "Error";
        }
      } catch (e) {
        this.error = "Network error";
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style scoped>
.forgot-bg {
  min-height: 100vh;
  background: url("/public/placeholder-hero.jpg") center/cover no-repeat;
  display: flex;
  align-items: center;
  justify-content: center;
}
.forgot-container {
  width: 100%;
  max-width: 500px;
  margin: auto;
  text-align: center;
}
.forgot-title {
  font-size: 2.2rem;
  font-weight: bold;
  margin-bottom: 0.5rem;
}
.forgot-subtitle {
  color: #666;
  margin-bottom: 2rem;
}
.forgot-card {
  background: #fff;
  padding: 2rem 2.5rem;
  border-radius: 18px;
  box-shadow: 0 2px 24px rgba(0, 0, 0, 0.08);
  text-align: left;
}
.forgot-card h2 {
  font-size: 1.2rem;
  margin-bottom: 0.5rem;
}
.forgot-card p {
  color: #555;
  margin-bottom: 1.2rem;
}
.forgot-label {
  font-weight: 500;
  margin-bottom: 0.3rem;
  display: block;
}
.forgot-input-group {
  display: flex;
  align-items: center;
  background: #f5f5f5;
  border-radius: 8px;
  margin-bottom: 1.2rem;
  padding: 0.5rem 0.8rem;
}
.forgot-icon {
  color: #888;
  margin-right: 0.5rem;
  font-size: 1.1rem;
}
.forgot-input {
  border: none;
  background: transparent;
  outline: none;
  width: 100%;
  font-size: 1rem;
  padding: 0.5rem 0;
}
.forgot-btn {
  width: 100%;
  background: #222;
  color: #fff;
  font-weight: 600;
  border: none;
  border-radius: 8px;
  padding: 0.8rem 0;
  margin-bottom: 1rem;
  cursor: pointer;
  font-size: 1rem;
  transition: background 0.2s;
  position: relative;
}
.forgot-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}
.forgot-btn:hover {
  background: #444;
}
.spinner {
  display: inline-block;
  width: 18px;
  height: 18px;
  border: 2px solid #fff;
  border-top: 2px solid #222;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
  margin-right: 8px;
  vertical-align: middle;
}
@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
.forgot-message {
  color: #2ecc40;
  margin-bottom: 0.5rem;
}
.forgot-error {
  color: #e74c3c;
  margin-bottom: 0.5rem;
}
.forgot-back {
  text-align: center;
  margin-top: 1rem;
  color: #888;
}
.forgot-back a {
  color: #222;
  text-decoration: underline;
  margin-left: 0.2rem;
}
</style>
