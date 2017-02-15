<template>
    <div class="media-toolbar">
        <div class="create-wrapper">
            <div class="btn-group">
                <button class="btn btn-primary">Upload</button>
                <button class="btn dropdown-toggle btn-primary" data-toggle="dropdown">
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li><a href="#">Create Folder</a></li>
                    <li class="divider"></li>
                    <li><a href="#">Upload File</a></li>
                    <li><a href="#">Upload Folder</a></li>
                </ul>
            </div>
        </div>
        <media-breadcrumb :currentDir="currentDir"></media-breadcrumb>
    </div>
</template>

<style>
    .media-toolbar {
        background-color: #fafafa;
        border-bottom: 1px solid transparent;
        box-shadow: 0 2px 4px rgba(0,0,0,.2);
        height: 31px;
        position: relative;
        z-index: 5;
        display: flex;
        padding: 11px 0 9px;
    }
    .create-wrapper {
        width: 16.5%;
    }

    .btn-group {
        margin: 0 15px;
    }

    .btn-group .btn-primary:first-child {
        padding-left: 35px;
        padding-right: 35px;
    }

    .btn-group .dropdown-toggle {
        padding-bottom: 3px;
    }

    .btn-group .dropdown-toggle .caret {
        border-top-color: #fff;
        border-bottom-color: #fff;
    }
</style>

<script>
    export default {
        name: 'media-toolbar',
        props: ['currentDir'],
    }
</script>