<template>
    <div class="media-toolbar">
        <media-breadcrumb></media-breadcrumb>
    </div>
</template>

<script>
    import * as types from "./../../store/mutation-types";
    export default {
        name: 'media-toolbar',
        methods: {
            /* Close the modal instance */
            showCreateFolderModal() {
                this.$store.commit(types.SHOW_CREATE_FOLDER_MODAL);
            }
        },
    }
</script>