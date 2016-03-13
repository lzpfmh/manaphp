<?php
defined('UNIT_TESTS_ROOT') || require 'bootstrap.php';

class MvcApplicationTest extends TestCase
{

    public function test_construct()
    {
        $application = new \ManaPHP\Mvc\Application('D:\Test\Application');
        $properties = $application->__debugInfo();
        $this->assertEquals('D:/Test/Application', $properties['_baseDirectory']);
        $this->assertEquals('Application', $properties['_baseNamespace']);

        $application = new \ManaPHP\Mvc\Application('d:\test\application\\');
        $properties = $application->__debugInfo();
        $this->assertEquals('d:/test/application', $properties['_baseDirectory']);
        $this->assertEquals('Application', $properties['_baseNamespace']);
    }

    public function test_useImplicitView()
    {
        $application = new \ManaPHP\Mvc\Application('D:\Test\Application');
        $properties = $application->__debugInfo();
        $this->assertEquals(true, $properties['_implicitView']);

        $this->assertInstanceOf('ManaPHP\Mvc\Application', $application->useImplicitView(false));
        $properties = $application->__debugInfo();
        $this->assertEquals(false, $properties['_implicitView']);
    }

    public function test_registerModules()
    {
        $application = new \ManaPHP\Mvc\Application('D:\Test\Application');
        $this->assertInstanceOf('ManaPHP\Mvc\Application', $application->registerModules());
        $properties = $application->__debugInfo();
        $this->assertEquals(['' => 'Application\Module'], $properties['_modules']);
        $this->assertEquals('', $properties['_defaultModule']);


        $application = new \ManaPHP\Mvc\Application('D:\Test\Application');
        $this->assertInstanceOf('ManaPHP\Mvc\Application', $application->registerModules(null));
        $properties = $application->__debugInfo();
        $this->assertEquals(['' => 'Application\Module'], $properties['_modules']);
        $this->assertEquals('', $properties['_defaultModule']);

        $application = new \ManaPHP\Mvc\Application('D:\Test\Application');
        $application->registerModules(['m1', 'm2']);
        $properties = $application->__debugInfo();
        $this->assertEquals('M1', $properties['_defaultModule']);
        $this->assertEquals(['M1' => 'Application\M1\Module', 'M2' => 'Application\M2\Module'],
          $properties['_modules']);
    }
}