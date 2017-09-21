<template>
    <media-modal v-if="$store.state.showRenameModal" :size="'sm'" @close="close()">
        <h3 slot="header" class="modal-title">{{ translate('COM_MEDIA_RENAME') }}</h3>
        <div slot="body">
            <form class="form" @submit.prevent="save" novalidate>
                <div class="form-group">
                    <label for="name">{{ translate('COM_MEDIA_NAME') }}</label>
                    <input id="name" class="form-control" placeholder="Name"
                           v-focus="true" v-model.trim="name" @input="name = $event.target.value"
                           required autocomplete="off">
                </div>
            </form>
        </div>
        <div slot="footer">
            <button class="btn btn-link" @click="close()">{{ translate('JCANCEL') }}</button>
            <button class="btn btn-success" @click="save()" :disabled="!isValid()">{{ translate('JAPPLY') }}
            </button>
        </div>
    </media-modal>
</template>

<script>
    import * as types from "./../../store/mutation-types";
    import {focus} from 'vue-focus';

    export default {
        name: 'media-rename-modal',
        directives: {focus: focus},
        computed: {
            name() {
                return this.$store.state.selectedItems[this.$store.state.selectedItems.length -1].name;
            }
        },
        methods: {
            /* Check if the the form is valid */
            isValid() {
                return (this.name);
            },
            /* Close the modal instance */
            close() {
                this.reset();
                this.$store.commit(types.HIDE_RENAME_MODAL);
            },
            /* Save the form and create the folder */
            save() {
                // Check if the form is valid
                if (!this.isValid()) {
                    // TODO mark the field as invalid
                    return;
                }

                // Create the directory
                this.$store.dispatch('rename', {
                    name: this.name,
                });
                this.reset();
            },
            /* Reset the form */
            reset() {
                this.name = '';
            }
        }
    }
</script>
