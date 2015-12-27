<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2015/12/28
 * Time: 0:02
 */
namespace Models;

class FilmActor extends \ManaPHP\Mvc\Model{
    public $actor_id;
    public $film_id;
    public $last_update;

    public function getSource(){
        return 'film_actor';
    }
}