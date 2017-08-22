<template>
    <div class="media-adapter" @click="selectAdapter">
        <div class="media-adapter-name">{{ adapter.displayName }}</div>
        <media-tree :root="adapter.root" ></media-tree>
    </div>
</template>

<script>
    export default {
        name: 'media-adapter',
        props: ['adapter'],
        methods: {
            selectAdapter() {
                this.$store.dispatch('getContents', this.adapter.root);
            }
        }
    }
</script>
