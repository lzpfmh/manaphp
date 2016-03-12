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
                'action' => 'index',
                'params' => array()
            ),
            array(
                'uri' => '/documentation',
                'controller' => 'documentation',
                'action' => 'index',
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
        $group=new \ManaPHP\Mvc\Router\Group();
        $group->add('/', array(
            'controller' => 'index',
            'action' => 'index'
        ));

        $group->add('/system/:controller/a/:action/:params', array(
            'controller' => 1,
            'action' => 2,
            'params' => 3,
        ));

        $group->add('/([a-z]{2})/:controller', array(
            'controller' => 2,
            'action' => 'index',
            'language' => 1
        ));

        $group->add('/admin/:controller/:action/:int', array(
            'controller' => 1,
            'action' => 2,
            'id' => 3
        ));

        $group->add('/posts/([0-9]{4})/([0-9]{2})/([0-9]{2})/:params', array(
            'controller' => 'posts',
            'action' => 'show',
            'year' => 1,
            'month' => 2,
            'day' => 3,
            'params' => 4,
        ));

        $group->add('/manual/([a-z]{2})/([a-z\.]+)\.html', array(
            'controller' => 'manual',
            'action' => 'show',
            'language' => 1,
            'file' => 2
        ));

        $group->add('/named-manual/{language:([a-z]{2})}/{file:[a-z\.]+}\.html', array(
            'controller' => 'manual',
            'action' => 'show',
        ));

        $group->add('/very/static/route', array(
            'controller' => 'static',
            'action' => 'route'
        ));

        $group->add('/feed/{lang:[a-z]+}/blog/{blog:[a-z\-]+}\.{type:[a-z\-]+}', 'Feed::get');

        $group->add('/posts/{year:[0-9]+}/s/{title:[a-z\-]+}', 'Posts::show');

        $group->add('/posts/delete/{id}', 'Posts::delete');

        $group->add('/show/{id:video([0-9]+)}/{title:[a-z\-]+}', 'Videos::show');

        $router->mount($group);
        foreach ($tests as $n => $test) {
            $router->handle($test['uri']);
            $this->assertEquals(strtolower($router->getControllerName()), strtolower($test['controller']), 'Testing ' . $test['uri']);
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
        $router->setDependencyInjector($di);
        $group=new \ManaPHP\Mvc\Router\Group();
        $group->add('/docs/index', array(
            'controller' => 'documentation2',
            'action' => 'index'
        ));

        $group->addPost('/docs/index', array(
            'controller' => 'documentation3',
            'action' => 'index'
        ));

        $group->addGet('/docs/index', array(
            'controller' => 'documentation4',
            'action' => 'index'
        ));

        $group->addPut('/docs/index', array(
            'controller' => 'documentation5',
            'action' => 'index'
        ));

        $group->addDelete('/docs/index', array(
            'controller' => 'documentation6',
            'action' => 'index'
        ));

        $group->addOptions('/docs/index', array(
            'controller' => 'documentation7',
            'action' => 'index'
        ));

        $group->addHead('/docs/index', array(
            'controller' => 'documentation8',
            'action' => 'index'
        ));

        $router->mount($group);
        foreach ($tests as $n => $test) {
            $_SERVER['REQUEST_METHOD'] = $test['method'];
            $router->handle($test['uri']);
            $this->assertEquals(strtolower($router->getControllerName()), strtolower($test['controller']), 'Testing ' . $test['uri']);
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
        $group =new \ManaPHP\Mvc\Router\Group();
        $group->add('/some/{name}',['controller'=>'c','action'=>'a']);
        $group->add('/some/{name}/{id:[0-9]+}',['controller'=>'c','action'=>'a']);
        $group->add('/some/{name}/{id:[0-9]+}/{date}',['controller'=>'c','action'=>'a']);

        $router->mount($group);

        foreach ($tests as $n => $test) {
            $_SERVER['REQUEST_METHOD'] = $test['method'];

            $router->handle($test['uri']);
            $this->assertEquals(strtolower($test['controller']), strtolower($router->getControllerName()), 'Testing ' . $test['uri']);
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
                'action' => 'index',
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
            /** @noinspection DisconnectedForeachInstructionInspection */
            $this->assertTrue($router->wasMatched());
            $this->assertEquals(strtolower($paths['controller']), strtolower($router->getControllerName()));
            $this->assertEquals($paths['action'], $router->getActionName());
        }
    }

    public function test_mount(){
        $router = new ManaPHP\Mvc\Router(false);

        $group = new ManaPHP\Mvc\Router\Group();

        $group->add('/blog/save', array(
            'action' => 'save'
        ));

        $group->add('/blog/edit/{id}', array(
            'action' => 'edit'
        ));

        $group->add('/blog/about', array(
            'controller' => 'about',
            'action' => 'index'
        ));

        $router->mount($group,'blog');

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
            /** @noinspection DisconnectedForeachInstructionInspection */
            $this->assertTrue($router->wasMatched());
            $this->assertEquals($paths['module'], $router->getModuleName());
            $this->assertEquals(strtolower($paths['controller']), strtolower($router->getControllerName()));
            $this->assertEquals($paths['action'], $router->getActionName());
        }
    }

    public function test_shortPaths(){
        $route = new \ManaPHP\Mvc\Router\Route('/route0', 'feed');
        $this->assertEquals($route->getPaths(), array(
            'controller' => 'Feed'
        ));

        $route =new ManaPHP\Mvc\Router\Route('/route1', 'Feed::get');
        $this->assertEquals($route->getPaths(), array(
            'controller' => 'Feed',
            'action' => 'get',
        ));

        $route = new \ManaPHP\Mvc\Router\Route('/route2', 'News::Posts::show');
        $this->assertEquals($route->getPaths(), array(
            'module' => 'News',
            'controller' => 'Posts',
            'action' => 'show',
        ));

        $route = new \ManaPHP\Mvc\Router\Route('/route3', 'Posts::show');
        $this->assertEquals($route->getPaths(), array(
            'controller' => 'Posts',
            'action' => 'show',
        ));
    }

    public function test_getRewriteUri(){
        $_GET['_url'] = '/some/route';

        $router = new ManaPHP\Mvc\Router(false);

        //default get from url
        $this->assertEquals('/some/route',$router->getRewriteUri());

        //explicitly get from request uri with query string
        $_SERVER['REQUEST_URI'] = '/some/route?x=1';
        $this->assertEquals('/some/route',$router->getRewriteUri());
    }
}