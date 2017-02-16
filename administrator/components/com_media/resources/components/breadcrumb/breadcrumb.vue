<template>
    <ul class="media-breadcrumb">
        <li>
            <a @click.stop.prevent="goTo('/')">Home</a>
        </li>
        <li v-for="item in directories">
            <span class="divider material-icons">keyboard_arrow_right</span>
            <a @click.stop.prevent="goTo(item.path)">{{ item.name }}</a>
        </li>
    </ul>
</template>

<script>
    export default {
        name: 'media-breadcrumb',
        props: ['currentDir'],
        computed: {
            /* Get the directories from the current directory path */
            directories: function () {
                const items = [];
                this.currentDir.split('/')
                    .filter((crumb) => crumb.length !== 0)
                    .forEach((crumb) => {
                        items.push({
                            name: crumb,
                            path: this.currentDir.split(crumb)[0] + crumb,
                        });
                    })
                return items;
            }
        },
        methods: {
            /* Go to a path */
            goTo: function (path) {
                this.$actions('setCurrentDir', path);
            },
            isLast(item) {
                return this.directories.indexOf(item) === this.directories.length - 1;
            }
        },
    }
</script>