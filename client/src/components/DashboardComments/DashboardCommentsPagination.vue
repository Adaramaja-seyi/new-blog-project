<template>
  <div v-if="total > perPage" class="d-flex justify-content-center mt-4">
    <nav>
      <ul class="pagination">
        <li class="page-item" :class="{ disabled: current === 1 }">
          <button @click="$emit('change-page', current - 1)" class="page-link">Previous</button>
        </li>
        <li v-for="page in pages" :key="page" class="page-item" :class="{ active: page === current }">
          <button @click="$emit('change-page', page)" class="page-link">{{ page }}</button>
        </li>
        <li class="page-item" :class="{ disabled: current === pages.length }">
          <button @click="$emit('change-page', current + 1)" class="page-link">Next</button>
        </li>
      </ul>
    </nav>
  </div>
</template>
<script>
export default {
  name: "DashboardCommentsPagination",
  props: {
    total: Number,
    perPage: Number,
    current: Number
  },
  computed: {
    pages() {
      return Array.from({ length: Math.ceil(this.total / this.perPage) }, (_, i) => i + 1);
    }
  }
};
</script>
