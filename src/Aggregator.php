<?php
namespace F3\ForkRunner;

interface Aggregator
{
    public function init();

    /**
     * @param mixed $result
     */
    public function addValue($result);

    /**
     * @return array
     */
    public function getValues();
}
