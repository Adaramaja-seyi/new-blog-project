import { ref, computed } from 'vue'
import { blogAPI } from '../api.js'

// Reactive state
const posts = ref([])
const currentPost = ref(null)
const loading = ref(false)
const error = ref(null)

// Computed properties
const publishedPosts = computed(() => posts.value.filter(post => post.is_published))
const draftPosts = computed(() => posts.value.filter(post => !post.is_published))

// Get all posts
const fetchPosts = async (filters = {}) => {
    try {
        loading.value = true
        error.value = null

        const response = await blogAPI.getPosts(filters.page || 1, filters.limit || 10)

        if (response.data.success) {
            posts.value = response.data.data
            return { success: true, data: response.data.data }
        } else {
            error.value = response.data.message
            return { success: false, message: response.data.message }
        }
    } catch (err) {
        console.error('Error fetching posts:', err)
        error.value = 'Failed to fetch posts'
        return { success: false, message: 'Failed to fetch posts' }
    } finally {
        loading.value = false
    }
}

// Get single post
const fetchPost = async (id) => {
    try {
        loading.value = true
        error.value = null

        const response = await blogAPI.getPost(id)

        if (response.data.success) {
            currentPost.value = response.data.data
            return { success: true, data: response.data.data }
        } else {
            error.value = response.data.message
            return { success: false, message: response.data.message }
        }
    } catch (err) {
        console.error('Error fetching post:', err)
        error.value = 'Failed to fetch post'
        return { success: false, message: 'Failed to fetch post' }
    } finally {
        loading.value = false
    }
}

// Create new post
const createPost = async (postData) => {
    try {
        loading.value = true
        error.value = null

        const response = await blogAPI.createPost(postData)

        if (response.data.success) {
            const newPost = response.data.data
            posts.value.unshift(newPost)
            return { success: true, data: newPost }
        } else {
            error.value = response.data.message
            return { success: false, message: response.data.message }
        }
    } catch (err) {
        console.error('Error creating post:', err)
        error.value = 'Failed to create post'
        return { success: false, message: 'Failed to create post' }
    } finally {
        loading.value = false
    }
}

// Update post
const updatePost = async (id, postData) => {
    try {
        loading.value = true
        error.value = null

        const response = await blogAPI.updatePost(id, postData)

        if (response.data.success) {
            const updatedPost = response.data.data
            const index = posts.value.findIndex(post => post.id === id)
            if (index !== -1) {
                posts.value[index] = updatedPost
            }
            if (currentPost.value && currentPost.value.id === id) {
                currentPost.value = updatedPost
            }
            return { success: true, data: updatedPost }
        } else {
            error.value = response.data.message
            return { success: false, message: response.data.message }
        }
    } catch (err) {
        console.error('Error updating post:', err)
        error.value = 'Failed to update post'
        return { success: false, message: 'Failed to update post' }
    } finally {
        loading.value = false
    }
}

// Delete post
const deletePost = async (id) => {
    try {
        loading.value = true
        error.value = null

        const response = await blogAPI.deletePost(id)

        if (response.data.success) {
            posts.value = posts.value.filter(post => post.id !== id)
            if (currentPost.value && currentPost.value.id === id) {
                currentPost.value = null
            }
            return { success: true }
        } else {
            error.value = response.data.message
            return { success: false, message: response.data.message }
        }
    } catch (err) {
        console.error('Error deleting post:', err)
        error.value = 'Failed to delete post'
        return { success: false, message: 'Failed to delete post' }
    } finally {
        loading.value = false
    }
}

// Get dashboard stats
const fetchDashboardStats = async () => {
    try {
        loading.value = true
        error.value = null

        const response = await blogAPI.getDashboardStats()

        if (response.data.success) {
            return { success: true, data: response.data }
        } else {
            error.value = response.data.message
            return { success: false, message: response.data.message }
        }
    } catch (err) {
        console.error('Error fetching dashboard stats:', err)
        error.value = 'Failed to fetch dashboard statistics'
        return { success: false, message: 'Failed to fetch dashboard statistics' }
    } finally {
        loading.value = false
    }
}

// Clear error
const clearError = () => {
    error.value = null
}

// Export the posts store
export const usePosts = () => {
    return {
        // State
        posts: computed(() => posts.value),
        currentPost: computed(() => currentPost.value),
        loading: computed(() => loading.value),
        error: computed(() => error.value),

        // Computed
        publishedPosts,
        draftPosts,

        // Actions
        fetchPosts,
        fetchPost,
        createPost,
        updatePost,
        deletePost,
        fetchDashboardStats,
        clearError
    }
}






