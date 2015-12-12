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

class IndexController extends Controller{
    public function indexAction(){
        error_reporting(E_ALL);
//        var_dump($this->db->fetchAll('SELECT * FROM test.user'));
//        var_dump($this->db->fetchAll('SELECT * FROM test.user WHERE id=:id',\PDO::FETCH_ASSOC,[':id'=>2]));
//        var_dump($this->db->getSQLStatement());
        $users=User::find(['id >:id:','bind'=>['id'=>3],
                    'order'=>'id desc',
                    'columns'=>'id, age']);
        foreach($users as $user){
            var_dump($user->toArray());
            echo $user->id;
        }


      //  var_dump(User::count(['id >1']));
    }

    public function index2Action(){
        echo date('Y-m-d H:i:s');
    }
}