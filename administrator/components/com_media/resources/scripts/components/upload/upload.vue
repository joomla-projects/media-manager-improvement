<template>
    <input type="file" class="hidden"
           :name="name"
           :multiple="multiple"
           :accept="accept"
           @change="upload"
           ref="fileInput">
</template>
<script>
    export default {
        name: 'media-upload',
        props: {
            accept: {
                type: String,
            },
            extensions: {
                default: () => [],
            },
            name: {
                type: String,
                default: 'file',
            },
            multiple: {
                type: Boolean,
                default: false,
            },
        },
        methods: {
            /* Open the choose-file dialog */
            chooseFiles() {
                this.$refs['fileInput'].click();
            },
            /* Upload files */
            upload(e) {
                e.preventDefault();
                const files = e.target.files;

                // Do the upload

            },
        },
        created() {
            // Listen to the toolbar upload click event
            MediaManager.Event.listen('onClickUpload', () => this.chooseFiles());
        },
    }
</script>