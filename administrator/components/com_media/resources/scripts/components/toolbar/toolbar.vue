<template>
    <div class="media-toolbar">
        <div class="create-wrapper">
            <div class="btn-group">
                <button class="btn btn-success">New</button>
                <button class="btn dropdown-toggle btn-success" data-toggle="dropdown">
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li><a href="#" @click.prevent="showCreateFolderModal()">Create Folder</a></li>
                    <li class="divider"></li>
                    <li><a href="#">Upload File</a></li>
                    <li><a href="#">Upload Folder</a></li>
                </ul>
            </div>
        </div>
        <media-breadcrumb></media-breadcrumb>
        <div class="media-tools">
            <a href="#"><span class="fa fa-list"></span></a>
            <a href="#"><span class="fa fa-info-circle"></span></a>
            <a href="#"><span class="fa fa-question-circle"></span></a>
            <a href="#"><span class="fa fa-cog"></span></a>
        </div>
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
            },
            /* Handle keydown events */
            onKeyDown(event) {
                if (this.show && event.keyCode == 27) {
                    this.close();
                }
            }
        },
    }
</script>