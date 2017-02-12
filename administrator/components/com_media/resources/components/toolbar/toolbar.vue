<template>
    <div class="media-toolbar">
        <media-breadcrumb :dir="dir"></media-breadcrumb>
    </div>
</template>

<style>
</style>

<script>
    export default {
        name: 'media-toolbar',
        props: ['dir'],
    }
</script>