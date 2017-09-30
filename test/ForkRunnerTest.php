<?php
namespace F3\ForkRunner;

class ForkRunnerTest extends \PHPUnit_Framework_TestCase
{
    public function testParallelExecutionIsFasterThanSerial()
    {
        $func = function ($n) {
            sleep(1);
            return $n * $n;
        };

        $runner = new ForkRunner();
        $args = [];
        for ($i = 0; $i < 10; $i++) {
            $args[] = [$i];
            $expected[] = $i * $i;
        }
        $start = microtime(true);
        $runner->run($func, $args);
        $this->assertLessThan(3, microtime(true) - $start, 'Process took loo long');
    }

    public function testResultsOrderIsUnchanged()
    {
        $func = function ($n) {
            return $n * $n;
        };

        $runner = new ForkRunner();
        $args = [];
        $expected = [];
        for ($i = 0; $i < 100; $i++) {
            $args[] = [$i];
            $expected[] = $i * $i;
        }
        $this->assertEquals($expected, $runner->run($func, $args));
    }
}
