import { ref, computed } from "vue";
import { blogAPI } from "../api.js";

// Reactive state
const categories = ref([]);
const loading = ref(false);
const error = ref(null);

// Computed properties
const activeCategories = computed(() =>
  categories.value.filter((category) => category.is_active)
);

// Actions
const fetchCategories = async () => {
  try {
    loading.value = true;
    error.value = null;
    const response = await blogAPI.getCategories();
    if (response.data) {
      categories.value = response.data;
    }
  } catch (err) {
    console.error("Failed to fetch categories:", err);
    error.value = err.response?.data?.message || "Failed to fetch categories";
  } finally {
    loading.value = false;
  }
};

const createCategory = async (categoryData) => {
  try {
    const response = await blogAPI.createCategory(categoryData);
    if (response.data) {
      categories.value.push(response.data);
      return { success: true, data: response.data };
    }
  } catch (err) {
    console.error("Failed to create category:", err);
    return {
      success: false,
      message: err.response?.data?.message || "Failed to create category",
      errors: err.response?.data?.errors,
    };
  }
};

const updateCategory = async (id, categoryData) => {
  try {
    const response = await blogAPI.updateCategory(id, categoryData);
    if (response.data) {
      const index = categories.value.findIndex((cat) => cat.id === id);
      if (index !== -1) {
        categories.value[index] = response.data;
      }
      return { success: true, data: response.data };
    }
  } catch (err) {
    console.error("Failed to update category:", err);
    return {
      success: false,
      message: err.response?.data?.message || "Failed to update category",
      errors: err.response?.data?.errors,
    };
  }
};

const deleteCategory = async (id) => {
  try {
    await blogAPI.deleteCategory(id);
    categories.value = categories.value.filter((cat) => cat.id !== id);
    return { success: true };
  } catch (err) {
    console.error("Failed to delete category:", err);
    return {
      success: false,
      message: err.response?.data?.message || "Failed to delete category",
    };
  }
};


fetchCategories();

export const useCategoriesStore = () => {
  return {
    // State
    categories: computed(() => categories.value),
    activeCategories,
    loading: computed(() => loading.value),
    error: computed(() => error.value),

    // Actions
    fetchCategories,
    createCategory,
    updateCategory,
    deleteCategory,
  };
};
