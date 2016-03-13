<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2015/11/21
 * Time: 22:21
 */
namespace Application\Home;

use ManaPHP\Db\Adapter\Mysql;
use ManaPHP\DbInterface;
use ManaPHP\Loader;
use ManaPHP\Mvc\ModuleInterface;

class Module implements ModuleInterface
{
    public function registerAutoloaders($di)
    {
        $loader = new Loader();
        $loader->registerNamespaces([
          'Application\Home' => realpath(__DIR__) . ''
        ])->register();
    }

    public function registerServices($di)
    {
        $di->set('db', function () {
            $mysql = new Mysql([
              'host' => 'localhost',
              'username' => 'root',
              'password' => '',
              'dbname' => 'manaphp_unit_test',
              'port' => 3306
            ]);

            $mysql->attachEvent('db:beforeQuery', function ($event, DbInterface $source, $data) {
                var_dump($source->getSQLStatement());
            });

            return $mysql;
        });
    }
}