<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2015/12/13
 * Time: 21:49
 */
defined('UNIT_TESTS_ROOT')||require 'bootstrap.php';

class ExceptionTest extends TestCase{
    public function testException(){
        $this->assertTrue(new \ManaPHP\Exception instanceof \Exception);
    }
}