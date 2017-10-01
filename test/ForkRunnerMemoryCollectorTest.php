<?php
/**
 * Aleksandr Kozhevnikov <iamdevice@gmail.com>
 * Date: 30.09.2017 22:37
 */

namespace F3\ForkRunner;

use PHPUnit_Framework_TestCase;

class ForkRunnerMemoryCollectorTest extends PHPUnit_Framework_TestCase
{
    public function testParallelExecutionIsFasterThanSerial()
    {
        $func = function ($n) {
            sleep(1);
            return $n * $n;
        };

        $collector = new MemoryCollector();
        $runner = new ForkRunner($collector);
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

        $collector = new MemoryCollector();
        $runner = new ForkRunner($collector);
        $args = [];
        $expected = [];
        for ($i = 0; $i < 100; $i++) {
            $args[] = [$i];
            $expected[] = $i * $i;
        }
        $this->assertEquals($expected, $runner->run($func, $args));
    }

    public function testExampleFromTheDocs()
    {
        $func = function ($n) {
            return $n * $n;
        };

        $collector = new MemoryCollector();
        $runner = new ForkRunner($collector);

        $args = [[3], [4], [5]];
        $result = $runner->run($func, $args); // [9, 16, 25]
        $this->assertEquals([9, 16, 25], $result);
    }
}
