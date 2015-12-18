<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2015/12/18
 * Time: 22:07
 */

defined('UNIT_TESTS_ROOT')||require 'bootstrap.php';


class HttpRequestTest extends TestCase{
    public function test_get(){
        $request =new \ManaPHP\Http\Request();

        $this->assertEquals(null,$request->get('name'));

        $this->assertEquals('test',$request->get('name',null,'test'));

        try{
            $this->assertEquals('test',$request->get('name','int'));
            $this->assertTrue(false,'why?');
        }catch (\Exception $e){
            $this->assertInstanceOf('ManaPHP\Http\Request\Exception',$e);
        }

        $_REQUEST['name']='mana';
        $this->assertEquals('mana',$request->get('name'));
    }

    public function test_getGet(){
        $request =new \ManaPHP\Http\Request();

        $this->assertEquals(null,$request->getGet('name'));

        $this->assertEquals('test',$request->getGet('name',null,'test'));

        try{
            $this->assertEquals('test',$request->getGet('name','int'));
            $this->assertTrue(false,'why?');
        }catch (\Exception $e){
            $this->assertInstanceOf('ManaPHP\Http\Request\Exception',$e);
        }

        $_GET['name']='mana';
        $this->assertEquals('mana',$request->getGet('name'));
    }

    public function test_getPost(){
        $request =new \ManaPHP\Http\Request();

        $this->assertEquals(null,$request->getPost('name'));

        $this->assertEquals('test',$request->getPost('name',null,'test'));

        try{
            $this->assertEquals('test',$request->getPost('name','int'));
            $this->assertTrue(false,'why?');
        }catch (\Exception $e){
            $this->assertInstanceOf('ManaPHP\Http\Request\Exception',$e);
        }

        $_POST['name']='mana';
        $this->assertEquals('mana',$request->getPost('name'));
    }

    public function test_getPut(){
        $request =new \ManaPHP\Http\Request();

        $_SERVER['REQUEST_METHOD']='PUT';
        $this->assertEquals(null,$request->getPut('name'));

        $this->assertEquals('test',$request->getPut('name',null,'test'));

        try{
            $this->assertEquals('test',$request->getPut('name','int'));
            $this->assertTrue(false,'why?');
        }catch (\Exception $e){
            $this->assertInstanceOf('ManaPHP\Http\Request\Exception',$e);
        }
    }

    public function test_getQuery(){
        $request =new \ManaPHP\Http\Request();

        $this->assertEquals(null,$request->getQuery('name'));

        $this->assertEquals('test',$request->getQuery('name',null,'test'));

        try{
            $this->assertEquals('test',$request->getQuery('name','int'));
            $this->assertTrue(false,'why?');
        }catch (\Exception $e){
            $this->assertInstanceOf('ManaPHP\Http\Request\Exception',$e);
        }

        $_GET['name']='mana';
        $this->assertEquals('mana',$request->getQuery('name'));
    }

    public function test_getScheme(){
        $request =new \ManaPHP\Http\Request();

        try{
            $this->assertEquals('http',$request->getScheme());
            $this->assertTrue(false,'why?');
        }catch (\Exception $e){
            $this->assertInstanceOf('ManaPHP\Http\Request\Exception',$e);
        }

        $_SERVER['HTTPS']='off';
        $this->assertEquals('http',$request->getScheme());

        $_SERVER['HTTPS']='on';
        $this->assertEquals('https',$request->getScheme());
    }

    public function test_isAjax(){
        $request =new \ManaPHP\Http\Request();
        $this->assertFalse($request->isAjax());

        $_SERVER['HTTP_X_REQUESTED_WITH']='XMLHttpRequest';
        $this->assertTrue($request->isAjax());

        $_SERVER['HTTP_X_REQUESTED_WITH']='ABC';
        $this->assertFalse($request->isAjax());
    }

    public function test_getRawBody(){

    }

    public function test_getClientAddress(){
        $request =new \ManaPHP\Http\Request();

        $_SERVER['REMOTE_ADDR']='1.2.3.4';
        $this->assertEquals('1.2.3.4',$request->getClientAddress());

        //client address is public ip, we not trust the HTTP_X_FORWARDED_FOR
        $_SERVER['REMOTE_ADDR']='1.2.3.4';
        $_SERVER['HTTP_X_FORWARDED_FOR']='5.6.7.8';
        $this->assertEquals('1.2.3.4',$request->getClientAddress());

        //client address is lookBack ip, we trust the HTTP_X_FORWARDED_FOR
        $_SERVER['REMOTE_ADDR']='127.0.0.1';
        $_SERVER['HTTP_X_FORWARDED_FOR']='5.6.7.8';
        $this->assertEquals('5.6.7.8',$request->getClientAddress());
        //client address is private ip, we trust the HTTP_X_FORWARDED_FOR
        $_SERVER['REMOTE_ADDR']='192.168.1.1';
        $_SERVER['HTTP_X_FORWARDED_FOR']='5.6.7.8';
        $this->assertEquals('5.6.7.8',$request->getClientAddress());

        //client address is private ip, we trust the HTTP_X_FORWARDED_FOR
        $_SERVER['REMOTE_ADDR']='10.0.1.2';
        $_SERVER['HTTP_X_FORWARDED_FOR']='5.6.7.8';
        $this->assertEquals('5.6.7.8',$request->getClientAddress());

        $request->setClientAddress('10.20.30.40');
        $this->assertEquals('10.20.30.40',$request->getClientAddress());
    }

    public function test_setClientAddress(){
        $request =new \ManaPHP\Http\Request();

        $_SERVER['REMOTE_ADDR']='1.2.3.4';
        $this->assertEquals('1.2.3.4',$request->getClientAddress());

        $request->setClientAddress('4.3.2.1');
        $this->assertEquals('4.3.2.1',$request->getClientAddress());

        $request->setClientAddress(function(){
            return '6.7.8.9';
        });
        $this->assertEquals('6.7.8.9',$request->getClientAddress());
    }
    public function test_getUserAgent(){
        $request =new \ManaPHP\Http\Request();

        $this->assertEquals('',$request->getUserAgent());

        $_SERVER['HTTP_USER_AGENT']='IOS';
        $this->assertEquals('IOS',$request->getUserAgent());
    }

    public function test_isPost(){
        $request =new \ManaPHP\Http\Request();

        $_SERVER['REQUEST_METHOD']='GET';
        $this->assertFalse($request->isPost());

        $_SERVER['REQUEST_METHOD']='POST';
        $this->assertTrue($request->isPost());
    }

    public function test_isGet(){
        $request =new \ManaPHP\Http\Request();

        $_SERVER['REQUEST_METHOD']='POST';
        $this->assertFalse($request->isGet());

        $_SERVER['REQUEST_METHOD']='GET';
        $this->assertTrue($request->isGet());
    }

    public function test_isPut(){
        $request =new \ManaPHP\Http\Request();

        $_SERVER['REQUEST_METHOD']='POST';
        $this->assertFalse($request->isPut());

        $_SERVER['REQUEST_METHOD']='PUT';
        $this->assertTrue($request->isPut());
    }


    public function test_isHead(){
        $request =new \ManaPHP\Http\Request();

        $_SERVER['REQUEST_METHOD']='POST';
        $this->assertFalse($request->isHead());

        $_SERVER['REQUEST_METHOD']='HEAD';
        $this->assertTrue($request->isHead());
    }

    public function test_isDelete(){
        $request =new \ManaPHP\Http\Request();

        $_SERVER['REQUEST_METHOD']='POST';
        $this->assertFalse($request->isDelete());

        $_SERVER['REQUEST_METHOD']='DELETE';
        $this->assertTrue($request->isDelete());
    }

    public function test_isOptions(){
        $request =new \ManaPHP\Http\Request();

        $_SERVER['REQUEST_METHOD']='POST';
        $this->assertFalse($request->isOptions());

        $_SERVER['REQUEST_METHOD']='OPTIONS';
        $this->assertTrue($request->isOptions());
    }

    public function test_isPatch(){
        $request =new \ManaPHP\Http\Request();

        $_SERVER['REQUEST_METHOD']='POST';
        $this->assertFalse($request->isPatch());

        $_SERVER['REQUEST_METHOD']='PATCH';
        $this->assertTrue($request->isPatch());
    }

    public function test_getReferer(){
        $request =new \ManaPHP\Http\Request();

        $this->assertEquals('',$request->getReferer());

        $_SERVER['HTTP_REFERER']='http://www.google.com/';
        $this->assertEquals('http://www.google.com/',$request->getReferer());
    }
}
