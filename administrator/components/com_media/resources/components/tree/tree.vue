<template>
    <ul class="media-tree">
        <media-tree-item v-for="item in directories" :item="item" :currentDir="currentDir"></media-tree-item>
    </ul>
</template>

<script>
    export default {
        name: 'media-tree',
        props: ['tree', 'currentDir'],
        computed: {
            directories: function () {
                return this.tree.children
                    .filter((item) => {
                        // Hide hidden files
                        return item.name.indexOf('.') !== 0;
                    })
                    .filter((item) => {
                        // Show only directories
                        return item.type === "dir";
                    })
                    .sort((a, b) => {
                        // Sort alphabetically
                        return (a.name.toUpperCase() < b.name.toUpperCase()) ? -1 : 1;
                    })
            }
        }
    }
</script>
<style>
    ul.media-tree {
        list-style: none;
    }
</style>