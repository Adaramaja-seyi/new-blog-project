<template>
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Comments ({{ comments.length }})</h5>
      <div class="d-flex gap-2">
        <button @click="$emit('select-all')" class="btn btn-outline-primary btn-sm">Select All</button>
        <button @click="$emit('clear-selection')" class="btn btn-outline-secondary btn-sm">Clear Selection</button>
      </div>
    </div>
    <div class="card-body p-0">
      <div v-if="loading" class="loading-spinner">
        <div class="spinner-border" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
      <div v-else-if="comments.length > 0" class="table">
        <div class="table-header">
          <div class="row align-items-center">
            <div class="col-1">
              <input type="checkbox" class="form-check-input" :checked="allSelected" @change="$emit('toggle-select-all')" />
            </div>
            <div class="col-2">Author</div>
            <div class="col-4">Comment</div>
            <div class="col-2">Post</div>
            <div class="col-1">Status</div>
            <div class="col-2">Actions</div>
          </div>
        </div>
        <div class="table-body">
          <div v-for="comment in comments" :key="comment.id" class="table-row">
            <div class="row align-items-center">
              <div class="col-1">
                <input type="checkbox" class="form-check-input" :checked="selectedComments.includes(comment.id)" @change="$emit('toggle-comment-select', comment.id)" />
              </div>
              <div class="col-2">
                <h6 class="mb-1">{{ comment.author_name }}</h6>
                <small class="text-muted">{{ comment.author_email }}</small>
              </div>
              <div class="col-4">
                <p class="mb-1">{{ truncateText(comment.content, 100) }}</p>
                <small class="text-muted">{{ formatDate(comment.created_at) }}</small>
              </div>
              <div class="col-2">
                <span class="badge bg-light text-dark">{{ comment.post?.title || "Unknown Post" }}</span>
              </div>
              <div class="col-1">
                <span class="badge" :class="getStatusClass(comment.status)">{{ comment.status }}</span>
              </div>
              <div class="col-2">
                <div class="btn-group btn-group-sm">
                  <button @click="$emit('approve-comment', comment.id)" v-if="comment.status !== 'approved'" class="btn btn-light text-success" title="Approve">
                    <i class="bi bi-check"></i>
                  </button>
                  <button @click="$emit('mark-spam', comment.id)" v-if="comment.status !== 'spam'" class="btn btn-light text-warning" title="Mark as Spam">
                    <i class="bi bi-exclamation-triangle"></i>
                  </button>
                  <button @click="$emit('delete-comment', comment.id)" class="btn btn-light text-danger" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-else class="empty-state">
        <div class="empty-icon">
          <i class="bi bi-chat-left-text"></i>
        </div>
        <h5>No comments found</h5>
        <p>Comments will appear here as users engage with your posts.</p>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "DashboardCommentsTable",
  props: {
    comments: Array,
    selectedComments: Array,
    loading: Boolean,
    allSelected: Boolean
  },
  methods: {
    formatDate(dateString) {
      if (!dateString) return "";
      const date = new Date(dateString);
      return date.toLocaleDateString("en-US", { year: "numeric", month: "short", day: "numeric" });
    },
    getStatusClass(status) {
      switch (status) {
        case "approved": return "bg-success";
        case "pending": return "bg-warning";
        case "spam": return "bg-danger";
        default: return "bg-secondary";
      }
    },
    truncateText(text, maxLength) {
      if (!text) return "";
      return text.length > maxLength ? text.substring(0, maxLength) + "..." : text;
    }
  }
};
</script>
