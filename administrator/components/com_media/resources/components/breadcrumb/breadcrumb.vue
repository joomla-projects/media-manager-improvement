<template>
    <ul class="media-breadcrumb">
        <li>
            <a @click.stop.prevent="goTo('/')"><i class="icon icon-home"></i></a>
            <span class="divider">/</span>
        </li>
        <li v-for="item in directories">
            <a @click.stop.prevent="goTo(item.path)">{{ item.name }}</a>
            <span class="divider" v-if="!isLast(item)">/</span>
        </li>
    </ul>
</template>

<style>
    .media-breadcrumb {
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .media-breadcrumb > li {
        display: inline-block;
    }
    .media-breadcrumb > li > a {
        cursor: pointer;
    }
    .media-breadcrumb > li > .divider {
        padding: 0 3px;
        color: #ccc;
    }
</style>

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
                            path: this.currentDir.split(crumb)[0] + '/' + crumb,
                        });
                    })
                return items;
            }
        },
        methods: {
            /* Go to a path */
            goTo: function (path) {
                Media.Event.fire('dirChanged', path);
            },
            isLast(item) {
                return this.directories.indexOf(item) === this.directories.length - 1;
            }
        },
    }
</script>