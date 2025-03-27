# Asyncify

一个基于ReactPHP的异步处理库，可以轻松地将同步代码转换为异步执行。

## 特性

- 简单易用的API
- 支持Promise和Stream两种异步模式
- 基于进程池的异步执行
- 自动管理进程生命周期

## 安装

```bash
composer require reactphp-x/asyncify
```

## 基本用法

### 1. 异步执行并返回Promise

```php
use ReactphpX\Asyncify\Asyncify;

// 初始化进程池（可选）
Asyncify::init(1, 1); // 最小1个进程，最大1个进程

// 异步执行并获取结果
Asyncify::call(function () {
    return json_encode(['hello' => 'world']);
})->then(function ($data) {
    var_dump($data);
}, function ($e) {
    var_dump($e->getMessage());
});
```

### 2. 异步读取文件

```php
Asyncify::call(function () {
    return file_get_contents('path/to/file.txt');
})->then(function ($data) {
    var_dump($data);
});
```

### 3. 流式处理

```php
$stream = Asyncify::call(function ($stream) {
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
```

## API

### Asyncify::init($min, $max)

初始化进程池

- `$min`: 最小进程数
- `$max`: 最大进程数

### Asyncify::call(callable $callable, $isStream = false, $prioritize = 0)

异步执行一个函数

- `$callable`: 要执行的函数
- `$isStream`: 是否以流的方式返回结果
- `$prioritize`: 优先级

返回：
- 当`$isStream = false`时，返回Promise
- 当`$isStream = true`时，返回Stream

## 注意事项

1. 确保在使用前正确安装并配置ReactPHP相关依赖
2. 异步执行的函数中不能访问外部变量的引用
3. 对于长时间运行的任务，建议使用流式处理

## 许可证

MIT