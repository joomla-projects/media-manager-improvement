<template>
    <li class="media-tree-item" :class="{active: isActive}">
        <a @click.stop.prevent="toggleItem()" :style="{'paddingLeft': 15 * level + 'px'}">
            <span class="item-icon material-icons">folder</span>
            <span class="item-name">{{ item.name }}</span>
        </a>
        <transition name="slide-fade">
            <media-tree v-if="item.children && item.children.length" v-show="isOpen"
                        :tree="item"></media-tree>
        </transition>
    </li>
</template>

<script>
    export default {
        name: 'media-tree-item',
        props: ['item'],
        computed: {
            /**
             * Whether or not the item is active
             */
            isActive () {
                return (this.item.path === this.state.currentDir);
            },
            /* Whether or not the item is open */
            isOpen () {
                return this.state.currentDir.includes(this.item.path);
            },
            /* Get the current level */
            level() {
                return this.item.path.split('/').length - 1;
            }
        },
        methods: {
            /**
             * Toggle an item open state
             */
            toggleItem () {
                this.$actions('setCurrentDir', this.item.path);
            }
        },
    }
</script>
