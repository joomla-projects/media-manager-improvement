<template>
    <div class="media-browser-item-directory">
        <div class="d-flex justify-content-center flex-column"
             @dblclick="goTo(item.path)">
            <div class="media-browser-item-preview d-flex justify-content-center">
                <span class="fa fa-folder-o"></span>
            </div>
            <div class="media-browser-item-info">
                {{ item.name }}
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'media-browser-item-directory',
        props: ['item'],
        methods: {
            /* Go to a path */
            goTo: function (path) {
                this.$store.dispatch('getContents', path);
            },
        }
    }
</script>