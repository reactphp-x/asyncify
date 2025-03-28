<?php

require __DIR__ . '/../vendor/autoload.php';

use ReactphpX\Asyncify\Asyncify;
use React\EventLoop\Loop;

$word = 'world';
Asyncify::call(function () use ($word) {
    return json_encode([
        'hello' => $word
    ]);
}, true)->then(function ($stream) {
    $stream->on('data', function ($data) {
        var_dump($data);
    });

    $stream->on('close', function () {});

    $stream->on('error', function ($e) {
        var_dump($e->getMessage(), 'error');
    });
});
