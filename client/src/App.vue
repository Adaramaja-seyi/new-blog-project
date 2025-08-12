<script>
import GlobalNavbar from './components/GlobalNavbar.vue'
import Footer from './components/Footer.vue'

export default {
  name: 'App',
  components: {
    GlobalNavbar,
    Footer
  }
}
</script>

<template>
  <div id="app">
    <!-- Global Navigation Bar -->
    <GlobalNavbar />
    
    <!-- Main Content Area -->
    <main class="main-content">
      <router-view />
    </main>
    
    <!-- Global Footer -->
    <Footer />
  </div>
</template>

<style scoped>
.main-content {
  min-height: calc(100vh - 200px); /* Adjust based on navbar + footer height */
  padding: 2rem 0;
}
</style>
