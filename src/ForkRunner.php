<?php
namespace F3\ForkRunner;

use RuntimeException;

/**
 * Running a callback in parallel processes
 */
class ForkRunner
{
    private $aggregator;

    public function __construct()
    {
        $this->aggregator = new Aggregator();
    }

    /**
     * Run a callback in $threadsCount parallel processes
     *
     * @param callable $callback Callback to run
     * @param array[] $argsCollection Array of arguments, two-dimensional
     * @return array (pid => callback result)
     */
    public function run(callable $callback, array $argsCollection)
    {
        $this->aggregator->prepare();
        $children = [];
        foreach ($argsCollection as $key => $args) {
            $pid = pcntl_fork();
            switch ($pid) {
                case -1:
                    throw new RuntimeException(sprintf('Unable to fork process %d of %d', $key, count($argsCollection)));
                case 0: // child
                    $this->aggregator->processResult(call_user_func_array($callback, $args));
                    die(0);
                default: //parent
                    $children[] = $pid;
            }
        }
        foreach ($children as $child) {
            pcntl_waitpid($child, $status);
        }
        return $this->aggregator->getAggregatedResult();
    }
}
