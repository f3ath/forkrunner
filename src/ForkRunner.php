<?php
namespace F3\ForkRunner;

/**
 * Running a callback in parallel processes
 */
class ForkRunner
{
    /**
     * @var Collector
     */
    private $collector;

    public function __construct(Collector $collector = null)
    {
        $this->collector = $collector ?: new FileCollector();
    }

    /**
     * Run a callback in parallel processes
     *
     * @param callable $callback Callback to run
     * @param array[] $argsCollection Array of arguments, two-dimensional
     * @return array array of results
     */
    public function run(callable $callback, array $argsCollection)
    {
        $this->collector->init();
        $children = [];
        foreach ($argsCollection as $key => $args) {
            $pid = pcntl_fork();
            switch ($pid) {
                case -1:
                    throw new \RuntimeException(sprintf('Unable to fork process %d of %d', $key,
                        count($argsCollection)));
                case 0: // child
                    $this->collector->setValue($key, call_user_func_array($callback, $args));
                    exit(0);
                default: //parent
                    $children[] = $pid;
            }
        }
        foreach ($children as $child) {
            pcntl_waitpid($child, $status);
            while (!pcntl_wifexited($status)) {
                usleep(100);
            }
        }
        return $this->collector->getValues(array_keys($argsCollection));
    }
}
