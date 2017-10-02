<?php
namespace F3\ForkRunner;

class SharedMemoryCollector implements Collector
{
    private $pointerId;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->pointerId = \ftok(__FILE__, 'f');

    }

    /**
     * Value MUST survive var_export()
     * @inheritdoc
     */
    public function setValue($key, $val)
    {
        $memory = \shm_attach($this->pointerId, 1024);
        \shm_put_var($memory, $key, $val);
        \shm_detach($memory);
    }

    /**
     * @inheritdoc
     */
    public function getValues(array $keys)
    {
        $result = [];
        $memory = \shm_attach($this->pointerId, 1024);
        foreach ($keys as $key) {
            $result[$key] = \shm_get_var($memory, $key);
        }
        \shm_detach($memory);

        return $result;
    }
}
