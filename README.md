# reactphp-x-asyncify

## install

```
composer require reactphp-x/asyncify -vvv
```

## Usage

promise

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

use ReactphpX\Asyncify\Asyncify;
use React\EventLoop\Loop;
use React\Promise\Deferred;

$word = 'world';
Asyncify::call(function () use ($word) {
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

Loop::addTimer(2, function () {
    Asyncify::terminate();
});

```

stream
```php
<?php

require __DIR__ . '/../vendor/autoload.php';

use ReactphpX\Asyncify\Asyncify;
use React\EventLoop\Loop;

$word = 'world';
$stream = Asyncify::call(function () use ($word) {
    return [
        'hello' => $word
    ];
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
```
```php
<?php

require __DIR__ . '/../vendor/autoload.php';

use ReactphpX\Asyncify\Asyncify;
use React\Stream\ThroughStream;
use React\EventLoop\Loop;

$stream = Asyncify::call(function (){
    $stream = new ThroughStream();
    $i = 0;
    $timer = Loop::addPeriodicTimer(1, function () use ($stream, &$i) {
        $i++;
        $stream->write('hello world:'. $i);
    });
    Loop::addTimer(5, function () use ($timer, $stream) {
        Loop::cancelTimer($timer);
        $stream->end();
    });
    return $stream;
}, true);

$stream->on('data', function ($data) {
    var_dump($data);
});

$stream->on('close', function () {

});

$stream->on('error', function ($e) {
    var_dump($e->getMessage(), 'error');
});

Loop::addTimer(6, function () {
    Asyncify::terminate();
});
```
