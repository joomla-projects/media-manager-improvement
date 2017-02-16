<template>
    <div class="media-container" :style="{minHeight: fullHeight}">
        <media-toolbar :currentDir="currentDir"></media-toolbar>
        <div class="media-main">
            <div class="media-sidebar">
                <media-tree :tree="tree" :currentDir="currentDir"></media-tree>
            </div>
            <media-browser :content="currentDirContent"></media-browser>
        </div>
    </div>
</template>

<style>
    .media-container {
        position: absolute;
        left: 0;
        width: 100%;
        margin-top: -62px;
        display: flex;
        flex-direction: column;
    }

    .media-main {
        background: #eee;
        flex-grow: 1;
        display: flex;
    }

    .media-sidebar {
        width: 16.5%;
        background: #fafafa;
        border-right: 1px solid #e1e1e1;
        padding-bottom: 50px;
    }
</style>

<script>
    export default {
        name: 'media-app',
        data() {
            return {
                currentDir: '/',
                // A global is loading flag
                isLoading: false,
                // The content of the selected directory
                currentDirContent: [],
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
                let url = this.baseUrl + '&path=' + this.currentDir;
                jQuery.getJSON(url, (response) => {
                    // Get the contents from the data attribute
                    let content = response.data;
                    // Update the current directory content
                    this.currentDirContent = content;
                    // Find the directory node by path and update its children
                    this._updateLeafByPath(this.tree, this.currentDir, content);
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
        created() {
            // Listen to the directory changed event
            window.Media.Event.listen('dirChanged', (dir) => {
                this.currentDir = dir;
            });
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
            'currentDir': function () {
                this.getContents();
            }
        },
        beforeDestroy: function () {
            // Add the global resize event listener
            window.removeEventListener('resize', this.setFullHeight)
        },
    }
</script>