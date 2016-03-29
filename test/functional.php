<?php
/**
 *  Funtional test
 */

require_once(__DIR__.'/../vendor/autoload.php');

$runner = new \F3\ForkRunner\ForkRunner();
$result = $runner->run(
    $argv[1], 
    function(){
        //return pow(getmypid(), 2);
        return function () {return 1;};
    }
);
echo serialize($result);
