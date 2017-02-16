<template>
    <ul class="media-tree">
        <media-tree-item v-for="item in directories" :item="item"></media-tree-item>
    </ul>
</template>

<script>
    export default {
        name: 'media-tree',
        props: ['tree'],
        computed: {
            /**
             * Get the directories
             */
            directories () {
                return this.tree.children
                // Hide hidden files
                    .filter(item => item.name.indexOf('.') !== 0)
                    // Show only directories
                    .filter(item =>  item.type === "dir")
                    // Sort alphabetically
                    .sort((a, b) => (a.name.toUpperCase() < b.name.toUpperCase()) ? -1 : 1);
            }
        }
    }
</script>