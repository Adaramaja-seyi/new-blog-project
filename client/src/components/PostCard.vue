<template>
  <div class="card h-100">
    <div class="position-relative">
      <img
        :src="getImageUrl(post.featured_image)"
        :alt="post.title"
        width="100%"
        height="200"
      />
      <span
        class="category-tag badge bg-primary position-absolute top-0 start-0 m-3"
      >
        {{ post.category?.name || "General" }}
      </span>
    </div>
    <div class="card-body d-flex flex-column">
      <h5 class="card-title">
        <router-link
          :to="{ name: 'PostDetail', params: { slug: post.slug } }"
          class="text-decoration-none"
        >
          {{ post.title }}
        </router-link>
      </h5>
      <p class="card-text text-muted flex-grow-1">
        {{ truncateExcerpt(post.excerpt) }}
      </p>
      <div class="card-text d-flex flex-wrap gap-3 mb-3 small text-muted">
        <span>
          <i class="bi bi-person-circle me-1"></i>
          {{ post.user?.name || post.author || "Unknown Author" }}
        </span>
        <span>
          <i class="bi bi-calendar3 me-1"></i>
          {{ formatDate(post.created_at) }}
        </span>
      </div>
      <div class="card-text d-flex flex-wrap gap-3 mb-3 small text-muted">
        <span>
          <i class="bi bi-eye me-1"></i>
          {{ post.views_count || 0 }}
        </span>
        <span>
          <i class="bi bi-chat-dots me-1"></i>
          {{ post.comments_count || 0 }}
        </span>
        <span>
          <i class="bi bi-heart me-1"></i>
          {{ post.likes_count || 0 }}
        </span>
      </div>
      <router-link
        :to="{ name: 'PostDetail', params: { slug: post.slug } }"
        class="btn btn-outline-primary btn-sm mt-auto"
      >
        Read More
        <i class="bi bi-arrow-right ms-1"></i>
      </router-link>
    </div>
  </div>
</template>

<script>
import { useAuth } from "../stores/auth";

export default {
  name: "PostCard",
  props: {
    post: {
      type: Object,
      required: true,
    },
  },
  computed: {
    auth() {
      return useAuth();
    },
  },
  methods: {
    truncateExcerpt(text, maxLength = 25) {
      if (!text) return "";
      if (text.length <= maxLength) return text;
      return text.substring(0, maxLength) + "...";
    },
    formatDate(dateString) {
      if (!dateString) return "";
      const date = new Date(dateString);
      return date.toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
      });
    },
    getImageUrl(path) {
      return `http://localhost:8000${path}`;
    },
  },

  mounted() {
    // console.log(this.post,33);
  },
};
</script>

<style scoped lang="scss">
.category-tag {
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
</style>
