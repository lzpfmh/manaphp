<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2015/11/21
 * Time: 22:21
 */
namespace Application\Home;

use \ManaPHP\Mvc\ModuleInterface;
use \ManaPHP\Autoloader;
use \ManaPHP\Db\Adapter;

class Module implements ModuleInterface{
    public function registerAutoloaders($di){
        $loader =new Autoloader();
        $loader->registerNamespaces([
            'Application\Home'=>realpath(__DIR__).''
        ])->register();
    }

    public function registerServices($di){
        $di->set('db',function(){
            return new Adapter(['host'=>'localhost',
                'username'=>'root',
                'password'=>'',
                'dbname'=>'test',
                'port'=>3306]);
        });
    }
}