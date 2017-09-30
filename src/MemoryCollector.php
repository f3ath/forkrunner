<?php
/**
 * Aleksandr Kozhevnikov <iamdevice@gmail.com>
 * Date: 30.09.2017 22:29
 */

namespace F3\ForkRunner;

class MemoryCollector implements Collector
{
    /** @var array $keys */
    private $keys = [];
    /** @var int $pointer */
    private $pointer;

    public function init()
    {
        $project = chr(rand(0, 255));
        $this->pointer = ftok(__FILE__, $project);
    }

    public function setValue($key, $val)
    {
        $memory = shm_attach($this->pointer);
        shm_put_var($memory, $key, $val);
        shm_detach($memory);
    }

    public function getValues()
    {
        $result = [];
        $memory = shm_attach($this->pointer);
        foreach ($this->keys as $key) {
            $result[$key] = shm_get_var($memory, $key);
            shm_remove_var($memory, $key);
        }
        shm_remove($memory);
    }
}
