<template>
    <div class="media-browser">
        <div class="media-browser-items">
            <media-browser-item v-for="item in content" :item="item"></media-browser-item>
        </div>
    </div>
</template>

<script>
    import FileMixin from "./../../mixins/file";
    export default {
        name: 'media-browser',
        props: ['content'],
        mixins: [FileMixin],
        computed: {
            contents: function () {
                return this.content
                    .filter((item) => {
                        // Hide hidden files
                        return item.name.indexOf('.') !== 0;
                    })
                    .map((item) => {
                        // Add file extension
                        if (item.type !== 'dir') {
                            item.extension = this.getFileExtension(item.name);
                        }
                        return item;
                    })
                    .sort((a, b) => {
                        // Sort by type and alphabetically
                        if (a.type !== b.type) {
                            return (a.type === 'dir') ? -1 : 1;
                        } else {
                            return (a.name.toUpperCase() < b.name.toUpperCase()) ? -1 : 1;
                        }
                    })
            }
        }
    }
</script>