<?php
namespace F3\ForkRunner;

class ForkRunnerTest extends \PHPUnit_Framework_TestCase
{
    public function testFunctional()
    {
        $output = shell_exec('php '.__DIR__.'/functional.php');
        var_dump($output);
        $result = unserialize($output);
        var_dump($result);
        $this->assertTrue(is_array($result));
        foreach ($result as $key => $value) {
            $this->assertEquals($key * $key, $value);
        }
    }
}
