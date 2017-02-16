<template>
    <ul class="media-breadcrumb">
        <li>
            <a @click.stop.prevent="goTo('/')">Home</a>
        </li>
        <li v-for="crumb in crumbs">
            <span class="divider material-icons">keyboard_arrow_right</span>
            <a @click.stop.prevent="goTo(crumb.path)">{{ crumb.name }}</a>
        </li>
    </ul>
</template>

<script>
    export default {
        name: 'media-breadcrumb',
        computed: {
            /**
             * Get the crumbs from the current directory path
             */
            crumbs () {
                const items = [];
                this.state.currentDir.split('/')
                    .filter((crumb) => crumb.length !== 0)
                    .forEach((crumb) => {
                        items.push({
                            name: crumb,
                            path: this.state.currentDir.split(crumb)[0] + crumb,
                        });
                    });

                return items;
            },
            /**
             * Check if item is the last element in the list
             */
            isLast(item) {
                return this.directories.indexOf(item) === this.directories.length - 1;
            }
        },
        methods: {
            /**
             * Go to a path
             */
            goTo: function (path) {
                this.$actions('setCurrentDir', path);
            },
        },
    }
</script>