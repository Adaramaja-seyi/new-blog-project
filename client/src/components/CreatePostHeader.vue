<template>
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2>{{ isEditing ? "Edit Post" : "Create New Post" }}</h2>
      <p class="text-muted mb-0">
        {{
          isEditing ? "Update your blog post" : "Write and publish your next blog post"
        }}
      </p>
    </div>
    <div class="d-flex gap-2">
      <button @click="$emit('save-draft')" class="btn btn-outline-secondary">
        <i class="bi bi-save me-2"></i>
        Save Draft
      </button>
      <button @click="$emit('publish-post')" class="btn btn-primary">
        <i class="bi bi-send me-2"></i>
        {{ isEditing ? "Update Post" : "Publish Post" }}
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: "CreatePostHeader",
  props: {
    isEditing: {
      type: Boolean,
      default: false,
    },
  },
  emits: ["save-draft", "publish-post"],
};
</script>

<style scoped lang="scss">
.d-flex {
  gap: 1rem;
}
</style>
