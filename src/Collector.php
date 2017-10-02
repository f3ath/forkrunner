<?php
namespace F3\ForkRunner;

/**
 * Collector collects values produced in parallel processes.
 *
 * Collector's lifecycle:
 * 0. init(); // main process
 * 1. addValue(); // parallel "executor" processes
 * 2. getValues(); // main process
 */
interface Collector
{
    /**
     * Initialize collector. Should be called from the main process.
     * @return void
     */
    public function init();

    /**
     * Set value. Should be called from an "executor" process
     * @param string|int $key
     * @param mixed $val
     * @return
     */
    public function setValue($key, $val);

    /**
     * Get collected values. Should be called from the main process;
     * @param array $keys
     * @return array
     */
    public function getValues(array $keys);
}
