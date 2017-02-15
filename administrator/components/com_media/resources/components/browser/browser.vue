<template>
    <div class="media-browser">
        <div class="media-browser-items">
            <media-browser-item v-for="item in content" :item="item"></media-browser-item>
        </div>
    </div>
</template>
<style>
    /** TODO: move styles to dedicated css file **/
    .media-browser-items {
        padding: 15px;
    }

    .media-browser-item {
        display: inline-block;
        position: relative;
        vertical-align: top;
        margin-top: 15px;
        margin-right: 15px;
        width: calc(14.285714285714286% - 16px);
    }

    .media-browser-item-icon {
        padding-top: 75%;
        position: relative;
        border-radius: 1px;
        width: 100%;
    }

    .media-browser-item-icon .icon {
        display: inline-block;
        width: auto;
        height: auto;
        margin: 0;
        line-height: 100px;
    }

    .media-browser-item-info {
        text-align: center;
        padding: 5px 0;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        width: 100px;
    }
</style>
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