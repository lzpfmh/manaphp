<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2015/11/22
 * Time: 12:35
 */
namespace Application\Home\Controllers;

use Application\Home\Models\User;
use ManaPHP\Mvc\Controller;
class SomeComponent{
    public $someProperty=false;

    public function __construct($v){
        $this->someProperty =$v;
    }
}
class IndexController extends Controller{
    public function indexAction(){
        error_reporting(E_ALL);
//        var_dump($this->db->fetchAll('SELECT * FROM test.user'));
//        var_dump($this->db->fetchAll('SELECT * FROM test.user WHERE id=:id',\PDO::FETCH_ASSOC,[':id'=>2]));
//        var_dump($this->db->getSQLStatement());
//        $users=User::find(['id >:id:','bind'=>['id'=>3],
//                    'order'=>'id desc',
//                    'columns'=>'id, age']);
//        foreach($users as $user){
//            var_dump($user->toArray());
//            echo $user->id;
//        }

//        var_dump(User::findFirst('2')->toArray());

      //  var_dump(User::count(['id >1']));
        $user =new User();
        $user->id=12;
        $user->age=30;
        $user->name='mana'.microtime(true);
        $user->save();

        echo $this->dispatcher->getControllerClass(),PHP_EOL;
        echo $this->dispatcher->getControllerName(),PHP_EOL;
        echo $this->dispatcher->getActionName(),PHP_EOL;
        var_dump($user->toArray());

        $this->_dependencyInjector->set('getComponent1', function($v){
            return new SomeComponent($v);
        });

        $this->_dependencyInjector->set('getComponent2','Application\Home\Controllers\SomeComponent');

        var_dump($this->_dependencyInjector->get('getComponent1',[100]));
        var_dump($this->_dependencyInjector->get('getComponent2',[50]));
    }

    public function index2Action(){
        echo date('Y-m-d H:i:s');
    }
}