<?php
namespace F3\ForkRunner;

class Aggregator
{
    public function prepare()
    {
        $this->file = tempnam(sys_get_temp_dir(), 'php');
        file_put_contents($this->file, "<?php\n");
    }

    /**
     * @param mixed $result
     */
    public function processResult($result)
    {
        file_put_contents(
            $this->file,
            sprintf(
                "\$result[%s] = %s;\n",
                getmypid(),
                var_export(
                    $result,
                    true
                )
            ),
            FILE_APPEND
        );
    }

    /**
     * @return array
     */
    public function getAggregatedResult()
    {
        $result = [];
        require $this->file;
        unlink($this->file);
        return $result;
    }
}
