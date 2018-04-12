<template>
    <media-modal v-if="$store.state.showShareModal" :size="'sm'" @close="close()" :show-close="false">
        <h3 slot="header" class="modal-title">{{ translate('COM_MEDIA_SHARE') }}</h3>
        <div slot="body">
            <div class="desc">
                {{ translate('COM_MEDIA_SHARE_DESC') }}
            </div>
        </div>
        <div slot="footer">
            <button class="btn btn-primary" @click="close()">{{ translate('JCANCEL') }}</button>
        </div>
    </media-modal>
</template>

<script>
    import * as types from "./../../store/mutation-types";

    export default {
        name: 'media-rename-modal',
        data() {
           return {

           } 
        },
        computed: {
            
        },
        methods: {
            /* Close the modal instance */
            close() {
                this.$store.commit(types.HIDE_SHARE_MODAL);
            },
        }
    }
</script>
