<template>
    <div class="media-browser-item-directory"
         @click="select(item)"
         @dblclick="open(item.path)">
        <div class="media-browser-item-preview">
            <span class="icon material-icons">folder</span>
        </div>
        <div class="media-browser-item-info">
            {{ item.name }}
        </div>
    </div>
</template>
<script>
    export default {
        name: 'media-browser-item-directory',
        props: ['item'],
        methods: {
            open: function (path) {
                this.$actions('setCurrentDir', path);
            },
            select: function(item) {
                // TODO implement
            }
        }
    }
</script>