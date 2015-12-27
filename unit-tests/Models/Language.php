<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2015/12/28
 * Time: 0:04
 */
namespace Models;

class Language extends \ManaPHP\Mvc\Model{
    public $language_id;
    public $name;
    public $last_update;

    public function getSource(){
        return 'language';
    }
}