<?php
namespace F3\ForkRunner;

class ForkRunnerTest extends \PHPUnit_Framework_TestCase
{
    public function testExampleFromTheDocs()
    {
        $func = function ($n) {
            return $n * $n;
        };
        $runner = new ForkRunner();
        $args = [[3], [4], [5]];
        $result = $runner->run($func, $args); // [9, 16, 25]
        $this->assertEquals([9, 16, 25], $result);
    }

    /**
     * @param Collector $collector
     * @dataProvider collectorsProvider
     */
    public function testParallelExecutionIsFasterThanSerial(Collector $collector)
    {
        $this->skipIfNotSupported($collector);
        $func = function ($n) {
            sleep(1);
            return $n * $n;
        };

        $expected = [];
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

    /**
     * @dataProvider collectorsProvider
     * @param Collector $collector
     */
    public function testResultsOrderIsUnchanged(Collector $collector)
    {
        $this->skipIfNotSupported($collector);
        $func = function ($n) {
            return $n * $n;
        };

        $runner = new ForkRunner($collector);
        $args = [];
        $expected = [];
        for ($i = 0; $i < 100; $i++) {
            $args[] = [$i];
            $expected[] = $i * $i;
        }
        $this->assertEquals($expected, $runner->run($func, $args));
    }

    /**
     * @return Collector[][]
     */
    public function collectorsProvider()
    {
        return [
            [new FileCollector()],
            [new MemoryCollector()],
        ];
    }

    /**
     * @param Collector $collector
     */
    private function skipIfNotSupported(Collector $collector)
    {
        if (!$collector->isSupported()) {
            $this->markTestSkipped(sprintf('Collector %s is not supported', get_class($collector)));
        }
    }
}
