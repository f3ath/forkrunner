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
     * @param array[] $arguments Array of arguments, two-dimensional
     * @return array (pid => callback result)
     */
    public function run(callable $callback, array $arguments)
    {
        if (empty($arguments)) {
            throw new \InvalidArgumentException('No arguments')
        }
        $file = tempnam(sys_get_temp_dir(), 'php');
        file_put_contents($file, "<?php\n", FILE_APPEND);
        $children = array();
        foreach ($arguments as $key => $arg) {
            $pid = pcntl_fork();
            if (-1 === $pid) {
                throw new RuntimeException(sprintf('Unable to fork thread %d of %d', $key, count($arguments)));
            }
            if ($pid) { // parent
                $children[] = $pid;
            } else { //  child
                break; 
            }
        }
        if ($pid) { // parent
            foreach ($children as $child) {
                pcntl_waitpid($child, $status);
            }
            $result = null;
            require $file;
            unlink($file);
            return $result;
        }
        // the rest happens is the child process
        file_put_contents(
            $file,
            sprintf(
                "\$result[%s] = %s;\n", 
                getmypid(), 
                var_export(
                    call_user_func_array($callback, $arguments), 
                    true
                )
            ),
            FILE_APPEND
        );
        die(0);
    }
}
