<template>
    <div v-if="page">
        <h1>{{ page.title }}</h1>
        <div v-html="page.content"></div>
        <div v-if="page.children && page.children.length">
            <div v-for="child in page.children" :key="child.id">
                <hr />
                <h3>{{ child.title }}</h3>
                <div v-html="child.content"></div>
                <Page :slug="child.slug" />
            </div>
        </div>
    </div>
    <div v-else>
        <p>Page not found</p>
    </div>
</template>

<script>
import { defineComponent } from 'vue';

export default defineComponent({
    props: ['slug'],
    data() {
        return {
            page: null,
        };
    },
    watch: {
        slug: 'fetchPage',
    },
    mounted() {
        this.fetchPage();
    },
    methods: {
        async fetchPage() {
            try {
                const response = await fetch(`/api/pages/${this.slug}`);
                if (response.ok) {
                    this.page = await response.json();
                } else {
                    this.page = null;
                }
            } catch (error) {
                this.page = null;
            }
        },
    },
});
</script>
