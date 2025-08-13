import { ref, computed } from "vue";
import { blogAPI } from "../api.js";

// Reactive state
const tags = ref([]);
const loading = ref(false);
const error = ref(null);

// Actions
const fetchTags = async () => {
  try {
    loading.value = true;
    error.value = null;
    const response = await blogAPI.getTags();
    if (response.data) {
      tags.value = response.data;
    }
  } catch (err) {
    console.error("Failed to fetch tags:", err);
    error.value = err.response?.data?.message || "Failed to fetch tags";
  } finally {
    loading.value = false;
  }
};

const searchTags = async (query) => {
  try {
    const response = await blogAPI.searchTags(query);
    return response.data || [];
  } catch (err) {
    console.error("Failed to search tags:", err);
    return [];
  }
};

const createTag = async (tagData) => {
  try {
    const response = await blogAPI.createTag(tagData);
    if (response.data) {
      tags.value.push(response.data);
      return { success: true, data: response.data };
    }
  } catch (err) {
    console.error("Failed to create tag:", err);
    return {
      success: false,
      message: err.response?.data?.message || "Failed to create tag",
      errors: err.response?.data?.errors,
    };
  }
};

const updateTag = async (id, tagData) => {
  try {
    const response = await blogAPI.updateTag(id, tagData);
    if (response.data) {
      const index = tags.value.findIndex((tag) => tag.id === id);
      if (index !== -1) {
        tags.value[index] = response.data;
      }
      return { success: true, data: response.data };
    }
  } catch (err) {
    console.error("Failed to update tag:", err);
    return {
      success: false,
      message: err.response?.data?.message || "Failed to update tag",
      errors: err.response?.data?.errors,
    };
  }
};

const deleteTag = async (id) => {
  try {
    await blogAPI.deleteTag(id);
    tags.value = tags.value.filter((tag) => tag.id !== id);
    return { success: true };
  } catch (err) {
    console.error("Failed to delete tag:", err);
    return {
      success: false,
      message: err.response?.data?.message || "Failed to delete tag",
    };
  }
};

// Initialize tags on first load
fetchTags();

export const useTagsStore = () => {
  return {
    // State
    tags: computed(() => tags.value),
    loading: computed(() => loading.value),
    error: computed(() => error.value),

    // Actions
    fetchTags,
    searchTags,
    createTag,
    updateTag,
    deleteTag,
  };
};
