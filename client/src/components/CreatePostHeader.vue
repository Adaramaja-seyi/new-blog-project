<template>
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2>{{ isEditing ? "Edit Post" : "Create New Post" }}</h2>
      <p class="text-muted mb-0">
        {{
          isEditing
            ? "Update your blog post"
            : "Write and publish your next blog post"
        }}
      </p>
    </div>
    <div class="d-flex gap-2">
      <button
        @click="$emit('save-draft')"
        class="btn btn-outline-warning"
        :disabled="saving"
      >
        <i class="bi bi-save me-2"></i>
        {{ saving ? "Saving..." : "Save Draft" }}
      </button>
      <button
        @click="$emit('publish-post')"
        class="btn btn-primary"
        :disabled="saving"
      >
        <i class="bi bi-send me-2"></i>
        {{
          saving ? "Publishing..." : isEditing ? "Update Post" : "Publish Post"
        }}
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
    saving: {
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

.btn {
  font-weight: 500;
  border-radius: 6px;
  padding: 0.5rem 1rem;
  transition: all 0.2s ease;

  &:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
  }

  &:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }
}

.btn-outline-warning {
  border-color: #ffc107;
  color: #856404;

  &:hover:not(:disabled) {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
  }
}

.btn-primary {
  background-color: #0d6efd;
  border-color: #0d6efd;

  &:hover:not(:disabled) {
    background-color: #0b5ed7;
    border-color: #0b5ed7;
  }
}
</style>
