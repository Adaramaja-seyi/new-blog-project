<template>
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">Preview</h5>
    </div>
    <div class="card-body">
      <div class="preview-content">
        <h4>{{ post.title || "Post Title" }}</h4>
        <p class="text-muted">
          {{ post.excerpt || "Post excerpt will appear here..." }}
        </p>
        <div class="preview-meta">
          <span class="badge bg-light text-dark me-2">{{ post.category || "Category" }}</span>
          <small class="text-muted">{{ currentDate }}</small>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "PostPreview",
  props: {
    post: { type: Object, required: true }
  },
  computed: {
    currentDate() {
      return new Date().toLocaleDateString();
    }
  }
};
</script>

<style scoped lang="scss">
.preview-content {
  h4 {
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
  }
  .preview-meta {
    margin-top: 1rem;
  }
}
.badge {
  font-size: 0.75rem;
  font-weight: 500;
}
</style>
