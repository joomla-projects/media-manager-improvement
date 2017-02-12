<template>
    <div class="media-browser">
        <ul class="media-browser-items">
            <media-browser-item v-for="item in content" :item="item"></media-browser-item>
        </ul>
    </div>
</template>
<script>
    export default {
        name: 'media-browser',
        props: ['content'],
        computed: {
            contents: function () {
                return this.content
                    .filter((item) => {
                        // Hide hidden files
                        return item.name.indexOf('.') !== 0;
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
        width: 100px;
        height: 100px;
        margin-bottom: 6px;
        margin-right: 6px;
        border: 3px solid #fff;
    }
    .media-browser-icon {
        font-size: 32px;
        margin: 15px 0;
        text-align: center;
    }
</style>