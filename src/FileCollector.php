<?php
namespace F3\ForkRunner;

class FileCollector implements Collector
{
    /**
     * @var string
     */
    private $file;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->file = tempnam(sys_get_temp_dir(), 'php');
        file_put_contents($this->file, "<?php\n");
    }

    /**
     * Value MUST survive var_export()
     * @inheritdoc
     */
    public function setValue($key, $val)
    {
        file_put_contents(
            $this->file,
            sprintf("\$values[%s] = %s;\n", var_export($key, true), var_export($val, true)),
            FILE_APPEND
        );
    }

    /**
     * @inheritdoc
     */
    public function getValues(array $keys)
    {
        $values = [];
        require $this->file;
        unlink($this->file);
        return $values;
    }

    /**
     * @inheritdoc
     */
    public function isSupported()
    {
        return true;
    }
}
