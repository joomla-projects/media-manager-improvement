<template>
    <li class="media-tree-item" :class="{active: isActive}">
        <a @click.stop.prevent="toggleItem()" :style="{'paddingLeft': 15 * level + 'px'}">
            <span class="item-icon material-icons">folder</span>
            <span class="item-name">{{ item.name }}</span>
        </a>
        <transition name="slide-fade">
            <media-tree v-if="item.children && item.children.length" v-show="isOpen"
                        :tree="item"
                        :currentDir="currentDir"></media-tree>
        </transition>
    </li>
</template>

<script>
    export default {
        name: 'media-tree-item',
        props: ['item', 'currentDir'],
        computed: {
            /* Whether or not the item is active */
            isActive () {
                return (this.item.path === this.currentDir);
            },
            /* Whether or not the item is open */
            isOpen () {
                return this.currentDir.includes(this.item.path);
            },
            /* Get the current level */
            level() {
                return this.item.path.split('/').length - 1;
            }
        },
        methods: {
            toggleItem () {
                this.isOpen = !this.isOpen;
                Media.Event.fire('dirChanged', this.item.path);
            }
        },
    }
</script>

<style>

    .media-tree-item {
        position: relative;
        display: block;
    }

    .media-tree-item a {
        display: block;
        position: relative;
        padding: 5px 10px;
        margin-bottom: 2px;
        cursor: pointer;
        color: #333;
        border-left: 4px solid transparent;
        text-decoration: none;
        height: 26px;
        line-height: 26px;
    }

    .media-tree-item a:hover {
        background-color: #e1e1e1;
        border-color: #646464;
        text-decoration: none;
    }

    .media-tree-item.active > a {
        background-color: transparent;
        border-color: #2384d3;
    }

    .item-icon {
        display: inline-block;
        line-height: normal;
        padding-right: 6px;
        vertical-align: middle;
        color: #8f8f8f;
    }

    .media-tree-item.active > a .item-icon {
        color: #2384d3;
    }

    .item-name {
        overflow: hidden;
        text-overflow: ellipsis;
        display: inline-block;
        vertical-align: middle;
        white-space: nowrap;
    }

    .media-tree-item.active > a .item-name {
        font-weight: bold;
    }

    .slide-fade-enter-active {
        transition: all .3s cubic-bezier(0.4, 0.0, 0.2, 1);
    }

    .slide-fade-leave-active {
        transition: all .2s cubic-bezier(0.4, 0.0, 0.2, 1);
    }

    .slide-fade-enter, .slide-fade-leave-to {
        transform: translateY(-10px);
        opacity: 0;
    }
</style>