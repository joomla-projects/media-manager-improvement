<?php

@include __DIR__.'/vendor/autoload.php';

if ( ! is_dir(__DIR__.'/tests/files')) {
    mkdir(__DIR__.'/tests/files', 0777, true);
}

$fs = new League\Flysystem\Adapter\Local(__DIR__.'/tests/files');
foreach (array_reverse($fs->listContents()) as $info) {
    if (is_file(__DIR__.'/tests/files/'.$info['path'])) {
        unlink(__DIR__.'/tests/files/'.$info['path']);
    } else {
        rmdir(__DIR__.'/tests/files/'.$info['path']);
    }
}
