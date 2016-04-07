<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2015/11/21
 * Time: 21:52
 */

error_reporting(E_ALL);

date_default_timezone_set('PRC');

define('APP_ROOT', dirname(__DIR__) . '/Application');

require dirname(__DIR__) . '/ManaPHP/Autoloader.php';

\ManaPHP\Autoloader::register(false);

//try{
$application = new \ManaPHP\Mvc\Application(APP_ROOT);
$application->useImplicitView(false);
$application->registerModules(['Home']);
$application->router->mount(new \ManaPHP\Mvc\Router\Group(),'Home','/');
class Authentication implements ManaPHP\AuthorizationInterface{
    public function authorize($dispatcher)
    {
        return true;
    }
}
$application->di->setShared('authorization',new Authentication());
echo $application->handle()->getContent();
//var_dump($application->__debugInfo());
//}catch (\Exception $e){
//    var_dump($e->getMessage());
//    var_dump($_GET);
//    var_dump($_SERVER);
//}
