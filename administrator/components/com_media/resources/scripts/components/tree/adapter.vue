<template>
    <div class="media-adapter" @click="selectAdapter">
        <div class="media-adapter-name">{{ account.displayName }}</div>
        <media-tree :root="account.root" ></media-tree>
    </div>
</template>

<script>
    export default {
        name: 'media-adapter',
        props: ['account'],
        methods: {
            selectAdapter() {
                this.$store.dispatch('getContents', this.account.root);
            }
        }
    }
</script>
