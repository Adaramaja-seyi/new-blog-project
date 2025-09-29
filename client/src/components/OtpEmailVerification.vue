<template>
  <div class="otp-verification-wrapper">
    <div class="otp-verification-card">
      <h4>Email Verification</h4>
      <p>
        An OTP has been sent to <b>{{ email }}</b
        >. Enter it below to verify your email.
      </p>
      <input
        v-model="otp"
        maxlength="6"
        class="form-control mb-2"
        placeholder="Enter OTP"
      />
      <button
        class="btn btn-success w-100 mb-2"
        :disabled="verifying"
        @click="verifyOtp"
      >
        <span
          v-if="verifying"
          class="spinner-border spinner-border-sm me-2"
        ></span>
        Verify Email
      </button>
      <div v-if="otpError" class="alert alert-danger">{{ otpError }}</div>
    </div>
  </div>
</template>

<script>
import { useToast } from "vue-toastification";
export default {
  name: "OtpEmailVerification",
  props: {
    email: { type: String, required: true },
  },
  data() {
    return {
      otp: "",
      verifying: false,
      otpError: "",
    };
  },
  setup() {
    const toast = useToast();
    return { toast };
  },
  methods: {
    async verifyOtp() {
      this.otpError = "";
      if (!this.otp || this.otp.length !== 6) {
        this.otpError = "Please enter the 6-digit OTP.";
        return;
      }
      this.verifying = true;
      try {
        const res = await fetch("/api/email/verify-otp", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ email: this.email, otp: this.otp }),
        });
        const data = await res.json();
        if (res.ok) {
          this.toast.success("Email verified! Redirecting to login...");
          setTimeout(() => {
            this.$router.push({ name: "Login" });
          }, 1200);
        } else {
          this.otpError = data.message || "Invalid OTP.";
        }
      } catch (e) {
        this.otpError = "Network error. Please try again.";
      } finally {
        this.verifying = false;
      }
    },
  },
};
</script>

<style scoped>
.otp-verification-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  background: rgba(0, 0, 0, 0.03);
}
.otp-verification-card {
  background: #fff;
  padding: 2rem 2rem 1.5rem 2rem;
  border-radius: 12px;
  box-shadow: 0 2px 24px rgba(0, 0, 0, 0.12);
  max-width: 350px;
  width: 100%;
  text-align: center;
}
</style>
