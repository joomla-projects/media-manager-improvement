<template>
    <li class="media-tree-item" :class="{active: isActive }">
        <a @click.stop.prevent="toggleItem()">{{ item.name }}</a>
        <media-tree v-if="item.children && item.children.length" v-show="isOpen" :tree="item" :dir="dir"></media-tree>
    </li>
</template>

<script>
    export default {
        name: 'media-tree-item',
        props: ['item', 'dir'],
        data() {
            return {
                isOpen: false,
            }
        },
        computed: {
            isActive: function() {
                return (this.item.path === this.dir);
            }
        },
        methods: {
            toggleItem: function() {
                Media.Event.fire('dirChanged', this.item.path);
                this.isOpen = !this.isOpen;
            }
        },
    }
</script>

<style>
    .media-tree-item.active > a {
        font-weight: bold;
    }
    .media-tree-item a {
        cursor: pointer;
    }
</style>