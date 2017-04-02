<template>
    <ul class="media-tree">
        <media-tree-item v-for="item in directories" :item="item"></media-tree-item>
    </ul>
</template>

<script>
    export default {
        name: 'media-tree',
        props: ['root'],
        computed: {
            /* Get the directories */
            directories() {
                return this.$store.getters.getSelectedDirectoryDirectories.sort((a, b) => {
                    // Sort by type and alphabetically
                    return (a.name.toUpperCase() < b.name.toUpperCase()) ? -1 : 1;
                });
            },
        }
    }
</script>