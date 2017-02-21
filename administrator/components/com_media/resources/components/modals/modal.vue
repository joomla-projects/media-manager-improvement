<template>
    <div class="media-modal-backdrop" @click="close()">
        <div class="modal">
            <div class="modal-header">
                <button v-if="showCloseButton" type="button" class="close">×</button>
                <slot name="header"></slot>
            </div>
            <div class="modal-body">
                <slot name="body"></slot>
            </div>
            <div class="modal-footer">
                <slot name="footer"></slot>
            </div>
        </div>
    </div>
</template>

<style>
    /** TODO DN extract styles **/
    .media-modal-backdrop {
        position: fixed;
        z-index: 1040;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, .8);
        display: table;
        transition: opacity .3s ease;
    }
</style>

<script>
    // TODO DN: transition and advanced styling
    // TODO DN: perhaps use a better modal than the b2 modal
    import * as types from "./../../store/mutation-types";
    export default {
        name: 'media-modal',
        props: {
            /* Whether or not the close button in the header should be shown */
            showClose: {
                type: Boolean,
                default: true,
            },
        },
        methods: {
            /* Close the modal instance */
            close() {
                this.$store.commit(types.HIDE_CREATE_FOLDER_MODAL);
            },
            /* Handle keydown events */
            onKeyDown(event) {
                if (this.show && event.keyCode == 27) {
                    this.close();
                }
            }
        },
        mounted() {
            // Listen to keydown events on the document
            document.addEventListener("keydown", this.onKeyDown);
        },
        beforeDestroy() {
            // Remove the keydown event listener
            document.removeEventListener('keydown', this.onKeyDown);
        },
    }
</script>