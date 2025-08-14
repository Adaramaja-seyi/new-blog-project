<template>
  <div class="card mb-4">
    <div class="card-body">
      <!-- Title -->
      <div class="mb-4">
        <label class="form-label">Post Title *</label>
        <input
          type="text"
          class="form-control form-control-lg"
          v-model="post.title"
          placeholder="Enter your post title..."
          :class="{ 'is-invalid': errors.title }"
        />
        <div v-if="errors.title" class="invalid-feedback">
          {{ errors.title }}
        </div>
      </div>
      <!-- Excerpt -->
      <div class="mb-4">
        <label class="form-label">Excerpt</label>
        <textarea
          class="form-control"
          v-model="post.excerpt"
          rows="3"
          placeholder="Brief description of your post..."
        ></textarea>
        <small class="text-muted">A short summary that will appear in post previews</small>
      </div>
      <!-- Content -->
      <div class="mb-4">
        <label class="form-label">Content *</label>
        <textarea
          class="form-control"
          v-model="post.content"
          rows="15"
          placeholder="Write your post content here..."
          :class="{ 'is-invalid': errors.content }"
        ></textarea>
        <div v-if="errors.content" class="invalid-feedback">
          {{ errors.content }}
        </div>
        <small class="text-muted">Use markdown formatting for better content structure</small>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "PostFormMain",
  props: {
    post: { type: Object, required: true },
    errors: { type: Object, default: () => ({}) }
  }
};
</script>

<style scoped lang="scss">
.form-control-lg {
  font-size: 1.25rem;
  font-weight: 600;
}
</style>
