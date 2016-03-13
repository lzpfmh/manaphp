<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2016/1/1
 * Time: 23:00
 */
namespace Application\Home\Models;

use ManaPHP\Mvc\Model;

class Address extends Model
{
    public $address_id;
    public $address;
    public $address2;
    public $district;
    public $city_id;
    public $postal_code;
    public $phone;
    public $last_update;

    public function getSource()
    {
        return 'address';
    }
}