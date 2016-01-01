<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2015/11/21
 * Time: 21:52
 */

error_reporting(E_ALL);

date_default_timezone_set('PRC');

define ('APP_ROOT',dirname(__DIR__).'/Application');
define ('MANAPHP_ROOT',dirname(APP_ROOT).'/ManaPHP');
require MANAPHP_ROOT. '/Autoloader.php';

\ManaPHP\Autoloader::register(false);

try{
    $application = new \ManaPHP\Mvc\Application();
    $di =$application->getDI();
    $di->set('router',function(){
        return require APP_ROOT . '/Config/Routes.php';
    },true);
    $application->useImplicitView(false);

    $application->registerModules(
        ['Home'=>['className'=>'Application\Home\Module','path'=>APP_ROOT.'/Home/Module.php']]
    );
    $application->setDefaultModule('Home');

    echo $application->handle()->getContent();
}catch (\Exception $e){
    var_dump($e->getMessage());
    var_dump($_GET);
    var_dump($_SERVER);
}