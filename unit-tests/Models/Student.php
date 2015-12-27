<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2015/12/28
 * Time: 0:05
 */
namespace Models;

class Student extends \ManaPHP\Mvc\Model{
    public $id;
    public $age;
    public $name;

    public function getSource(){
        return '_student';
    }
}