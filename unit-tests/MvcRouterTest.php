<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2015/12/15
 * Time: 22:15
 */
defined('UNIT_TESTS_ROOT')||require 'bootstrap.php';

class MvcRouterTest extends TestCase{
    public function test_router(){
        $_GET['_url'] = '';

        $tests = array(
            array(
                'uri' => '',
                'controller' => 'index',
                'action' => 'index',
                'params' => array()
            ),
            array(
                'uri' => '/',
                'controller' => 'index',
                'action' => 'index',
                'params' => array()
            ),
            array(
                'uri' => '/documentation/index/hello/ñda/dld/cc-ccc',
                'controller' => 'documentation',
                'action' => 'index',
                'params' => array('hello', 'ñda', 'dld', 'cc-ccc')
            ),
            array(
                'uri' => '/documentation/index/',
                'controller' => 'documentation',
                'action' => 'index',
                'params' => array()
            ),
            array(
                'uri' => '/documentation/index',
                'controller' => 'documentation',
                'action' => 'index',
                'params' => array()
            ),
            array(
                'uri' => '/documentation/',
                'controller' => 'documentation',
                'action' => null,
                'params' => array()
            ),
            array(
                'uri' => '/documentation',
                'controller' => 'documentation',
                'action' => null,
                'params' => array()
            ),
            array(
                'uri' => '/system/admin/a/edit/hello/adp',
                'controller' => 'admin',
                'action' => 'edit',
                'params' => array('hello', 'adp')
            ),
            array(
                'uri' => '/es/news',
                'controller' => 'news',
                'action' => 'index',
                'params' => array('language' => 'es')
            ),
            array(
                'uri' => '/admin/posts/edit/100',
                'controller' => 'posts',
                'action' => 'edit',
                'params' => array('id' => 100)
            ),
            array(
                'uri' => '/posts/2010/02/10/title/content',
                'controller' => 'posts',
                'action' => 'show',
                'params' => array('year' => '2010', 'month' => '02', 'day' => '10', 0 => 'title', 1 => 'content')
            ),
            array(
                'uri' => '/manual/en/translate.adapter.html',
                'controller' => 'manual',
                'action' => 'show',
                'params' => array('language' => 'en', 'file' => 'translate.adapter')
            ),
            array(
                'uri' => '/named-manual/en/translate.adapter.html',
                'controller' => 'manual',
                'action' => 'show',
                'params' => array('language' => 'en', 'file' => 'translate.adapter')
            ),
            array(
                'uri' => '/posts/1999/s/le-nice-title',
                'controller' => 'posts',
                'action' => 'show',
                'params' => array('year' => '1999', 'title' => 'le-nice-title')
            ),
            array(
                'uri' => '/feed/fr/blog/ema.json',
                'controller' => 'feed',
                'action' => 'get',
                'params' => array('lang' => 'fr', 'blog' => 'ema', 'type' => 'json')
            ),
            array(
                'uri' => '/posts/delete/150',
                'controller' => 'posts',
                'action' => 'delete',
                'params' => array('id' => '150')
            ),
            array(
                'uri' => '/very/static/route',
                'controller' => 'static',
                'action' => 'route',
                'params' => array()
            ),
        );

        $router = new ManaPHP\Mvc\Router();

        $router->add('/', array(
            'controller' => 'index',
            'action' => 'index'
        ));

        $router->add('/system/:controller/a/:action/:params', array(
            'controller' => 1,
            'action' => 2,
            'params' => 3,
        ));

        $router->add('/([a-z]{2})/:controller', array(
            'controller' => 2,
            'action' => 'index',
            'language' => 1
        ));

        $router->add('/admin/:controller/:action/:int', array(
            'controller' => 1,
            'action' => 2,
            'id' => 3
        ));

        $router->add('/posts/([0-9]{4})/([0-9]{2})/([0-9]{2})/:params', array(
            'controller' => 'posts',
            'action' => 'show',
            'year' => 1,
            'month' => 2,
            'day' => 3,
            'params' => 4,
        ));

        $router->add('/manual/([a-z]{2})/([a-z\.]+)\.html', array(
            'controller' => 'manual',
            'action' => 'show',
            'language' => 1,
            'file' => 2
        ));

        $router->add('/named-manual/{language:([a-z]{2})}/{file:[a-z\.]+}\.html', array(
            'controller' => 'manual',
            'action' => 'show',
        ));

        $router->add('/very/static/route', array(
            'controller' => 'static',
            'action' => 'route'
        ));

        $router->add('/feed/{lang:[a-z]+}/blog/{blog:[a-z\-]+}\.{type:[a-z\-]+}', 'Feed::get');

        $router->add('/posts/{year:[0-9]+}/s/{title:[a-z\-]+}', 'Posts::show');

        $router->add('/posts/delete/{id}', 'Posts::delete');

        $router->add('/show/{id:video([0-9]+)}/{title:[a-z\-]+}', 'Videos::show');

        foreach ($tests as $n => $test) {
            $router->handle($test['uri']);
            $this->assertEquals($router->getControllerName(), $test['controller'], 'Testing ' . $test['uri']);
            $this->assertEquals($router->getActionName(), $test['action'], 'Testing ' . $test['uri']);
            $this->assertEquals($router->getParams(), $test['params'], 'Testing ' . $test['uri']);
        }
    }

    public function test_add(){
        $tests = array(
            array(
                'method' => null,
                'uri' => '/documentation/index/hello',
                'controller' => 'documentation',
                'action' => 'index',
                'params' => array('hello')
            ),
            array(
                'method' => 'POST',
                'uri' => '/docs/index',
                'controller' => 'documentation3',
                'action' => 'index',
                'params' => array()
            ),
            array(
                'method' => 'GET',
                'uri' => '/docs/index',
                'controller' => 'documentation4',
                'action' => 'index',
                'params' => array()
            ),
            array(
                'method' => 'PUT',
                'uri' => '/docs/index',
                'controller' => 'documentation5',
                'action' => 'index',
                'params' => array()
            ),
            array(
                'method' => 'DELETE',
                'uri' => '/docs/index',
                'controller' => 'documentation6',
                'action' => 'index',
                'params' => array()
            ),
            array(
                'method' => 'OPTIONS',
                'uri' => '/docs/index',
                'controller' => 'documentation7',
                'action' => 'index',
                'params' => array()
            ),
            array(
                'method' => 'HEAD',
                'uri' => '/docs/index',
                'controller' => 'documentation8',
                'action' => 'index',
                'params' => array()
            ),
        );

        $di = new ManaPHP\DI();

        $di->set('request', function(){
            return new ManaPHP\Http\Request();
        });

        $router = new ManaPHP\Mvc\Router();
        $router->setDI($di);

        $router->add('/docs/index', array(
            'controller' => 'documentation2',
            'action' => 'index'
        ));

        $router->addPost('/docs/index', array(
            'controller' => 'documentation3',
            'action' => 'index'
        ));

        $router->addGet('/docs/index', array(
            'controller' => 'documentation4',
            'action' => 'index'
        ));

        $router->addPut('/docs/index', array(
            'controller' => 'documentation5',
            'action' => 'index'
        ));

        $router->addDelete('/docs/index', array(
            'controller' => 'documentation6',
            'action' => 'index'
        ));

        $router->addOptions('/docs/index', array(
            'controller' => 'documentation7',
            'action' => 'index'
        ));

        $router->addHead('/docs/index', array(
            'controller' => 'documentation8',
            'action' => 'index'
        ));

        foreach ($tests as $n => $test) {
            $_SERVER['REQUEST_METHOD'] = $test['method'];
            $router->handle($test['uri']);
            $this->assertEquals($router->getControllerName(), $test['controller'], 'Testing ' . $test['uri']);
            $this->assertEquals($router->getActionName(), $test['action'], 'Testing ' . $test['uri']);
            $this->assertEquals($router->getParams(), $test['params'], 'Testing ' . $test['uri']);
        }
    }

    public function test_params(){
        $router = new ManaPHP\Mvc\Router(false);

        $tests = array(
            array(
                'method' => null,
                'uri' => '/some/hattie',
                'controller' => 'c',
                'action' => 'a',
                'params' => array('name' => 'hattie')
            ),
            array(
                'method' => null,
                'uri' => '/some/hattie/100',
                'controller' => 'c',
                'action' => 'a',
                'params' => array('name' => 'hattie', 'id' => 100)
            ),
            array(
                'method' => null,
                'uri' => '/some/hattie/100/2011-01-02',
                'controller' => 'c',
                'action' => 'a',
                'params' => array('name' => 'hattie', 'id' => 100, 'date' => '2011-01-02')
            ),
        );

        $router->add('/some/{name}',['controller'=>'c','action'=>'a']);
        $router->add('/some/{name}/{id:[0-9]+}',['controller'=>'c','action'=>'a']);
        $router->add('/some/{name}/{id:[0-9]+}/{date}',['controller'=>'c','action'=>'a']);

        foreach ($tests as $n => $test) {
            $_SERVER['REQUEST_METHOD'] = $test['method'];

            $router->handle($test['uri']);
            $this->assertEquals($test['controller'], $router->getControllerName(), 'Testing ' . $test['uri']);
            $this->assertEquals($test['action'], $router->getActionName(), 'Testing ' . $test['uri']);
            $this->assertEquals($test['params'],$router->getParams(), 'Testing ' . $test['uri']);
        }
    }

    public function test_removeExtraSlashes(){
        $router = new ManaPHP\Mvc\Router();

        $router->removeExtraSlashes(true);

        $routes = array(
            '/index/' => array(
                'controller' => 'index',
                'action' => '',
            ),
            '/session/start/' => array(
                'controller' => 'session',
                'action' => 'start'
            ),
            '/users/edit/100/' => array(
                'controller' => 'users',
                'action' => 'edit'
            ),
        );

        foreach ($routes as $route => $paths) {
            $router->handle($route);
            $this->assertTrue($router->wasMatched());
            $this->assertEquals($paths['controller'], $router->getControllerName());
            $this->assertEquals($paths['action'], $router->getActionName());
        }
    }

    public function test_mount(){
        $router = new ManaPHP\Mvc\Router(false);

        $blog = new ManaPHP\Mvc\Router\Group(array(
            'module' => 'blog',
            'controller' => 'index'
        ));

        $blog->setPrefix('/blog');

        $blog->add('/save', array(
            'action' => 'save'
        ));

        $blog->add('/edit/{id}', array(
            'action' => 'edit'
        ));

        $blog->add('/about', array(
            'controller' => 'about',
            'action' => 'index'
        ));

        $router->mount($blog);

        $routes = array(
            '/blog/save' => array(
                'module' => 'blog',
                'controller' => 'index',
                'action' => 'save',
            ),
            '/blog/edit/1' => array(
                'module' => 'blog',
                'controller' => 'index',
                'action' => 'edit'
            ),
            '/blog/about' => array(
                'module' => 'blog',
                'controller' => 'about',
                'action' => 'index'
            ),
        );

        foreach ($routes as $route => $paths) {
            $router->handle($route);
            $this->assertTrue($router->wasMatched());
            $this->assertEquals($paths['module'], $router->getModuleName());
            $this->assertEquals($paths['controller'], $router->getControllerName());
            $this->assertEquals($paths['action'], $router->getActionName());
        }
    }

    public function test_shortPaths(){
        $router = new ManaPHP\Mvc\Router(false);

        $route = $router->add('/route0', 'Feed');
        $this->assertEquals($route->getPaths(), array(
            'controller' => 'feed'
        ));

        $route = $router->add('/route1', 'Feed::get');
        $this->assertEquals($route->getPaths(), array(
            'controller' => 'feed',
            'action' => 'get',
        ));

        $route = $router->add('/route2', 'News::Posts::show');
        $this->assertEquals($route->getPaths(), array(
            'module' => 'News',
            'controller' => 'posts',
            'action' => 'show',
        ));

        $route = $router->add('/route3', 'MyApp\Controllers\Posts::show');
        $this->assertEquals($route->getPaths(), array(
            'namespace' => 'MyApp\Controllers',
            'controller' => 'posts',
            'action' => 'show',
        ));

        //incompatible with phalcon
        $route = $router->add('/route3', 'MyApp\Controllers\::show');
        $this->assertEquals($route->getPaths(), array(
            'namespace' => 'MyApp\Controllers',
            'controller' => '',
            'action' => 'show',
        ));

        $route = $router->add('/route3', 'News::MyApp\Controllers\Posts::show');
        $this->assertEquals($route->getPaths(), array(
            'module' => 'News',
            'namespace' => 'MyApp\Controllers',
            'controller' => 'posts',
            'action' => 'show',
        ));

        //incompatible with phalcon
        $route = $router->add('/route3', '\Posts::show');
        $this->assertEquals($route->getPaths(), array(
            'namespace'=>'',
            'controller' => 'posts',
            'action' => 'show',
        ));
    }

    public function test_getRewriteUri(){
        $_GET['_url'] = '/some/route';

        $router = new ManaPHP\Mvc\Router(false);

        //default get from url
        $this->assertEquals('/some/route',$router->getRewriteUri());

        //explicitly get from url
        $router->setUriSource(ManaPHP\Mvc\Router::URI_SOURCE_GET_URL);
        $this->assertEquals('/some/route',$router->getRewriteUri());

        //explicitly get from request uri without query string
        $router->setUriSource(ManaPHP\Mvc\Router::URI_SOURCE_SERVER_REQUEST_URI);
        $_SERVER['REQUEST_URI'] = '/some/route';
        $this->assertEquals('/some/route',$router->getRewriteUri());

        //explicitly get from request uri with query string
        $_SERVER['REQUEST_URI'] = '/some/route?x=1';
        $this->assertEquals('/some/route',$router->getRewriteUri());
    }

    public function test_beforeMatch(){
        $trace = 0;

        $router = new ManaPHP\Mvc\Router(false);

        $router->add('/static/route',[])
            ->beforeMatch(function() use (&$trace) {
                $trace++;
                return false;
            });

        $router
            ->add('/static/route2',[])
            ->beforeMatch(function() use (&$trace) {
                $trace++;
                return true;
            });

        try{
            $router->handle();
            $this->assertTrue(false,'why not?');
        }catch (\ManaPHP\Exception $e){
            $this->assertInstanceOf('\ManaPHP\Mvc\Router\Exception',$e);
        }

        $router->handle('/static/route');
        $this->assertFalse($router->wasMatched());
        $this->assertEquals(1,$trace);
        $router->handle('/static/route2');
        $this->assertTrue($router->wasMatched());

        $this->assertEquals(2,$trace);
    }
}