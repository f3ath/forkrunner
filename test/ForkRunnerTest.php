<?php

namespace F3\ForkRunner;

class ForkRunnerTest extends \PHPUnit_Framework_TestCase
{
    public function testFunctional()
    {
        $func = function ($n) {
            sleep(1);
            return $n * $n;
        };

        $runner = new ForkRunner();
        for ($i = 0; $i < 10; $i++) {
            $args[] = [$i];
            $expected[] = $i * $i;
        }
        $start = microtime(true);
        $result = $runner->run($func, $args);
        $this->assertLessThan(2, microtime(true) - $start, 'Process took loo long');
        $values = array_values($result);
        sort($values);
        $this->assertEquals($expected, $values);
    }
}
