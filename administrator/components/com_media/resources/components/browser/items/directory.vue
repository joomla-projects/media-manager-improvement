<template>
    <div @click="select(item)"
         @dblclick="open(item.path)">
        <p class="media-browser-icon">
            <i class="icon icon-folder-close"></i>
        </p>
        <p class="text-center">
            {{ item.name }}
        </p>
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