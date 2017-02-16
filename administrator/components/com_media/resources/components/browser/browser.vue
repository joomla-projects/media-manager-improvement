<template>
    <div class="media-browser">
        <div class="media-browser-items">
            <media-browser-item v-for="item in content" :item="item"></media-browser-item>
        </div>
    </div>
</template>
<style>
    .media-browser {
        width: 83.5%;
    }

    .media-browser-items {
        padding: 15px;
        display: flex;
        flex-wrap: wrap;
    }

    .media-browser-item {
        position: relative;
        margin-top: 15px;
        margin-right: 15px;
        width: calc(25% - 15px);
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
    }

    .media-browser-item-preview {
        position: relative;
        border-radius: 1px;
        width: 100%;
        height: 100px;
        background: #fff;
        box-shadow: 0 1px 1px 0 rgba(0,0,0,.2);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .media-browser-item-preview .icon {
        font-size: 80px;
        color: #2384d3;
    }

    .media-browser-item-info {
        padding: 8px 5px;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        text-align: center;
    }

    @media only screen and (min-width : 979px) {
        .media-browser-item {
            width: calc(20% - 15px);
        }
        .media-browser-item-preview {
            height: 150px;
        }
    }

    @media only screen and (min-width : 1200px) {
        .media-browser-item {
            width: calc(14.285714285714286% - 15px);
        }
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