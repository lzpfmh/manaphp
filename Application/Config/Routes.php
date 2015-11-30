<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2015/11/22
 * Time: 12:31
 */
$router =new \ManaPHP\Mvc\Router(false);
$router->addGet('/',['controller'=>'Index','action'=>'Index','module'=>'Home','namespace'=>'Application\Home\Controllers']);
$router->removeExtraSlashes(true);
return $router;