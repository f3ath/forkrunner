<?php
namespace F3\ForkRunner;

use InvalidArgumentException;
use RuntimeException;

/**
 * Running a callback in parallel threads
 *
 * @package Base/PCNTL
 * @version $id$
 * @author Alexey Karapetov <karapetov@gmail.com>
 */
class ForkRunner
{
    /**
     * Run a callback in $threadsCount parrallel threads
     *
     * @param int       $threadsCount   Number of threads
     * @param callable  $callback       The code to run
     * @param array     $args           callback arguments
     * @return array (pid => status), status from pcntl_waitpid()
     */
    public function run($threadsCount, $callback, array $args = array())
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException(sprintf('%s is not callable', gettype($callback)));
        }
        $children = array();
        for ($thread = 0; $thread < $threadsCount; $thread++) {
            $pid = pcntl_fork();
            if (-1 == $pid) {
                throw new RuntimeException(sprintf('Unable to fork thread %d of %d', $thread + 1, $threadsCount));
            }
            if ($pid) { // parent
                $children[] = $pid;
                $this->onThreadStart($pid);
            } else { //  child
                break; 
            }
        }
        if ($pid) { // parent
            foreach ($children as $child) {
                pcntl_waitpid($child, $status);
                $this->onThreadExit($child, $status);
            }
        } else { // child
            call_user_func_array($callback, $args);
            die();
        }
    }

    /**
     * Hook on thread exit
     *
     * @param int $pid      Process ID
     * @param int $status   Exit status
     * @return void
     */
    protected function onThreadExit($pid, $status)
    {
    }

    /**
     * Hook on thread start
     *
     * @param int $pid Process ID
     * @return void
     */
    protected function onThreadStart($pid)
    {
    }
}
