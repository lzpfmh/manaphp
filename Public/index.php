<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2015/11/21
 * Time: 21:52
 */

error_reporting(E_ALL);


define('APP_ROOT', dirname(__DIR__) . '/Application');

require dirname(__DIR__) . '/ManaPHP/Autoloader.php';

\ManaPHP\Autoloader::register(false);

require dirname(__DIR__).'/Application/Application.php';
$application =new \Application\Application(APP_ROOT);

echo $application->main();