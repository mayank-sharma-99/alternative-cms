<template>
  <div class="page-management">
    <h1>Page Management</h1>
    <tree-view :pages="pages" @edit-page="editPage" @delete-page="deletePage" />
    <hr />

    <h2>{{ isEditing ? "Edit Page" : "Add New Page" }}</h2>
    <form @submit.prevent="savePage" class="form">
      <div class="form-group">
        <label for="title">Title:</label>
        <input v-model="form.title" id="title" required />
      </div>

      <div class="form-group">
        <label for="slug">Slug:</label>
        <input v-model="form.slug" id="slug" required />
      </div>

      <div class="form-group">
        <label for="parent">Parent:</label>
        <select v-model="form.parent_id" id="parent">
          <option value="">No Parent</option>
          <option v-for="page in flatPages" :key="page.id" :value="page.id">
            {{ page.title }}
          </option>
        </select>
      </div>

      <div class="form-group">
        <label for="content">Content:</label>
        <textarea v-model="form.content" id="content"></textarea>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">
          {{ isEditing ? "Update" : "Create" }} Page
        </button>
        <button type="button" @click="resetForm" v-if="isEditing" class="btn btn-secondary">
          Cancel
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import TreeView from "../components/TreeView.vue";
import { ref, reactive, onMounted } from "vue";
import axios from "axios";

export default {
  components: { TreeView },
  setup() {
    const pages = ref([]);
    const flatPages = ref([]);
    const isEditing = ref(false);
    const form = reactive({
      id: null,
      parent_id: null,
      title: "",
      slug: "",
      content: "",
    });

    const loadPages = async () => {
      const response = await axios.get("/api/pages");
      pages.value = response.data;
      flattenPages(response.data);
    };

    const flattenPages = (nestedPages, parentPrefix = "") => {
      flatPages.value = [];
      const traverse = (nodes, prefix = "") => {
        nodes.forEach((node) => {
          flatPages.value.push({ id: node.id, title: prefix + node.title });
          if (node.children && node.children.length) {
            traverse(node.children, prefix + "--");
          }
        });
      };
      traverse(nestedPages, parentPrefix);
    };

    const savePage = async () => {
      try {
        if (isEditing.value) {
          await axios.put(`/api/pages/${form.id}`, form);
        } else {
          await axios.post("/api/pages", form);
        }
        resetForm();
        await loadPages();
      } catch (error) {
        console.error("Error saving page:", error.response.data);
      }
    };

    const editPage = (page) => {
      form.id = page.id;
      form.title = page.title;
      form.slug = page.slug;
      form.parent_id = page.parent_id;
      form.content = page.content;
      isEditing.value = true;
    };

    const deletePage = async (pageId) => {
      if (confirm("Are you sure you want to delete this page?")) {
        await axios.delete(`/api/pages/${pageId}`);
        await loadPages();
      }
    };

    const resetForm = () => {
      form.id = null;
      form.title = "";
      form.slug = "";
      form.parent_id = null;
      form.content = "";
      isEditing.value = false;
    };

    onMounted(loadPages);

    return {
      pages,
      flatPages,
      isEditing,
      form,
      savePage,
      editPage,
      deletePage,
      resetForm,
    };
  },
};
</script>

<style scoped>
.page-management {
  max-width: 600px;
  margin: 0 auto;
  font-family: Arial, sans-serif;
}

h1,
h2 {
  text-align: center;
  color: #333;
}

hr {
  margin: 20px 0;
}

.form {
  background: #f9f9f9;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.form-group {
  margin-bottom: 15px;
}

label {
  display: block;
  font-weight: bold;
  margin-bottom: 5px;
}

input,
select,
textarea {
  width: 100%;
  padding: 8px;
  font-size: 14px;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}

textarea {
  resize: vertical;
}

.form-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
}

.btn {
  padding: 10px 15px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.btn-primary {
  background-color: #007bff;
  color: #fff;
}

.btn-primary:hover {
  background-color: #0056b3;
}

.btn-secondary {
  background-color: #6c757d;
  color: #fff;
}

.btn-secondary:hover {
  background-color: #5a6268;
}
</style>
