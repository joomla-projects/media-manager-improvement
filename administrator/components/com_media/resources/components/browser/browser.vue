<template>
    <div class="media-browser">
        <div class="media-browser-items">
            <media-browser-item v-for="item in filteredContents" :item="item"></media-browser-item>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'media-browser',
        computed: {
            /**
             * Filter the contents of the currently selected directory
             */
            filteredContents () {
                return this.state.currentDirContents
                // Hide hidden files
                    .filter((item) => item.name.indexOf('.') !== 0)
                    .sort((a, b) => {
                        // Sort by type and alphabetically
                        if (a.type !== b.type) {
                            return (a.type === 'dir') ? -1 : 1;
                        } else {
                            return (a.name.toUpperCase() < b.name.toUpperCase()) ? -1 : 1;
                        }
                    })
            }
        }
    }
</script>