<template>
    <div class="media-toolbar">
        <media-breadcrumb :dir="dir"></media-breadcrumb>
    </div>
</template>

<style>
    .media-toolbar {
        background: #f8f8f8;
        padding: 15px;
        border-bottom: 1px solid #dedede;
    }
</style>

<script>
    export default {
        name: 'media-toolbar',
        props: ['dir'],
    }
</script>