<?php
require "vendor/autoload.php";
class SimpleTest extends PHPUnit\Framework\TestCase {
    public function test() {
        $this->assertTrue(
            class_exists("TwentyI\API\Authentication"),
            "Authentication: loads"
        );
        $this->assertTrue(
            class_exists("TwentyI\API\ControlPanel"),
            "ControlPanel: loads"
        );
        $this->assertTrue(
            class_exists("TwentyI\API\Services"),
            "Services: loads"
        );
    }
}
