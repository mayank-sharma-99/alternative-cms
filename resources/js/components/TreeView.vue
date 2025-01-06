<template>
  <div>
    <button @click="showCreatePageForm">Create Page</button>
    
    <div v-if="pages.length">
      <ul>
        <li v-for="page in pages" :key="page.id">
          <span>{{ page.title }}</span>
          <button @click="showEditPageForm(page)">Edit</button>
          <button @click="deletePage(page.id)">Delete</button>
          <ul v-if="page.children && page.children.length">
            <li v-for="child in page.children" :key="child.id">
              <span>{{ child.title }}</span>
              <button @click="showEditPageForm(child)">Edit</button>
              <button @click="deletePage(child.id)">Delete</button>
            </li>
          </ul>
        </li>
      </ul>
    </div>

    <div v-if="showForm" class="form">
      <h2>{{ formType === 'create' ? 'Create Page' : 'Edit Page' }}</h2>
      <form @submit.prevent="handleSubmit">
        <input v-model="formData.title" placeholder="Title" required />
        <textarea v-model="formData.content" placeholder="Content" required></textarea>
        <input v-model="formData.slug" placeholder="Slug" required />
        <button type="submit">{{ formType === 'create' ? 'Create' : 'Update' }} Page</button>
        <button @click="cancelForm">Cancel</button>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      pages: [],
      showForm: false,
      formType: 'create',
      formData: {
        title: '',
        content: '',
        slug: '',
        parent_id: null
      }
    };
  },
  created() {
    this.fetchPages();
  },
  methods: {
    async fetchPages() {
      try {
        const response = await axios.get('/api/pages');
        this.pages = response.data;
      } catch (error) {
        console.error("There was an error fetching pages: ", error);
      }
    },
    showCreatePageForm() {
      this.showForm = true;
      this.formType = 'create';
      this.formData = { title: '', content: '', slug: '', parent_id: null };
    },
    showEditPageForm(page) {
      this.showForm = true;
      this.formType = 'edit';
      this.formData = { ...page };
    },
    async handleSubmit() {
      try {
        if (this.formType === 'create') {
          await axios.post('/api/pages', this.formData);
        } else {
          await axios.put(`/api/pages/${this.formData.id}`, this.formData);
        }
        this.fetchPages();
        this.cancelForm();
      } catch (error) {
        console.error("Error saving page: ", error);
      }
    },
    cancelForm() {
      this.showForm = false;
    },
    async deletePage(pageId) {
      try {
        await axios.delete(`/api/pages/${pageId}`);
        this.fetchPages();
      } catch (error) {
        console.error("Error deleting page: ", error);
      }
    }
  }
};
</script>

<style scoped>
.form {
  padding: 20px;
  border: 1px solid #ccc;
  margin-top: 20px;
}
div {
  font-family: Arial, sans-serif;
  margin: 20px;
}

h1, h2 {
  color: #333;
}

button {
  background-color: #007bff;
  color: white;
  border: none;
  padding: 8px 12px;
  font-size: 14px;
  cursor: pointer;
  border-radius: 4px;
  margin-left: 8px;
}

button:hover {
  background-color: #0056b3;
}

button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

ul {
  list-style-type: none;
  padding-left: 0;
}

li {
  margin-bottom: 10px;
  display: flex;
  align-items: center;
}

li span {
  font-size: 16px;
  color: #555;
}

ul > li > ul {
  margin-left: 20px;
  border-left: 2px solid #ccc;
  padding-left: 10px;
}

.form {
  padding: 20px;
  border: 1px solid #ccc;
  margin-top: 20px;
  background-color: #f9f9f9;
  border-radius: 8px;
  width: 400px;
  max-width: 100%;
}

input, textarea {
  width: 100%;
  padding: 10px;
  margin-bottom: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 14px;
}

textarea {
  resize: vertical;
  min-height: 100px;
}

input:focus, textarea:focus {
  border-color: #007bff;
  outline: none;
}

button[type="submit"] {
  background-color: #28a745;
}

button[type="submit"]:hover {
  background-color: #218838;
}

button[type="button"] {
  background-color: #dc3545;
  margin-left: 10px;
}

button[type="button"]:hover {
  background-color: #c82333;
}

@media (max-width: 600px) {
  .form {
    width: 100%;
  }

  ul {
    padding-left: 10px;
  }
}
</style>
