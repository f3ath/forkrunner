<?php
/**
 * Aleksandr Kozhevnikov <iamdevice@gmail.com>
 * Date: 30.09.2017 22:29
 */

namespace F3\ForkRunner;

class MemoryCollector implements Collector
{
    /** @var int $pointer */
    private $pointer;
    private $semaphore;

    public function init()
    {
        $this->pointer = ftok(__FILE__, chr(rand(0, 255)));
        $this->semaphore = sem_get($this->pointer, 10);
        sem_acquire($this->semaphore);
    }

    public function setValue($key, $val)
    {
        $memory = shm_attach($this->pointer);
        while (false === shm_has_var($memory, $key)) {
            shm_put_var($memory, $key, $val);
        }
        shm_detach($memory);
    }

    public function getValues(array $keys)
    {
        sem_remove($this->semaphore);
        $memory = shm_attach($this->pointer);
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = shm_get_var($memory, $key);
        }
        shm_remove($memory);

        return $result;
    }

    public function isSupported()
    {
        return extension_loaded('sysvsem') && extension_loaded('sysvshm');
    }
}
