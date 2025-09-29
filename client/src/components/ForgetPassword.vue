<!-- ForgotPassword.vue -->
<template>
  <form @submit.prevent="submit">
    <input v-model="email" type="email" placeholder="Email" required />
    <button type="submit">Send Reset Link</button>
    <div v-if="message">{{ message }}</div>
    <div v-if="error">{{ error }}</div>
  </form>
</template>

<script>
export default {
  data() {
    return { email: '', message: '', error: '' }
  },
  methods: {
    async submit() {
      this.message = ''; this.error = '';
      try {
        const res = await fetch('/api/forgot-password', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email: this.email })
        });
        const data = await res.json();
        if (res.ok) this.message = data.message;
        else this.error = data.message || 'Error';
      } catch (e) { this.error = 'Network error'; }
    }
  }
}
</script>
