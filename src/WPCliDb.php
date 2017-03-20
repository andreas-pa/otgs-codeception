<?php
namespace Codeception\Module;

use Codeception\Module as CodeceptionModule;
use Codeception\TestInterface;
use Helper\Scenario;

/*
 * WP-Cli Db module for CodeCeption
 * Uses wp-cli to import and reset the WordPress database
 *
 * Can be used in codeception.yml like that:
 *
 * WPCliDb:
       dump: codeception/_data/dump.sql
       cleandump: codeception/_data/cleandb.sql
       dump1: codeception/_data/germandefault.sql
       dump2: codeception/_data/german-default-no-english.sql
 */

class WPCliDb extends Cli
{

    public function _cleanup()
    {
        $this->runShellCommand("wp db reset --yes");

        if (isset($this->config['dump'])) {
            $this->runShellCommand("wp db import " . $this->config['dump']);
            $this->db_cleanup();
        } else {
            $this->fail("You need to define at least one dump");
        }
    }

    public function start_with_a_clean_site()
    {
        $this->runShellCommand("wp db reset --yes");

        if (isset($this->config['cleandump'])) {
            $this->runShellCommand("wp db import " . $this->config['cleandump']);
            $this->db_cleanup();
        } else {
            echo "cleandump not set, default database dump loaded instead.";
        }
    }

    public function start_with_dump1()
    {
        $this->runShellCommand("wp db reset --yes");

        if (isset($this->config['dump1'])) {
            $this->runShellCommand("wp db import " . $this->config['dump1']);
            $this->db_cleanup();
        } else {
            echo "Dump1 not set, default database dump loaded instead.";
        }
    }

    public function start_with_dump2()
    {
        $this->runShellCommand("wp db reset --yes");

        if (isset($this->config['dump2'])) {
            $this->runShellCommand("wp db import " . $this->config['dump2']);
            $this->db_cleanup();
        } else {
            echo "Dump2 not set, default database dump loaded instead.";
        }
    }
    
    public function import_dump($filename)
    {
        $this->runShellCommand("wp db reset --yes");

        if (file_exists($filename)) {
            $this->runShellCommand("wp db import " . codecept_data_dir() . $filename);
            $this->db_cleanup();
        } else {
            echo $filename." not found in _data folder, default database dump loaded instead.";
        }
    }

    private function db_cleanup()
    {
        $this->runShellCommand("wp transient delete-all");
        $this->runShellCommand("wp cache flush");
        $this->runShellCommand("wp core update-db");

        $oldURL = ( isset( $this->config['oldURL'] ) ) ? $this->config['oldURL'] : "http://wpbeta.dev";
        $newURL = ( isset( $this->config['newUrl'] ) ) ? $this->config['newUrl'] : $this->getModule('WPWebDriver')->_getUrl();

        if ($newURL != $oldURL) {
            $this->runShellCommand("wp search-replace " . $oldURL . " " . $newURL);
        }
    }
       
    public function set_checkpoint($checkpoint = "checkpoint.sql")
    {
        $this->runShellCommand("rm -f " . codecept_output_dir() . $checkpoint);
        $this->runShellCommand("wp db export " . codecept_output_dir() . $checkpoint);
    }

    public function rollback_to_checkpoint($checkpoint = "checkpoint.sql")
    {
        $this->runShellCommand("wp db reset --yes");
        $this->runShellCommand("wp db import " . codecept_output_dir() . $checkpoint);
    }

    public function _failed(TestInterface $test, $fail)
    {
        $test_name = $test->getMetadata()->getName();
        $this->runShellCommand("wp db export " . codecept_output_dir() . $test_name . "_db.sql");
    }
}
