<template>
    <div class="media-container" :style="{minHeight: fullHeight}">
        <media-toolbar></media-toolbar>
        <div class="media-main">
            <div class="media-sidebar">
                <media-tree :tree="tree"></media-tree>
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
                // The tree structure
                tree: {path: '/', children: []},
                // The api base url
                baseUrl: '/administrator/index.php?option=com_media&task=api.files&format=json',
                // The full height of the app in px
                fullHeight: '',
            };
        },
        methods: {
            // Get the content of the current directory
            getContents() {
                this.isLoading = true;
                let url = this.baseUrl + '&path=' + this.state.currentDir;
                jQuery.getJSON(url, (response) => {
                    // Get the contents from the data attribute
                    let contents = response.data;
                    // Update the current directory contents in the store
                    this.$actions('setCurrentDirContents', contents);
                    // Find the directory node by path and update its children
                    this._updateLeafByPath(this.tree, this.state.currentDir, contents);
                }).error(() => {
                    alert("Error loading directory content.");
                }).always(() => {
                    this.isLoading = false;
                });
            },
            // Set the full height on the app container
            setFullHeight () {
                this.fullHeight = window.innerHeight - this.$el.offsetTop + 'px';
            },
            // TODO move to a mixin
            _updateLeafByPath(obj, path, data) {
                // Set the node children
                if (obj.path && obj.path === path) {
                    this.$set(obj, 'children', data);
                    return true;
                }

                // Loop over the node children
                if (obj.children && obj.children.length) {
                    for (let i = 0; i < obj.children.length; i++) {
                        if (this._updateLeafByPath(obj.children[i], path, data)) {
                            return true;
                        }
                    }
                }

                return false;
            }
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