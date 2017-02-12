<template>
    <div class="media-browser">
        <ul class="media-browser-items">
            <media-browser-item v-for="item in content" :item="item"></media-browser-item>
        </ul>
    </div>
</template>
<style>
    /** TODO: move styles to dedicated css file **/
    .media-browser-items {
        margin: 0;
        padding: 15px;
        list-style: none;
    }

    .media-browser-items li {
        display: inline-block;
        float: left;
        margin-bottom: 6px;
        margin-right: 6px;
        padding: 2px;
    }

    .media-browser-item {
    }

    .media-browser-item-icon {
        vertical-align: middle;
        width: 100px;
        height: 100px;
        font-size: 50px;
        text-align: center;
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