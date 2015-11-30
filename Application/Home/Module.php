<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2015/11/21
 * Time: 22:21
 */
namespace Application\Home;

use \ManaPHP\Mvc\ModuleDefinitionInterface;
use \ManaPHP\Autoloader;

class Module implements ModuleDefinitionInterface{
    public function registerAutoloaders($di){
        $loader =new Autoloader();
        $loader->registerNamespaces([
            'Application\Home\Controllers'=>realpath(__DIR__).'/Controllers'
        ])->register();
    }

    public function registerServices($di){
    }
}