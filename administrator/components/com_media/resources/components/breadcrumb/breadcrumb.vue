<template>
    <ul class="breadcrumb media-breadcrumb">
        <li>
            <a href="#">Root</a>
            <span class="divider">/</span>
        </li>
        <li v-for="item in directories">
            <a href="#">{{ item }}</a>
            <span class="divider">/</span>
        </li>
    </ul>
</template>

<script>
    export default {
        name: 'media-breadcrumb',
        props: ['dir'],
        computed: {
            directories: function () {
                return this.dir.split('/').filter((el) => el.length !== 0);
            }
        }
    }
</script>