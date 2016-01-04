<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2016/1/4
 * Time: 22:21
 */

namespace Application\Home\Models;

class Actor extends \ManaPHP\Mvc\Model{
    public $actor_id;
    public $first_name;
    public $last_name;
    public $last_update;

    public function getSource(){
        return 'actor';
    }
}