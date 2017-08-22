<template>
    <div class="media-disk">
        <div class="media-disk-name">{{ disk.displayName }}</div>
        <media-adapter v-for="account in disk.accounts" :account="account"></media-adapter>
    </div>
</template>

<script>
    export default {
        name: 'media-disk',
        props: ['disk'],
    }
</script>
