<template>
    <div class="media-browser-item-directory"
         @click="select(item)"
         @dblclick="open(item.path)">
        <div class="media-browser-item-icon">
            <i class="icon icon-folder-open"></i>
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
                Media.Event.fire('dirChanged', path);
            },
            select: function(item) {
                // TODO implement
                // Media.Event.fire('itemSelected', item);
            }
        }
    }
</script>