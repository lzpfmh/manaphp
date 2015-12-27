<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2015/12/28
 * Time: 0:01
 */
namespace Models;

class Country extends \ManaPHP\Mvc\Model{
    public $country_id;
    public $country;
    public $last_update;

    public function getSource(){
        return 'country';
    }
}