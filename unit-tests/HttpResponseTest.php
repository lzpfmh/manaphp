<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2015/12/17
 * Time: 21:42
 */
defined('UNIT_TESTS_ROOT')||require 'bootstrap.php';

class tResponse extends \ManaPHP\Http\Response{
    public function getHeaders(){
        return $this->_headers;
    }
}

class HttpResponseTest extends TestCase{
    public function test_setHeader(){
        $response =new tResponse();

        $response->setHeader('Content-Type','text/html');
        $this->assertEquals(['Content-Type'=>'text/html'],$response->getHeaders()->toArray());

        $response->setHeader('Content-Length','1234');
        $this->assertEquals(['Content-Type'=>'text/html',
                                'Content-Length'=>'1234'],
                        $response->getHeaders()->toArray());
    }

    public function test_setStatusCode(){
        $response =new tResponse();

        //set only time
        $response->setStatusCode(404,'Not Found');
        $this->assertEquals(['Status'=>'404 Not Found'],$response->getHeaders()->toArray());

        //set multiple times
        $response =new tResponse();
        $response->setStatusCode(200,'OK');
        $response->setStatusCode(404,'Not Found');
        $response->setStatusCode(409,'Conflict');
        $this->assertEquals(['Status'=>'409 Conflict'],$response->getHeaders()->toArray());
    }

    public function test_setRawHeader(){
        $response =new tResponse();

        $response->setRawHeader('HTTP/1.1 404 Not Found');
        $this->assertEquals(['HTTP/1.1 404 Not Found'=>''],$response->getHeaders()->toArray());
    }

    public function test_setContentType(){
        $response =new tResponse();

        $response->setContentType('application/json');
        $this->assertEquals(['Content-Type'=>'application/json'],$response->getHeaders()->toArray());

        $response->setContentType('application/json','utf-8');
        $this->assertEquals(['Content-Type'=>'application/json; charset=utf-8'],$response->getHeaders()->toArray());
    }

    public function test_setNotModified(){
        $response =new tResponse();

        $response->setNotModified();

        $this->assertEquals(['Status'=>'304 Not modified'],$response->getHeaders()->toArray());
    }

    public function test_redirect(){
        $response =new tResponse();
        $response->setDI(new ManaPHP\Di());

        $response->redirect('some/local/url');
        $this->assertEquals(['Status'=>'302 Temporarily Moved',
                        'Location'=>'some/local/url'],
                    $response->getHeaders()->toArray());

        $response =new tResponse();
        $response->setDI(new ManaPHP\Di());

        $response->redirect('http://www.manaphp.com',true);
        $this->assertEquals(['Status'=>'302 Temporarily Moved',
            'Location'=>'http://www.manaphp.com'],
            $response->getHeaders()->toArray());

        $response =new tResponse();
        $response->setDI(new ManaPHP\Di());

        $response->redirect('http://www.manaphp.com',true,301);
        $this->assertEquals(['Status'=>'301 Permanently Moved',
            'Location'=>'http://www.manaphp.com'],
            $response->getHeaders()->toArray());


        $response =new tResponse();
        $response->setDI(new ManaPHP\Di());

        $response->redirect('http://www.manaphp.com',false,301);
        $this->assertEquals(['Status'=>'301 Permanently Moved',
            'Location'=>'http://www.manaphp.com'],
            $response->getHeaders()->toArray());
    }

    public function test_setContent(){
        $response =new tResponse();

        $response->setContent('<h1>Hello');
        $this->assertEquals('<h1>Hello',$response->getContent());

        $response->setContent('<h1>Hello 2');
        $this->assertEquals('<h1>Hello 2',$response->getContent());
    }

    public function test_appendContent(){
        $response =new tResponse();

        $this->assertEquals('',$response->getContent());

        $response->appendContent('<h1>Hello');
        $this->assertEquals('<h1>Hello',$response->getContent());

        $response->appendContent('</h1>');
        $this->assertEquals('<h1>Hello</h1>',$response->getContent());
    }

    /**
     * @
     */
    public function test_setFileToSend(){
        $response =new tResponse();

        $response->setFileToSend(__FILE__);
        ob_start();
        $response->send();
        $this->assertEquals(file_get_contents(__FILE__),ob_get_clean());
        $this->assertTrue($response->isSent());
    }
}