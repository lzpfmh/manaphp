<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2016/1/4
 * Time: 22:21
 */

namespace Application\Home\Models;
use \ManaPHP\Mvc\Model;
class Actor extends Model{
    public $actor_id;
    public $first_name;
    public $last_name;
    public $last_update;

    public function getSource(){
        return 'actor';
    }
}