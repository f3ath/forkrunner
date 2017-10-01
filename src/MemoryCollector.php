<?php
/**
 * Aleksandr Kozhevnikov <iamdevice@gmail.com>
 * Date: 30.09.2017 22:29
 */

namespace F3\ForkRunner;

class MemoryCollector implements Collector
{
    const KEY = 0;
    /** @var int $pointer */
    private $pointer;

    private $semaphore;

    public function init()
    {
        $this->pointer = ftok(__FILE__, chr(rand(0, 255)));
        $this->semaphore = sem_get($this->pointer);
        sem_acquire($this->semaphore, 10);
    }

    public function setValue($key, $val)
    {
        $saved = false;

        while (!$saved) {
            $saved = $this->saveIntoMemory($key, $val);
        }
    }

    public function getValues()
    {
        sem_remove($this->semaphore);
        $memory = shm_attach($this->pointer);
        $result = shm_get_var($memory, self::KEY);
        shm_remove($memory);

        return $result;
    }

    private function saveIntoMemory($key, $val)
    {
        $memory = shm_attach($this->pointer);
        if (shm_has_var($memory, self::KEY)) {
            $values = shm_get_var($memory, self::KEY);
            $values = is_scalar($values) ? [ $values ] : $values;
        }
        $values[$key] = $val;
        $result = shm_put_var($memory, self::KEY, $values);
        shm_detach($memory);

        return $result;
    }
}
