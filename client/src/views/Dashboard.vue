<template>
  <DashboardLayout page-title="Dashboard Overview">
    <DashboardHome />
  </DashboardLayout>
</template>

<script>
import DashboardLayout from '../components/DashboardLayout.vue'
import DashboardHome from './DashboardHome.vue'

export default {
  name: 'Dashboard',
  components: {
    DashboardLayout,
    DashboardHome
  },
  mounted() {
    // Check if user is authenticated and has author role
    this.checkAuth()
  },
  methods: {
    checkAuth() {
      const user = localStorage.getItem('user')
      if (!user) {
        this.$router.push('/login')
        return
      }

      const userData = JSON.parse(user)
      if (userData.role !== 'author') {
        this.$router.push('/')
        return
      }
    }
  }
}
</script>

<style scoped>
/* Dashboard specific styles can be added here if needed */
</style>

