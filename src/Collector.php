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
     * Add value. Should be called from an "executor" process
     * @param mixed $val
     */
    public function addValue($val);

    /**
     * Get collected values. Should be called from the main process;
     * @return array
     */
    public function getValues();
}
