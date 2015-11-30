<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2015/11/22
 * Time: 12:35
 */
namespace Application\Home\Controllers;

use ManaPHP\Mvc\Controller;

class IndexController extends Controller{
    public function indexAction(){
      //  $this->dispatcher->forward(['action'=>'index2']);
        echo date('Y-m-d H:i:s');
        var_dump(memory_get_usage(true));
        var_dump(memory_get_usage(false));
//        echo json_encode(get_defined_functions(),JSON_PRETTY_PRINT);
//        echo json_encode(xdebug_get_function_stack(),JSON_PRETTY_PRINT);
       // var_dump(get_included_files());
       // xdebug_var_dump($this);

        error_reporting(E_ALL);
    }

    public function index2Action(){
        echo date('Y-m-d H:i:s');
    }
}