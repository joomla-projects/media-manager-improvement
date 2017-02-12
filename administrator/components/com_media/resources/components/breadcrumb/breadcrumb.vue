<template>
    <ul class="breadcrumb media-breadcrumb">
        <li>
            <a @click.stop.prevent="goTo('/')">Root</a>
            <span class="divider">/</span>
        </li>
        <li v-for="item in directories">
            <a @click.stop.prevent="goTo(item.path)">{{ item.name }}</a>
            <span class="divider">/</span>
        </li>
    </ul>
</template>

<style>
    .breadcrumb a {
        cursor: pointer;
    }
</style>

<script>
    export default {
        name: 'media-breadcrumb',
        props: ['dir'],
        computed: {
            /* Get the directories from the current directory path */
            directories: function () {
                const items = [];
                this.dir.split('/')
                    .filter((crumb) => crumb.length !== 0)
                    .forEach((crumb) => {
                        items.push({
                            name: crumb,
                            path: this.dir.split(crumb)[0] + '/' + crumb,
                        });
                    })
                return items;
            }
        },
        methods: {
            /* Go to a path */
            goTo: function (path) {
                Media.Event.fire('dirChanged', path);
            }
        },
    }
</script>