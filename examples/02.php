<?php

require __DIR__ . '/../vendor/autoload.php';

use Reactphp\Framework\Asyncify\Asyncify;
use React\EventLoop\Loop;

$word = 'world';
$stream = Asyncify::call(function () use ($word) {
    return json_encode([
        'hello' => $word
    ]);
}, true);

$stream->on('data', function ($data) {
    var_dump($data);
});

$stream->on('close', function () {

});

$stream->on('error', function ($e) {
    var_dump($e->getMessage(), 'error');
});


Loop::addTimer(1, function () {
    Asyncify::terminate();
});