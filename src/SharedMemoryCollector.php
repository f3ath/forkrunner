<?php
namespace F3\ForkRunner;

class SharedMemoryCollector implements Collector
{
    private $pointerId;
    private $bufferSize;

    /**
     * SharedMemoryCollector constructor.
     * @param int $bufferSize
     */
    public function __construct($bufferSize)
    {
        $this->bufferSize = $bufferSize;
    }


    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->pointerId = ftok(__FILE__, 'f');

    }

    /**
     * Value MUST survive var_export()
     * @inheritdoc
     */
    public function setValue($key, $val)
    {
        $memory = $this->getMemResource();
        shm_put_var($memory, $key, $val);
        shm_detach($memory);
    }

    /**
     * @inheritdoc
     */
    public function getValues(array $keys)
    {
        $result = [];
        $memory = $this->getMemResource();
        foreach ($keys as $key) {
            $result[$key] = shm_get_var($memory, $key);
        }
        shm_detach($memory);
        return $result;
    }

    /**
     * @return resource
     */
    private function getMemResource()
    {
        return shm_attach($this->pointerId, $this->bufferSize);
    }

    /**
     * @inheritdoc
     */
    public function isSupported()
    {
        return function_exists('shm_attach');
    }
}
