<template>
    <input type="file">
</template>
<script>
    export default {
        props: {
            accept: {
                type: String,
            },
            extensions: {
                default: () => [],
            },
            name: {
                type: String,
                default: 'file',
            },
            multiple: {
                type: Boolean,
            },
        },
    }
</script>