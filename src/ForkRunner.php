<?php
namespace F3\ForkRunner;

use RuntimeException;

/**
 * Running a callback in parallel processes
 */
class ForkRunner
{
    /**
     * Run a callback in $threadsCount parallel processes
     *
     * @param callable $callback Callback to run
     * @param array[] $argsCollection Array of arguments, two-dimensional
     * @return array (pid => callback result)
     */
    public function run(callable $callback, array $argsCollection)
    {
        $pointerId = ftok(__FILE__, 'f');
        $children = [];
        $result = [];
        $keys = [];
        foreach ($argsCollection as $key => $args) {
            $memory = shm_attach($pointerId, 1024);
            $pid = pcntl_fork();
            $keys[] = $key;
            switch ($pid) {
                case -1:
                    throw new RuntimeException(sprintf('Unable to fork process %d of %d', $key, count($argsCollection)));
                case 0: // child
                    $memory = shm_attach($pointerId, 1024);
                    shm_put_var($memory, $key, call_user_func_array($callback, $args));
                    shm_detach($memory);
                    die(0);
                default: //parent
                    $children[] = $pid;
            }
            shm_detach($memory);
        }

        foreach ($children as $child) {
            pcntl_waitpid($child, $status);
        }

        foreach ($keys as $key) {
            $memory = shm_attach($pointerId, 1024);
            array_push($result, shm_get_var($memory, $key));
            shm_remove_var($memory, $key);
            shm_detach($memory);
        }

        return $result;
    }
}
