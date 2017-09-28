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
        $file = tempnam(sys_get_temp_dir(), 'php');
        file_put_contents($file, "<?php\n");
        $children = [];
        foreach ($argsCollection as $key => $args) {
            $pid = pcntl_fork();
            switch ($pid) {
                case -1:
                    throw new RuntimeException(sprintf('Unable to fork process %d of %d', $key, count($argsCollection)));
                case 0: // child
                    $this->runChild($callback, $args, $file);
                    die(0);
                default: //parent
                    $children[] = $pid;
            }
        }
        foreach ($children as $child) {
            pcntl_waitpid($child, $status);
        }
        $result = [];
        require $file;
        unlink($file);
        return $result;
    }

    /**
     * @param callable $callback
     * @param array $arguments
     * @param $file
     */
    private function runChild(callable $callback, array $arguments, $file)
    {
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
    }
}
