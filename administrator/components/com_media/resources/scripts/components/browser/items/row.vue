<template>
    <tr>
        <td class="media-browser-item-cell" :class="{selected: isSelected}">
            {{ item.name }}
        </td>
        <td class="media-browser-item-cell" :class="{selected: isSelected}">
            {{ item.size }}
        </td>
        <td class="media-browser-item-cell" :class="{selected: isSelected}">
            {{ dimension }}
        </td>
        <td class="media-browser-item-cell" :class="{selected: isSelected}">
            {{ item.mime_type }}
        </td>
        <td class="media-browser-item-cell" :class="{selected: isSelected}">
            {{ item.create_date_formatted }}
        </td>
        <td class="media-browser-item-cell" :class="{selected: isSelected}">
            {{ item.modified_date_formatted }}
        </td>
    </tr>
</template>

<script>
    export default {
        name: 'media-browser-item-file',
        props: ['item'],
        computed: {
            dimension() {
                if (!this.item.width) {
                    return '';
                }
                return `${this.item.width} x ${this.item.height}`;
            }
        }
    }
</script> 