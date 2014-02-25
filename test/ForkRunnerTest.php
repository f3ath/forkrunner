<?php
namespace F3\ForkRunner;

class ForkRunnerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage string is not callable
     */
    public function testRunNotACallable()
    {
        $runner = new ForkRunner();
        $runner->run(1, 'foo');
    }

    public function testFunctional()
    {
        $count = 256;
        exec('php '.__DIR__."/functional.php $count", $output);
        $this->assertEquals('OK', trim(array_pop($output)));
        $log = array();
        foreach ($output as $line) {
            list($pid, $action) = preg_split('/ /', trim($line));
            $log[$pid][] = $action;
        }
        $this->assertEquals($count, count($log));
        foreach ($log as $actions) {
            sort($actions);
            $this->assertEquals(array('payload', 'start', 'stop'), array_values($actions));
        }
    }
}
