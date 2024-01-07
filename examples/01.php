<?php

require __DIR__ . '/../vendor/autoload.php';

use Reactphp\Framework\Asyncify\Async;
use React\EventLoop\Loop;
use React\Promise\Deferred;

$word = 'world';
Async::call(function () use ($word) {
    return [
        'hello' => $word
    ];
})->then(function ($data) {
    var_dump($data);
}, function ($e) {
    var_dump($e->getMessage(), 'reject');
})->catch(function ($e) {
    var_dump($e->getMessage(), 'catch');
});


Async::call(function () {
    return file_get_contents(__DIR__ . '/test.txt');
})->then(function ($data) {
    var_dump($data);
}, function ($e) {
    var_dump($e->getMessage(), 'reject');
})->catch(function ($e) {
    var_dump($e->getMessage(), 'catch');
});

Async::call(function () {
    $deferred = new Deferred();
    Loop::addTimer(1, function () use ($deferred) {
        $deferred->resolve('hello world promise');
    });
    return $deferred->promise();
})->then(function ($data) {
    var_dump($data);
}, function ($e) {
    var_dump($e->getMessage(), 'reject');
})->catch(function ($e) {
    var_dump($e->getMessage(), 'catch');
});

Loop::addTimer(2, function () {
    Async::terminate();
});
