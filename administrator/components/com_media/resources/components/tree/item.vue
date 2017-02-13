<template>
    <li class="media-tree-item" :class="{active: isActive}">
        <a @click.stop.prevent="toggleItem()">
            <span>
                <i class="icon" :class="{'icon-folder-open': isActive, 'icon-folder-close': !isActive}"></i>
                {{ item.name }}
            </span>
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