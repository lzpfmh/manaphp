<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2015/12/13
 * Time: 21:57
 */
defined('UNIT_TESTS_ROOT')||require 'bootstrap.php';

class Test1Controller extends \ManaPHP\Mvc\Controller{

}

class Test2Controller extends \ManaPHP\Mvc\Controller
{
    public function indexAction()
    {
    }

    public function otherAction()
    {

    }

    public function anotherAction()
    {
        return 100;
    }

    public function another2Action($a, $b)
    {
        return $a+$b;
    }

    public function another3Action()
    {
        return $this->dispatcher->forward(
            array(
                'controller' => 'test2',
                'action' => 'another4'
            )
        );
    }

    public function another4Action()
    {
        return 120;
    }

    public function another5Action()
    {
        return $this->dispatcher->getParam('param1')+$this->dispatcher->getParam('param2');
    }

}

class Test4Controller extends \ManaPHp\Mvc\Controller
{
    public function requestAction()
    {
        return $this->request->getPost('email', 'email');
    }

    public function viewAction()
    {
        return $this->view->setParamToView('born', 'this');
    }
}

class ControllerBase extends \ManaPHP\Mvc\Controller
{
    public function serviceAction()
    {
        return 'hello';
    }

}
class Test5Controller extends ManaPHP\Mvc\Controller
{
    public function notFoundAction()
    {
        return 'not-found';
    }

}


class Test6Controller extends ManaPHP\Mvc\Controller
{


}

class Test7Controller extends ControllerBase
{

}

class Test8Controller extends ManaPHP\Mvc\Controller
{
    public function buggyAction()
    {
        throw new Exception('This is an uncaught exception');
    }

}
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

    public function test_dispatch(){
        $di=new ManaPHP\Di();
        $di->set('response',new ManaPHP\Http\Response());

        $dispatcher =new ManaPHP\Mvc\Dispatcher();
        $dispatcher->setDI($di);
        $this->assertInstanceOf('\ManaPHP\Di',$dispatcher->getDI());
        $di->set('dispatcher',$dispatcher);

        //camelize the handler class:not require
        $dispatcher->setControllerName('Index');
        $dispatcher->setActionName('index');
        $dispatcher->setParams([]);

        try{
            $dispatcher->dispatch();
            $this->assertTrue(false,'oh, why?');
        }catch (\Manaphp\Exception $e){
            $this->assertEquals('IndexController handler class cannot be loaded',$e->getMessage());
            $this->assertInstanceOf('ManaPHP\Mvc\Dispatcher\Exception',$e);
        }

        //camelize the handler class: require,only one word
        $dispatcher->setControllerName('missing');
        $dispatcher->setActionName('index');
        $dispatcher->setParams([]);

        try{
            $dispatcher->dispatch();
            $this->assertTrue(false,'oh, why?');
        }catch (\Manaphp\Exception $e){
            $this->assertEquals('MissingController handler class cannot be loaded',$e->getMessage());
            $this->assertInstanceOf('ManaPHP\Mvc\Dispatcher\Exception',$e);
        }

        //camelize the handler class: require,multiple words
        $dispatcher->setControllerName('test_home');
        $dispatcher->setActionName('index');
        $dispatcher->setParams([]);

        try {
            $dispatcher->dispatch();
            $this->assertTrue(FALSE, 'oh, Why?');
        } catch(\Manaphp\Exception $e) {
            $this->assertEquals('TestHomeController handler class cannot be loaded',$e->getMessage());
            $this->assertInstanceOf('ManaPHP\Mvc\Dispatcher\Exception', $e);
        }

        //action determination
        $dispatcher->setControllerName('test1');
        $dispatcher->setActionName('index');
        $dispatcher->setParams([]);

        try {
            $dispatcher->dispatch();
            $this->assertTrue(FALSE, 'oh, Why?');
        } catch (\Manaphp\Exception $e) {
            $this->assertEquals("Action 'index' was not found on handler 'Test1Controller'",$e->getMessage());
        }

        //normal usage without return value
        $dispatcher->setControllerName('test2');
        $dispatcher->setActionName('other');
        $dispatcher->setParams([]);
        $controller = $dispatcher->dispatch();
        $this->assertInstanceOf('Test2Controller', $controller);
        $this->assertEquals('other',$dispatcher->getActionName());
        $this->assertEquals(null,$dispatcher->getReturnedValue());

        //normal usage with return value
        $dispatcher->setControllerName('test2');
        $dispatcher->setActionName('another');
        $dispatcher->setParams([]);
        $dispatcher->dispatch();
        $this->assertEquals(100,$dispatcher->getReturnedValue());

        //bind param to method parameter
        $dispatcher->setControllerName('test2');
        $dispatcher->setActionName('another2');
        $dispatcher->setParams([2, '3']);
        $dispatcher->dispatch();
        $this->assertEquals(5,$dispatcher->getReturnedValue());

        //forward
        $dispatcher->setControllerName('test2');
        $dispatcher->setActionName('another3');
        $dispatcher->setParams([]);
        $dispatcher->dispatch();
        $this->assertEquals('another4',$dispatcher->getActionName());
        $this->assertEquals(120,$dispatcher->getReturnedValue());
        $this->assertTrue($dispatcher->wasForwarded());

        //fetch param from dispatcher
        $dispatcher->setControllerName('test2');
        $dispatcher->setActionName('another5');
        $dispatcher->setParams(['param1' => 2, 'param2' => 3]);
        $dispatcher->dispatch();
        $this->assertEquals(5,$dispatcher->getReturnedValue());

        //inherit class
        $dispatcher->setControllerName('test7');
        $dispatcher->setActionName('service');
        $dispatcher->setParams([]);
        $dispatcher->dispatch();
        $this->assertEquals('hello',$dispatcher->getReturnedValue());

        $this->assertEquals('test7',$dispatcher->getControllerName());
    }

    public function test_getReturnedValue(){
        $di=new ManaPHP\Di();
        $di->set('response',new ManaPHP\Http\Response());

        $dispatcher =new ManaPHP\Mvc\Dispatcher();
        $dispatcher->setDI($di);
        $this->assertInstanceOf('\ManaPHP\Di',$dispatcher->getDI());
        $di->set('dispatcher',$dispatcher);

        $dispatcher->setControllerName('test2');
        $dispatcher->setActionName('another5');
        $dispatcher->setParams(['param1' => 2, 'param2' => 3]);
        $dispatcher->dispatch();
        $this->assertEquals(5,$dispatcher->getReturnedValue());
    }
    public function test_forward(){
        $di=new ManaPHP\Di();
        $di->set('response',new ManaPHP\Http\Response());

        $dispatcher =new ManaPHP\Mvc\Dispatcher();
        $dispatcher->setDI($di);
        $this->assertInstanceOf('\ManaPHP\Di',$dispatcher->getDI());
        $di->set('dispatcher',$dispatcher);

        $dispatcher->setControllerName('test2');
        $dispatcher->setActionName('another3');
        $dispatcher->setParams([]);
        $dispatcher->dispatch();
        $this->assertEquals('Test2Controller',$dispatcher->getPreviousControllerClass());
        $this->assertEquals('test2',$dispatcher->getPreviousControllerName());
        $this->assertEquals('another3',$dispatcher->getPreviousActionName());
        $this->assertEquals('Test2Controller',$dispatcher->getControllerClass());
        $this->assertEquals('test2',$dispatcher->getControllerName());
        $this->assertEquals('another4',$dispatcher->getActionName());

        $dispatcher->setControllerName('test2');
        $dispatcher->setActionName('index');
        $dispatcher->setParams([]);

        $dispatcher->forward(['controller'=>'test3','action'=>'other']);
        $this->assertEquals('Test3Controller',$dispatcher->getControllerClass());
        $this->assertEquals('test3',$dispatcher->getControllerName());
        $this->assertEquals('other',$dispatcher->getActionName());

        $this->assertEquals('Test2Controller',$dispatcher->getPreviousControllerClass());
        $this->assertEquals('test2',$dispatcher->getPreviousControllerName());
        $this->assertEquals('index',$dispatcher->getPreviousActionName());
    }

    public function test_wasForwarded(){
        $di=new ManaPHP\Di();
        $di->set('response',new ManaPHP\Http\Response());

        $dispatcher =new ManaPHP\Mvc\Dispatcher();
        $dispatcher->setDI($di);
        $this->assertInstanceOf('\ManaPHP\Di',$dispatcher->getDI());
        $di->set('dispatcher',$dispatcher);

        $dispatcher->setControllerName('test2');
        $dispatcher->setActionName('another3');
        $dispatcher->setParams([]);
        $dispatcher->dispatch();
        $this->assertTrue($dispatcher->wasForwarded());
    }

}