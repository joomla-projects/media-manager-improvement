<template>
    <ul class="breadcrumb media-breadcrumb">
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
                return this.dir.split('/');
            }
        }
    }
</script>