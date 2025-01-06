<template>
    <div v-if="page">
      <h1>{{ page.title }}</h1>
      <p v-html="page.content"></p>
    </div>
  </template>
  
  <script>
  import { ref, onMounted } from "vue";
  import axios from "axios";
  
  export default {
    setup() {
      const page = ref(null);
  
      onMounted(async () => {
        const slugPath = window.location.pathname.substring(1);
        const response = await axios.get(`/api/pages/${slugPath}`);
        page.value = response.data;
      });
  
      return { page };
    },
  };
  </script>
  