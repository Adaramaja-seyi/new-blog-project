<template>
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Recent Comments</h5>
      <router-link to="/dashboard/comments" class="btn btn-outline-primary btn-sm">View All</router-link>
    </div>
    <div class="card-body p-0">
      <div v-if="comments.length > 0" class="table">
        <div class="table-body">
          <div v-for="comment in comments" :key="comment.id" class="table-row">
            <div class="d-flex align-items-start">
              <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start mb-1">
                  <strong class="text-sm">{{ comment.author_name || 'Anonymous' }}</strong>
                  <small class="text-muted">{{ formatDate(comment.created_at) }}</small>
                </div>
                <p class="text-sm mb-1">{{ truncateText(comment.content, 60) }}</p>
                <small class="text-primary-blue">on "{{ comment.post?.title || 'Unknown Post' }}"</small>
                <div class="mt-2">
                  <button v-if="comment.status === 'pending'" @click="$emit('approve', comment.id)" class="btn btn-outline-success btn-sm me-1" title="Approve">
                    <i class="bi bi-check"></i>
                  </button>
                  <button @click="$emit('delete', comment.id)" class="btn btn-outline-danger btn-sm" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-else class="empty-state">
        <div class="empty-icon"><i class="bi bi-chat-dots"></i></div>
        <h6>No comments yet</h6>
        <p>Comments will appear here once readers start engaging with your posts.</p>
      </div>
    </div>
  </div>
</template>

<script>
import { blogAPI } from '../../api.js'

export default {
  name: 'RecentCommentsTable',
  emits: ['approve', 'delete'],
  data() {
    return {
      comments: [],
    }
  },
  mounted() {
    this.fetchComments()
  },
  methods: {
    async fetchComments() {
      try {
        const response = await blogAPI.getDashboardComments()
        if (response.data) {
          this.comments = response.data
        }
      } catch (e) {
        // handle error
      }
    },
    formatDate(dateString) {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
    },
    truncateText(text, maxLength) {
      if (!text) return ''
      if (text.length <= maxLength) return text
      return text.substring(0, maxLength) + '...'
    },
  },
}
</script>

<style scoped lang="scss">
.text-sm {
  font-size: 0.875rem;
}
@media (max-width: 768px) {
  .table-row .row .col-2 {
    margin-bottom: 0.5rem;
  }
}
</style>
