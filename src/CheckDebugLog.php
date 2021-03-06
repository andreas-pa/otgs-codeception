<?php

namespace Codeception\Module;

use Codeception\Module;
use Codeception\Module\Asserts;
use Codeception\TestInterface;

class CheckDebugLog extends Module
{
    public function _initialize()
    {
        if (!isset($this->config['WPpath'])) {
            $this->config['WPpath'] = "";
        }
    }

    public function _before(TestInterface $step) {
        if ( file_exists($this->config['WPpath'] . "wp-content/debug.log" )) {
            unlink($this->config['WPpath'] . "wp-content/debug.log");
        }
    }

    public function _after(TestInterface $step)
    {
        if (file_exists($this->config['WPpath'] . "wp-content/debug.log")) {
            copy($this->config['WPpath'] . "wp-content/debug.log", codecept_output_dir() . "/wpdebug.log");
            unlink($this->config['WPpath'] . "wp-content/debug.log");

            $this->fail("Debug.log has been altered. Test failed.");
        }
    }
}
