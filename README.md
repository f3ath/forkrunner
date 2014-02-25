ForkRunner
==========

A simple framework to run a process in multiple threads

#Installation
Via composer:
`$ composer require "f3ath/forkrunner"`

#Usage
```php
$runner = new F3\ForkRunner\ForkRunner();
$runner->run(10, function(){printf("%s\n", getmypid());});
```
will produce something like
```
10013
10014
10017
10015
10021
10022
10018
10020
10016
10019
```
