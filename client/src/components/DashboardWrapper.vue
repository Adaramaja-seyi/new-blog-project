<template>
  <DashboardLayout :page-title="pageTitle">
    <router-view />
  </DashboardLayout>
</template>

<script>
import DashboardLayout from './DashboardLayout.vue'

export default {
  name: 'DashboardWrapper',
  components: {
    DashboardLayout
  },
  computed: {
    pageTitle() {
      // Get the page title from the current route meta or generate from route name
      const route = this.$route
      if (route.meta && route.meta.title) {
        return route.meta.title
      }
      
      // Generate title from route name
      const routeName = route.name
      if (routeName) {
        return routeName.replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase())
      }
      
      return 'Dashboard'
    }
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

      try {
        const userData = JSON.parse(user)
        if (userData.role !== 'author') {
          this.$router.push('/')
          return
        }
      } catch (error) {
        this.$router.push('/login')
        return
      }
    }
  }
}
</script>

<style scoped>
/* No additional styles needed - this is just a wrapper */
</style>
