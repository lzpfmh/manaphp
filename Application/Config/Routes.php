<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2015/11/22
 * Time: 12:31
 */
$router =new \ManaPHP\Mvc\Router(false);
$router->add('/',['controller'=>'index','action'=>'index']);
$router->add('/test2',['controller'=>'index','action'=>'test2']);
$router->removeExtraSlashes(true);
return $router;