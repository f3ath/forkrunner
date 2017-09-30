# ForkRunner

A simple library to run a process in multiple processes

## Installation
Via composer:
`$ composer require "f3ath/forkrunner"`

## Usage
```php
<?php
$func = function ($n) {
    return $n * $n;
};
$runner = new \F3\ForkRunner\ForkRunner();
$args = [[3], [4], [5]];
$result = $runner->run($func, $args); // [9, 16, 25]
```
