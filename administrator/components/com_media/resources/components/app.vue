<template>
    <div>
        <div class="row-fluid">
            <div class="span3 media-sidebar">
                <media-tree :tree="tree" :dir="dir"></media-tree>
            </div>
            <div class="span9 media-browser">
                <media-breadcrumb :dir="dir"></media-breadcrumb>
                <media-browser :content="content" v-if="!isLoading"></media-browser>
                <div v-else>Loading...</div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'media-app',
        data() {
            return {
                // A global is loading flag
                isLoading: false,
                // The current selected directory
                dir: '/',
                // The content of the selected directory
                content: [],
                // The tree structure
                tree: {path: '/', children: []},
                // The api base url
                baseUrl: '/administrator/index.php?option=com_media&task=api.files&format=json'
            }
        },
        methods: {
            getContents() {
                this.isLoading = true;
                let url = this.baseUrl + '&path=' + this.dir;
                jQuery.getJSON(url, (response) => {
                    // Get the contents from the data attribute
                    let content = response.data;
                    // Update the current directory content
                    this.content = content;
                    // Find the directory node by path and update its children
                    this._updateLeafByPath(this.tree, this.dir, content);
                }).error(() => {
                    alert("Error loading directory content.");
                }).always(() => {
                    this.isLoading = false;
                });
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
                    for(let i=0; i < obj.children.length; i++) {
                        if(this._updateLeafByPath(obj.children[i], path, data)) {
                            return true;
                        }
                    }
                }

                return false;
            }
        },
        created() {
            // Listen to the directory changed event
            Media.Event.listen('dirChanged', (dir) => {
                this.dir = dir;
            });
        },
        mounted() {
            // Load the tree data
            this.getContents();
        },
        watch: {
            dir: function () {
                this.getContents();
            }
        }
    }
</script>