<?php
/**
 * Aleksandr Kozhevnikov <iamdevice@gmail.com>
 * Date: 30.09.2017 22:29
 */

namespace F3\ForkRunner;

use RuntimeException;

class MemoryCollector implements Collector
{
    const KEY = 0;
    const LOCK_ID = -1;
    /** @var int $pointer */
    private $pointer;
    /** @var int $locker */
    private $locker;
    private $semaphore;
    /** @var string $keyFile */
    private $keyFile;

    public function init()
    {
        $this->pointer = ftok(__FILE__, chr(rand(0, 255)));
        $this->locker = rand(0, 10000);
        $this->semaphore = sem_get($this->pointer, 10);
        sem_acquire($this->semaphore);
        $this->keyFile = tempnam(sys_get_temp_dir(), 'lock');
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
