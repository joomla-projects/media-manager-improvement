<template>
    <div class="media-toolbar">
        <div class="create-wrapper">
            <div class="btn-group">
                <button class="btn btn-success">Upload</button>
                <button class="btn dropdown-toggle btn-success" data-toggle="dropdown">
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
        <div class="media-tools">
            <a href="#" class="material-icons">list</a>
            <a href="#" class="material-icons">info_outline</a>
            <a href="#" class="material-icons">help_outline</a>
            <a href="#" class="material-icons">settings</a>
        </div>
    </div>
</template>

<style>
    .media-toolbar {
        background-color: #fafafa;
        border-bottom: 1px solid transparent;
        box-shadow: 0 2px 4px rgba(0, 0, 0, .2);
        height: 31px;
        position: relative;
        z-index: 5;
        display: flex;
        padding: 11px 0 9px;
        border-top: 1px solid #10223e;
    }

    .create-wrapper {
        width: 16.5%;
    }

    .btn-group {
        margin: 0 15px;
    }

    .btn-group .btn-success:first-child {
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

    .media-breadcrumb {
        flex-grow: 1;
    }

    .media-tools {
        display: flex;
        width: calc(16.5% - 15);
        text-align: right;
        justify-content: flex-end;
        margin-right: 15px;
    }

    .media-tools a {
        display: inline-block;
        padding: 0 10px;
        color: #7d7d7d;
        text-decoration: none;
        line-height: 32px;
    }

    .media-tools a:hover {
        color: #333;
    }

    .media-tools-divider {
        border-right: 1px solid #e5e5e5;
        display: inline-block;
        height: 53px;
        margin: -10.5px 8px;
        vertical-align: middle;
    }

</style>

<script>
    export default {
        name: 'media-toolbar',
        props: ['currentDir'],
    }
</script>