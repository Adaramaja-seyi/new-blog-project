<template>
  <div class="post-card">
    <div class="post-image">
      <img 
        :src="post.featured_image || '/placeholder-image.jpg'" 
        :alt="post.title"
        class="img-fluid"
      />
      <div class="category-tag">{{ post.category?.name || post.category || 'General' }}</div>
    </div>
    
    <div class="post-content">
      <h3 class="post-title">
        <router-link :to="{ name: 'PostDetail', params: { slug: post.slug } }">
          {{ post.title }}
        </router-link>
      </h3>
      
      <p class="post-excerpt">
        {{ truncateExcerpt(post.excerpt) }}
      </p>
      
      <div class="post-meta">
        <div class="post-author">
          <i class="bi bi-person-circle me-1"></i>
          {{ post.user?.name || post.author || 'Unknown Author' }}
        </div>
        <div class="post-date">
          <i class="bi bi-calendar3 me-1"></i>
          {{ formatDate(post.created_at) }}
        </div>
      </div>
      
      <div class="post-stats">
        <span class="stat-item">
          <i class="bi bi-eye me-1"></i>
          {{ post.views || 0 }}
        </span>
        <span class="stat-item">
          <i class="bi bi-chat-dots me-1"></i>
          {{ post.comments_count || 0 }}
        </span>
        <span class="stat-item">
          <i class="bi bi-heart me-1"></i>
          {{ post.likes_count || 0 }}
        </span>
      </div>
      
      <router-link 
        :to="{ name: 'PostDetail', params: { slug: post.slug } }"
        class="btn btn-outline-primary btn-sm read-more-btn"
      >
        Read More
        <i class="bi bi-arrow-right ms-1"></i>
      </router-link>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PostCard',
  props: {
    post: {
      type: Object,
      required: true
    }
  },
  methods: {
    truncateExcerpt(text, maxLength = 120) {
      if (!text) return '';
      if (text.length <= maxLength) return text;
      return text.substring(0, maxLength) + '...';
    },
    formatDate(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    }
  }
}
</script>

<style scoped lang="scss">
.post-card {
  background: var(--bg-primary);
  border: 1px solid var(--border-light);
  border-radius: 0.75rem;
  overflow: hidden;
  transition: all 0.3s ease;
  height: 100%;
  display: flex;
  flex-direction: column;
  
  &:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary-blue);
  }
  
  .post-image {
    position: relative;
    height: 200px;
    overflow: hidden;
    
    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.3s ease;
    }
    
    .category-tag {
      position: absolute;
      top: 1rem;
      left: 1rem;
      background-color: var(--primary-blue);
      color: var(--primary-white);
      padding: 0.25rem 0.75rem;
      border-radius: 1rem;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
  }
  
  .post-content {
    padding: var(--component-spacing);
    flex: 1;
    display: flex;
    flex-direction: column;
  }
  
  .post-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
    line-height: 1.4;
    
    a {
      color: var(--text-primary);
      text-decoration: none;
      transition: color 0.2s ease;
      
      &:hover {
        color: var(--primary-blue);
      }
    }
  }
  
  .post-excerpt {
    color: var(--text-secondary);
    font-size: 0.9rem;
    line-height: 1.6;
    margin-bottom: 1rem;
    flex: 1;
  }
  
  .post-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 0.75rem;
    font-size: 0.8rem;
    color: var(--text-muted);
    
    .post-author,
    .post-date {
      display: flex;
      align-items: center;
      
      i {
        font-size: 0.9rem;
      }
    }
  }
  
  .post-stats {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
    font-size: 0.8rem;
    color: var(--text-muted);
    
    .stat-item {
      display: flex;
      align-items: center;
      
      i {
        font-size: 0.9rem;
      }
    }
  }
  
  .read-more-btn {
    align-self: flex-start;
    font-weight: 500;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
    
    &:hover {
      transform: translateX(2px);
    }
  }
}

// Hover effect for image
.post-card:hover .post-image img {
  transform: scale(1.05);
}

// Responsive adjustments
@media (max-width: 768px) {
  .post-card {
    .post-image {
      height: 180px;
    }
    
    .post-title {
      font-size: 1.1rem;
    }
    
    .post-meta,
    .post-stats {
      flex-wrap: wrap;
      gap: 0.5rem;
    }
  }
}
</style>
