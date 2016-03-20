<?php

defined('UNIT_TESTS_ROOT') || require 'bootstrap.php';

class LoggerTest extends TestCase{
    public function test_setLevel(){
        $logger=new ManaPHP\Logger();

        // To confirm the default level is LEVEL_ALL
        $this->assertEquals(ManaPHP\Logger::LEVEL_ALL,$logger->getLevel());

        $memory=new ManaPHP\Logger\Adapter\Memory();
        $logger->addAdapter($memory);
        $logger->debug('**debug**');

        // To confirm the debug message correctly
        $this->assertCount(1,$memory->getLogs());
        list($level,$message,$context)=$memory->getLogs()[0];
        $this->assertEquals(ManaPHP\Logger::LEVEL_DEBUG,$level);
        $this->assertContains('**debug**',$message);
        $this->assertEquals(null,$context);

        // To confirm the level can set correctly
        $logger=new ManaPHP\Logger();
        $logger->setLevel(ManaPHP\Logger::LEVEL_OFF);
        $this->assertEquals(ManaPHP\Logger::LEVEL_OFF,$logger->getLevel());

        $memory=new ManaPHP\Logger\Adapter\Memory();
        $logger->addAdapter($memory);
        $logger->debug('**debug**');

        // To confirm  when LEVEL is higher than log level, the log was ignored correctly
        $this->assertCount(0,$memory->getLogs());
    }

    public function test_getLevel(){
        $logger=new ManaPHP\Logger();
        $logger->setLevel(ManaPHP\Logger::LEVEL_INFO);
        $this->assertEquals(ManaPHP\Logger::LEVEL_INFO,$logger->getLevel());
    }

    public function test_debug(){
        $logger=new ManaPHP\Logger();

        $memory=new ManaPHP\Logger\Adapter\Memory();
        $logger->addAdapter($memory);
        $logger->debug('**debug**');

        // To confirm the debug message correctly
        $this->assertCount(1,$memory->getLogs());
        list($level,$message,$context)=$memory->getLogs()[0];
        $this->assertEquals(ManaPHP\Logger::LEVEL_DEBUG,$level);
        $this->assertContains('**debug**',$message);
        $this->assertEquals(null,$context);
    }

    public function test_info(){
        $logger=new ManaPHP\Logger();

        $memory=new ManaPHP\Logger\Adapter\Memory();
        $logger->addAdapter($memory);
        $logger->info('**info**');

        // To confirm the debug message correctly
        $this->assertCount(1,$memory->getLogs());
        list($level,$message,$context)=$memory->getLogs()[0];
        $this->assertEquals(ManaPHP\Logger::LEVEL_INFO,$level);
        $this->assertContains('**info**',$message);
        $this->assertEquals(null,$context);
    }

    public function test_warning(){
        $logger=new ManaPHP\Logger();

        $memory=new ManaPHP\Logger\Adapter\Memory();
        $logger->addAdapter($memory);
        $logger->warning('**warning**');

        // To confirm the debug message correctly
        $this->assertCount(1,$memory->getLogs());
        list($level,$message,$context)=$memory->getLogs()[0];
        $this->assertEquals(ManaPHP\Logger::LEVEL_WARNING,$level);
        $this->assertContains('**warning**',$message);
        $this->assertEquals(null,$context);
    }

    public function test_error(){
        $logger=new ManaPHP\Logger();

        $memory=new ManaPHP\Logger\Adapter\Memory();
        $logger->addAdapter($memory);
        $logger->error('**error**');

        // To confirm the debug message correctly
        $this->assertCount(1,$memory->getLogs());
        list($level,$message,$context)=$memory->getLogs()[0];
        $this->assertEquals(ManaPHP\Logger::LEVEL_ERROR,$level);
        $this->assertContains('**error**',$message);
        $this->assertEquals(null,$context);
    }

    public function test_fatal(){
        $logger=new ManaPHP\Logger();

        $memory=new ManaPHP\Logger\Adapter\Memory();
        $logger->addAdapter($memory);
        $logger->fatal('**fatal**');

        // To confirm the debug message correctly
        $this->assertCount(1,$memory->getLogs());
        list($level,$message,$context)=$memory->getLogs()[0];
        $this->assertEquals(ManaPHP\Logger::LEVEL_FATAL,$level);
        $this->assertContains('**fatal**',$message);
        $this->assertEquals(null,$context);
    }
}