<template>
    <div class="media-toolbar">
        <media-breadcrumb :currentDir="currentDir"></media-breadcrumb>
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
        props: ['currentDir'],
    }
</script>