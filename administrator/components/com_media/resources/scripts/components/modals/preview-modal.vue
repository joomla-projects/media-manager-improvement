<template>
    <media-modal v-if="item" :size="'sm'" @close="close()">
        <h3 slot="header" class="modal-title">{{ item.name }}</h3>
        <div slot="body">
            {{ item.url }}
        </div>
        <div slot="footer">
            <button class="btn btn-link" @click="close()">{{ translate('JCANCEL') }}</button>
        </div>
    </media-modal>
</template>

<script>
    import * as types from "../../store/mutation-types";
    import * as item from "../../components/browser/items/item";

    export default {
        name: 'media-preview-modal',
        data() {
            return {source: ''};
        },
        computed: {
            /* Get the item to show in the modal */
            item() {
                // Use the currently selected directory as a fallback
                return this.$store.state.previewItem;
            }
        },
        methods: {
            /* Close the modal */
            close() {
                this.$store.commit(types.HIDE_PREVIEW_MODAL);
            }
        }
    }
</script>
