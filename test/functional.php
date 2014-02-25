<?php
/**
 *  Funtional test
 */

require_once(dirname(__DIR__).'/vendor/autoload.php');

class MyForkRunner extends F3\ForkRunner\ForkRunner
{
    public function onThreadStart($pid)
    {
        echo "$pid start\n";
    }

    public function onThreadExit($pid, $status)
    {
        echo "$pid stop\n";
    }
}
$runner = new MyForkRunner();
$runner->run($argv[1], function(){$pid = getmypid(); echo "$pid payload\n";});
echo "OK\n";
