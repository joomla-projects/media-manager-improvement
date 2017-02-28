<template>
    <div class="media-infobar col-md-4">
        <div class="card">
            <div class="card-header">
            </div>
            <div class="card-block">
                <h4 class="item-name">
                    {{ item.name }}
                </h4>
                <div v-if="item.path === '/'" class="text-center">
                    <span class="fa fa-info"></span>
                    Select file or folder to view its details.
                </div>
                <ul v-else></ul>
            </div>
        </div>
    </div>
</template>
<style>
    .media-infobar {
        background-color: #fafafa;
        height: 100%;
        overflow: hidden;
        position: absolute;
        right: 0;
        top: 0;
        z-index: 4;
        float: none;
        padding: 0;
    }
    .media-infobar .card {
        height: 100%;
        border-top: 0;
        border-right: 0;
        border-bottom: 0;
        border-radius: 0;
    }
</style>
<script>
    export default {
        name: 'media-infobar',
        computed: {
            /* Get the item to show in the infobar */
            item() {

                // Check if there are selected items
                const selectedItems = this.$store.state.selectedItems;

                // If there is only one selected item, show that one.
                if(selectedItems.length === 1) {
                    return selectedItems[0];
                }

                // If there are more selected items, use the last one
                if(selectedItems.length > 1) {
                    return selectedItems.slice(-1)[0];
                }

                // Use the currently selected directory as a fallback
                return this.$store.getters.getSelectedDirectory;
            }
        }
    }
</script>