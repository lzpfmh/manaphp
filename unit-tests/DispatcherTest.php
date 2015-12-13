<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2015/12/13
 * Time: 21:57
 */
defined('UNIT_TESTS_ROOT')||require 'bootstrap.php';

class tDispatcher extends \ManaPHP\Dispatcher{
    protected function _handleException($exception){

    }

    protected function _throwDispatchException($message, $exceptionCode=0){

    }

    public function getActionSuffix(){
        return $this->_actionSuffix;
    }

    public function getModuleName(){
        return $this->_moduleName;
    }

    public function getDefaultAction(){
        return $this->_defaultAction;
    }
}


class DispatcherTest extends TestCase{
    public function test_setActionSuffix(){
        $dispatcher =new tDispatcher();

        $dispatcher->setActionSuffix('');
        $this->assertEquals('',$dispatcher->getActionSuffix());

        $dispatcher->setActionSuffix('_ACTION');
        $this->assertEquals('_ACTION',$dispatcher->getActionSuffix());

        $this->assertInstanceOf('\ManaPHP\Dispatcher',$dispatcher->setActionSuffix(''));
        $this->assertInstanceOf('tDispatcher',$dispatcher->setActionSuffix(''));
    }

    public function test_setDefaultNamespace(){
        $dispatcher =new tDispatcher();

        $dispatcher->setDefaultNamespace('Application\Api');
        $this->assertEquals('Application\Api',$dispatcher->getDefaultNamespace());

        $this->assertInstanceOf('\ManaPHP\Dispatcher',$dispatcher->setDefaultNamespace('Application\Api'));
        $this->assertInstanceOf('tDispatcher',$dispatcher->setDefaultNamespace('Application\Api'));
    }

    public function test_setNamespaceName(){
        $dispatcher =new tDispatcher();

        $dispatcher->setNamespaceName('Application\Api');
        $this->assertEquals('Application\Api',$dispatcher->getNamespaceName());

        $this->assertInstanceOf('\ManaPHP\Dispatcher',$dispatcher->setNamespaceName('Application\Api'));
        $this->assertInstanceOf('tDispatcher',$dispatcher->setNamespaceName('Application\Api'));
    }
    public function test_setModuleName(){
        $dispatcher =new tDispatcher();

        $dispatcher->setModuleName('Api');
        $this->assertEquals('Api',$dispatcher->getModuleName());

        $this->assertInstanceOf('\ManaPHP\Dispatcher',$dispatcher->setModuleName('Api'));
        $this->assertInstanceOf('tDispatcher',$dispatcher->setModuleName('Api'));
    }

    public function test_setDefaultAction(){
        $dispatcher =new tDispatcher();

        $this->assertEquals('',$dispatcher->getDefaultAction());

        $dispatcher->setDefaultAction('index');
        $this->assertEquals('index',$dispatcher->getDefaultAction());

        $this->assertInstanceOf('\ManaPHP\Dispatcher',$dispatcher->setDefaultAction('index'));
        $this->assertInstanceOf('tDispatcher',$dispatcher->setDefaultAction('index'));
    }

    public function test_setActionName(){
        $dispatcher =new tDispatcher();

        $dispatcher->setActionName('index');
        $this->assertEquals('index',$dispatcher->getActionName());

        $this->assertInstanceOf('\ManaPHP\Dispatcher',$dispatcher->setActionName('index'));
        $this->assertInstanceOf('tDispatcher',$dispatcher->setActionName('index'));
    }

    public function test_getActionName(){
        $dispatcher =new tDispatcher();

        $dispatcher->setActionName('index');
        $this->assertEquals('index',$dispatcher->getActionName());
    }

    public function test_setParams(){
        $dispatcher =new tDispatcher();

        $params=['id'=>10,'name'=>'manaphp'];
        $this->assertEquals($params,$dispatcher->setParams($params)->getParams());

        $this->assertInstanceOf('\ManaPHP\Dispatcher',$dispatcher->setParams([]));
        $this->assertInstanceOf('tDispatcher',$dispatcher->setParams([]));
    }

    public function test_getParams(){
        $dispatcher =new tDispatcher();

        $this->assertEquals([],$dispatcher->getParams());

        $params=['id'=>10,'name'=>'manaphp'];
        $this->assertEquals($params,$dispatcher->setParams($params)->getParams());
    }

    public function test_setParam(){
        $dispatcher =new tDispatcher();

        $this->assertEquals(null,$dispatcher->getParam('name'));

        $dispatcher->setParam('name','manaphp');
        $this->assertEquals('manaphp',$dispatcher->getParam('name'));

        $this->assertInstanceOf('\ManaPHP\Dispatcher',$dispatcher->setParam('name','manaphp'));
        $this->assertInstanceOf('tDispatcher',$dispatcher->setParam('name','manaphp'));
    }

    public function test_getParam(){
        $dispatcher =new tDispatcher();

        $this->assertEquals(null,$dispatcher->getParam('name'));

        $dispatcher->setParam('name','manaphp');
        $this->assertEquals('manaphp',$dispatcher->getParam('name'));

        $this->assertEquals('default',$dispatcher->getParam('not_exist',null,'default'));
    }

    public function test_dispatcher(){
        $di=new ManaPHP\Di();
        $di->set('response',new ManaPHP\Http\Response());
        $dispatcher =new ManaPHP\Mvc\Dispatcher();
        $dispatcher->setDI($di);
        $this->assertInstanceOf('\ManaPHP\Di',$dispatcher->getDI());
    }
}