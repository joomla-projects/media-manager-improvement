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

<style>
    .media-breadcrumb {
        margin: 0;
        padding: 0 15px;
        list-style: none;
        height: 31px;
        line-height: 31px;
    }
    .media-breadcrumb > li {
        display: inline-block;
    }
    .media-breadcrumb > li > a {
        cursor: pointer;
        color: #555;
        text-decoration: none;
        font-size: 16px;
    }
    .media-breadcrumb > li > .divider {
        color: #555;
        vertical-align: middle;
        height: 31px;
        line-height: 31px;
    }
    .media-breadcrumb > li:last-child a {
        font-weight: bold;
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
                            path: this.currentDir.split(crumb)[0] + crumb,
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