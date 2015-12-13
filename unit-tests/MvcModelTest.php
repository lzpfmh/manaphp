<?php

/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2015/12/12
 * Time: 17:07
 */
defined('UNIT_TESTS_ROOT')||require 'bootstrap.php';

class Actor extends \ManaPHP\Mvc\Model{
    public $actor_id;
    public $first_name;
    public $last_name;
    public $last_update;

    public function getSource(){
        return 'actor';
    }
}

class Address extends \ManaPHP\Mvc\Model{
    public $address_id;
    public $address;
    public $address2;
    public $district;
    public $city_id;
    public $postal_code;
    public $phone;
    public $last_update;

    public function getSource(){
        return 'address';
    }
}

class Category extends \ManaPHP\Mvc\Model{
    public $category_id;
    public $name;
    public $last_update;

    public function getSource(){
        return 'category';
    }
}

class City extends \ManaPHP\Mvc\Model{
    public $city_id;
    public $city;
    public $country_id;
    public $last_update;

    public function getSource(){
        return 'city';
    }
}

class Country extends \ManaPHP\Mvc\Model{
    public $country_id;
    public $country;
    public $last_update;

    public function getSource(){
        return 'country';
    }
}

class Customer extends \ManaPHP\Mvc\Model{
    public $customer_id;
    public $store_id;
    public $first_name;
    public $last_name;
    public $email;
    public $address_id;
    public $active;
    public $create_date;
    public $last_update;

    public function getSource(){
        return 'customer';
    }
}

class Film extends \ManaPHP\Mvc\Model{
    public $film_id;
    public $title;
    public $description;
    public $release_year;
    public $language_id;
    public $original_language_id;
    public $rental_duration;
    public $rental_rate;
    public $length;
    public $replacement_cost;
    public $rating;
    public $special_features;
    public $last_update;

    public function getSource(){
        return 'film';
    }
}

class FilmActor extends \ManaPHP\Mvc\Model{
    public $actor_id;
    public $film_id;
    public $last_update;

    public function getSource(){
        return 'film_actor';
    }
}

class FilmCategory extends \ManaPHP\Mvc\Model{
    public $film_id;
    public $category_id;
    public $last_update;

    public function getSource(){
        return 'film_category';
    }
}

class FilmText extends \ManaPHP\Mvc\Model{
    public $film_id;
    public $title;
    public $description;

    public function getSource(){
        return 'film_text';
    }
}

class Inventory extends \ManaPHP\Mvc\Model{
    public $inventory_id;
    public $film_id;
    public $store_id;
    public $last_update;

    public function getSource(){
        return 'inventory';
    }
}

class Language extends \ManaPHP\Mvc\Model{
    public $language_id;
    public $name;
    public $last_update;

    public function getSource(){
        return 'language';
    }
}

class Payment extends \ManaPHP\Mvc\Model{
    public $payment_id;
    public $customer_id;
    public $staff_id;
    public $rental_id;
    public $amount;
    public $payment_date;
    public $last_update;

    public function getSource(){
        return 'payment';
    }
}

class Rental extends \ManaPHP\Mvc\Model{
    public $rental_id;
    public $rental_date;
    public $inventory_id;
    public $customer_id;
    public $return_date;
    public $staff_id;
    public $last_update;

    public function getSource(){
        return 'rental';
    }
}

class Staff extends \ManaPHP\Mvc\Model{
    public $staff_id;
    public $first_name;
    public $last_name;
    public $address_id;
    public $picture;
    public $email;
    public $store_id;
    public $active;
    public $username;
    public $password;
    public $last_update;

    public function getSource(){
        return 'staff';
    }
}

class Store extends \ManaPHP\Mvc\Model{
    public $store_id;
    public $manager_staff_id;
    public $address_id;
    public $last_update;

    public function getSource(){
        return 'store';
    }
}

class Student extends \ManaPHP\Mvc\Model{
    public $id;
    public $age;
    public $name;

    public function getSource(){
        return '_student';
    }
}

class MvcModelTest extends TestCase{
    /**
     * @var \ManaPHP\DiInterface
     */
    protected $di;
    public function setUp(){
        $this->di=new ManaPHP\Di();

        $this->di->set('modelsManager',function(){
           return new ManaPHP\Mvc\Model\Manager();
        });

        $this->di->set('modelsMetadata',function(){
            return new ManaPHP\Mvc\Model\MetaData\Memory();
        });

        $this->di->set('db',function(){
            $config= require 'config.database.php';
            return new ManaPHP\Db\Adapter($config['mysql']);
        });
    }

    public function testCount(){

        $this->assertTrue(is_int(Actor::count()));

        $this->assertTrue(Actor::count()===200);

        $this->assertTrue(Actor::count('')===200);
        $this->assertTrue(Actor::count('actor_id=1')===1);

        $this->assertTrue(Actor::count([])===200);
        $this->assertTrue(Actor::count(['actor_id=1'])===1);
        $this->assertTrue(Actor::count(['conditions'=>'actor_id=1'])===1);

        $this->assertTrue(Actor::count(['actor_id=0'])===0);
    }

    public function testFindFirst(){
        $actor =Actor::findFirst();

        $this->assertTrue(is_object($actor));
        $this->assertEquals(get_class($actor),'Actor');
        $this->assertEquals(get_parent_class($actor),'ManaPHP\Mvc\Model');

        $this->assertTrue(is_object(Actor::findFirst('')));
        $this->assertTrue(is_object(Actor::findFirst('actor_id=1')));
        $this->assertTrue(is_object(Actor::findFirst(['actor_id=1'])));
        $this->assertTrue(is_object(Actor::findFirst(['conditions'=>'actor_id=1'])));

        $actor=Actor::findFirst(['conditions'=>'first_name=\'BEN\'','order'=>'actor_id']);
        $this->assertTrue($actor instanceof Actor);
        $this->assertEquals($actor->actor_id,'83');
        $this->assertEquals($actor->last_name,'WILLIS');

        $actor2=Actor::findFirst(['conditions'=>'first_name=:first_name:',
                'bind'=>['first_name'=>'BEN'],
            'order'=>'actor_id']);
        $this->assertTrue($actor2 instanceof Actor);
        $this->assertEquals($actor->actor_id,$actor2->actor_id);

        $actor=Actor::findFirst(10);
        $this->assertEquals(get_class($actor),'Actor');
        $this->assertEquals($actor->actor_id,'10');
    }

    public function testFind(){
        $actors=Actor::find();
        $this->assertTrue(is_array($actors));
        $this->assertEquals(get_class($actors[1]),'Actor');
        $this->assertEquals(get_parent_class($actors[0]),'ManaPHP\Mvc\Model');

        $this->assertCount(200,Actor::find());
        $this->assertCount(200,Actor::find(''));
        $this->assertCount(200,Actor::find([]));
        $this->assertCount(200,Actor::find(['']));

        $this->assertCount(2,Actor::find('first_name=\'BEN\''));
        $this->assertCount(2,Actor::find(['first_name=:first_name:',
                                'bind'=>['first_name'=>'BEN']]));

        $this->assertCount(1,Actor::find(['first_name=:first_name:',
            'bind'=>['first_name'=>'BEN'],
            'limit'=>1]));
    }

    /**
     * @param \ManaPHP\Mvc\Model $model
     */
    protected function _truncateTable($model){
        /**
         * @var \ManaPHP\Db\Adapter $db
         */
        $db =$this->di->getShared('db');
        $db->execute('TRUNCATE TABLE '.$model->getSource());
    }

    public function testSave(){
        $this->_truncateTable(new Student());

        $student=new Student();

        $student->id=1;
        $student->age=30;
        $student->name='manaphp';
        $this->assertTrue($student->save());

        $student=Student::findFirst(1);
        $this->assertNotEquals(false,$student);
        $this->assertTrue($student instanceof Student);
        $this->assertEquals('1',$student->id);
        $this->assertEquals('30',$student->age);
        $this->assertEquals('manaphp',$student->name);
    }
}
