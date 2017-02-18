<template>
    <div class="media-container" :style="{minHeight: fullHeight}">
        <media-toolbar></media-toolbar>
        <div class="media-main">
            <div class="media-sidebar">
                <media-tree></media-tree>
            </div>
            <media-browser></media-browser>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'media-app',
        data() {
            return {
                // The full height of the app in px
                fullHeight: '',
            };
        },
        methods: {
            // Get the contents of the current directory
            getContents() {
                this.$api.getContents(this.state.currentDir)
                    .then((response) => {
                        this.$actions('setCurrentDirContents', response.data);
                    })
                    .catch((error) => {
                        console.log(error);
                        // this.$actions('setError', error)
                    });
            },
            // Set the full height on the app container
            setFullHeight () {
                this.fullHeight = window.innerHeight - this.$el.offsetTop + 'px';
            },
        },
        mounted() {
            // Load the tree data
            this.getContents();
            this.$nextTick(function () {
                this.setFullHeight();
                // Add the global resize event listener
                window.addEventListener('resize', this.setFullHeight)
            });
        },
        watch: {
            'state.currentDir': function () {
                this.getContents();
            }
        },
        beforeDestroy: function () {
            // Add the global resize event listener
            window.removeEventListener('resize', this.setFullHeight)
        },
    }
</script>