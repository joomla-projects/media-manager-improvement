<template>
    <div class="media-infobar">
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
</template>
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