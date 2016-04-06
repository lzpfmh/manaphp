<?php
defined('UNIT_TESTS_ROOT') || require __DIR__.'/bootstrap.php';

class MvcApplicationTest extends TestCase
{

    public function test_construct()
    {
        $application = new \ManaPHP\Mvc\Application('D:\Test\Application');
        $properties = $application->__debugInfo();
        $this->assertEquals('D:/Test/Application', $properties['_rootDirectory']);
        $this->assertEquals('Application', $properties['_rootNamespace']);

        $application = new \ManaPHP\Mvc\Application('d:\test\application\\');
        $properties = $application->__debugInfo();
        $this->assertEquals('d:/test/application', $properties['_rootDirectory']);
        $this->assertEquals('Application', $properties['_rootNamespace']);
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
}