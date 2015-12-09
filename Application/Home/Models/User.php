<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2015/12/7
 * Time: 22:27
 */
namespace Application\Home\Models;

use ManaPHP\Mvc\Model;

class User extends Model{
    public $id;
    public $age;
    public $name;

    public function getSource(){
        return 'user';
    }
}