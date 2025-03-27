<?php

require __DIR__ . '/../vendor/autoload.php';

use ReactphpX\Asyncify\Asyncify;
use React\EventLoop\Loop;
use React\Promise\Deferred;

$word = 'world';
Asyncify::call(function () use ($word) {
    return json_encode([
        'hello' => $word
    ]);
})->then(function ($data) {
    var_dump($data);
}, function ($e) {
    var_dump($e->getMessage(), 'reject');
})->catch(function ($e) {
    var_dump($e->getMessage(), 'catch');
});


Asyncify::call(function () {
    return file_get_contents(__DIR__ . '/test.txt');
})->then(function ($data) {
    var_dump($data);
}, function ($e) {
    var_dump($e->getMessage(), 'reject');
})->catch(function ($e) {
    var_dump($e->getMessage(), 'catch');
});

Asyncify::call(function () {
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


