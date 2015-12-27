<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2015/12/27
 * Time: 20:13
 */
defined('UNIT_TESTS_ROOT')||require 'bootstrap.php';
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

class Language extends \ManaPHP\Mvc\Model{
    public $language_id;
    public $name;
    public $last_update;

    public function getSource(){
        return 'language';
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

class MvcModelQueryBuilderTest extends TestCase{

    protected $di;
    /**
     * @var \ManaPHP\Mvc\Model\Manager
     */
    protected $modelsManager;

    public function setUp(){
        $this->di=new ManaPHP\Di();

        $this->di->set('modelsManager',function(){
            return new ManaPHP\Mvc\Model\Manager();
        });

        $this->di->set('modelsMetadata',function(){
            return new ManaPHP\Mvc\Model\MetaData\Memory();
        });

        $this->di->setShared('db',function(){
            $config= require 'config.database.php';
            $db =new ManaPHP\Db\Adapter\Mysql($config['mysql']);
            $db->attachEvent('db:beforeQuery',function($event,ManaPHP\DbInterface $source){
                var_dump($source->getSQLStatement());
                var_dump($source->getEmulatePrepareSQLStatement());
            });
            return $db;
        });
        $this->modelsManager=$this->di->get('modelsManager');
    }

    public function test_distinct(){
        $builder=$this->modelsManager->createBuilder()
            ->columns('city_id')
            ->addFrom('Address');
        $this->assertCount(603,$builder->getQuery()->execute());

        $builder=$this->modelsManager->createBuilder()
            ->columns('city_id')
            ->addFrom('Address')
            ->distinct(false);
        $this->assertCount(603,$builder->getQuery()->execute());

        $builder=$this->modelsManager->createBuilder()
             ->columns('city_id')
             ->addFrom('Address')
             ->distinct(true);
        $this->assertCount(599,$builder->getQuery()->execute());
    }

    public function test_columns(){
        //default all columns
        $builder=$this->modelsManager->createBuilder()
                ->addFrom('Address')
                ->limit(2);
        $rows=$builder->getQuery()->execute();
        $this->assertCount(2,$rows);
        $this->assertCount(8,$rows[0]);

        //all columns explicitly
        $builder=$this->modelsManager->createBuilder()
            ->columns('*')
            ->addFrom('Address')
            ->limit(2);
        $rows=$builder->getQuery()->execute();
        $this->assertCount(2,$rows);
        $this->assertCount(8,$rows[0]);

        // all columns explicitly and use table alias
        $builder=$this->modelsManager->createBuilder()
            ->columns('a.*')
            ->addFrom('Address','a')
            ->limit(2);
        $rows=$builder->getQuery()->execute();
        $this->assertCount(2,$rows);
        $this->assertCount(8,$rows[0]);

        $builder =$this->modelsManager->createBuilder()
            ->columns('a.address_id,a.address,a.phone')
            ->addFrom('Address','a')
            ->limit(2);
        $rows=$builder->getQuery()->execute();
        $this->assertCount(2,$rows);
        $this->assertCount(3,$rows[0]);

        $builder =$this->modelsManager->createBuilder()
            ->columns('a.address_id,a.address, c.city')
            ->addFrom('Address','a')
            ->leftJoin('City','c.city_id =a.city_id','c')
            ->limit(2)
            ->orderBy('a.address_id');
        $rows=$builder->getQuery()->execute();
        $this->assertCount(2,$rows);
        $this->assertCount(3,$rows[0]);
    }

    public function test_from(){
        $builder =$this->modelsManager->createBuilder()
            ->columns('address_id,address,phone')
            ->from('Address')
            ->limit(2);

        $rows=$builder->getQuery()->execute();
        $this->assertCount(2,$rows);
        $this->assertCount(3,$rows[0]);
    }

    public function test_addFrom(){
        $builder =$this->modelsManager->createBuilder()
            ->columns('address_id,address,phone')
            ->addFrom('Address')
            ->limit(2);

        $rows=$builder->getQuery()->execute();
        $this->assertCount(2,$rows);
        $this->assertCount(3,$rows[0]);

        $builder =$this->modelsManager->createBuilder()
            ->columns('a.address_id,a.address,a.phone')
            ->addFrom('Address','a')
            ->limit(2);

        $rows=$builder->getQuery()->execute();
        $this->assertCount(2,$rows);
        $this->assertCount(3,$rows[0]);
    }

    public function test_join(){
        $countCity=City::count();
        $this->assertEquals(600,$countCity);

        $countCountry=Country::count();
        $this->assertEquals(109,$countCountry);

        $builder=$this->modelsManager->createBuilder()
            ->columns('c1.*,c2.*')
            ->addFrom('City','c1')
            ->join('Country','','c2');
        $this->assertCount($countCity*$countCountry,$builder->getQuery()->execute());
    }

    public function test_innerJoin(){
        $countCity=City::count();
        $this->assertEquals(600,$countCity);

        $countCountry=Country::count();
        $this->assertEquals(109,$countCountry);

        $builder=$this->modelsManager->createBuilder()
            ->columns('c1.*,c2.*')
            ->addFrom('City','c1')
            ->innerJoin('Country','c1.city_id=c2.country_id','c2');
        $this->assertCount($countCountry,$builder->getQuery()->execute());
    }

    public function test_leftJoin(){
        $countCity=City::count();
        $this->assertEquals(600,$countCity);

        $countCountry=Country::count();
        $this->assertEquals(109,$countCountry);

        $builder=$this->modelsManager->createBuilder()
            ->columns('c1.*,c2.*')
            ->addFrom('City','c1')
            ->leftJoin('Country','c1.city_id=c2.country_id','c2');
        $this->assertCount($countCity,$builder->getQuery()->execute());
    }

    public function test_rightJoin(){
        $countCity=City::count();
        $this->assertEquals(600,$countCity);

        $countCountry=Country::count();
        $this->assertEquals(109,$countCountry);

        $builder=$this->modelsManager->createBuilder()
            ->columns('c1.*,c2.*')
            ->addFrom('City','c1')
            ->rightJoin('Country','c1.city_id=c2.country_id','c2');
        $this->assertCount($countCountry,$builder->getQuery()->execute());
    }

    public function test_where(){
        $builder =$this->modelsManager->createBuilder()
                ->where('address_id <=100')
                ->addFrom('Address');
        $this->assertCount(100,$builder->getQuery()->execute());

        $builder =$this->modelsManager->createBuilder()
            ->where('address_id <=:max_address_id',['max_address_id'=>100])
            ->addFrom('Address');
        $this->assertCount(100,$builder->getQuery()->execute());

        $builder =$this->modelsManager->createBuilder()
            ->where('address_id >=:min_address_id AND address_id <=:max_address_id',['min_address_id'=>51,'max_address_id'=>100])
            ->addFrom('Address');
        $this->assertCount(50,$builder->getQuery()->execute());
    }

    public function test_andWhere(){
        $builder =$this->modelsManager->createBuilder()
            ->andWhere('address_id <=100')
            ->addFrom('Address');
        $this->assertCount(100,$builder->getQuery()->execute());

        $builder =$this->modelsManager->createBuilder()
            ->andWhere('address_id <=:max_address_id',['max_address_id'=>100])
            ->addFrom('Address');
        $this->assertCount(100,$builder->getQuery()->execute());

        $builder =$this->modelsManager->createBuilder()
            ->andWhere('address_id >=:min_address_id',['min_address_id'=>51])
            ->andWhere('address_id <=:max_address_id',['max_address_id'=>100])
            ->addFrom('Address');
        $this->assertCount(50,$builder->getQuery()->execute());
    }

    public function test_orWhere(){
        $builder =$this->modelsManager->createBuilder()
            ->orWhere('address_id <=100')
            ->addFrom('Address');
        $this->assertCount(100,$builder->getQuery()->execute());

        $builder =$this->modelsManager->createBuilder()
            ->orWhere('address_id <=:max_address_id',['max_address_id'=>100])
            ->addFrom('Address');
        $this->assertCount(100,$builder->getQuery()->execute());

        $builder =$this->modelsManager->createBuilder()
            ->orWhere('address_id =:address_id1',['address_id1'=>100])
            ->orWhere('address_id =:address_id2',['address_id2'=>101])
            ->addFrom('Address');
        $this->assertCount(2,$builder->getQuery()->execute());
    }

    public function test_betweenWhere(){
        $builder =$this->modelsManager->createBuilder()
            ->betweenWhere('address_id',51,100)
            ->addFrom('Address');
        $this->assertCount(50,$builder->getQuery()->execute());

        $builder =$this->modelsManager->createBuilder()
            ->betweenWhere('address_id',51,100)
            ->betweenWhere('address_id',61,70)
            ->addFrom('Address');
        $this->assertCount(10,$builder->getQuery()->execute());
    }

    public function test_notBetweenWhere(){
        $builder =$this->modelsManager->createBuilder()
            ->notBetweenWhere('address_id',51,1000000)
            ->addFrom('Address');
        $this->assertCount(50,$builder->getQuery()->execute());

        $builder =$this->modelsManager->createBuilder()
            ->notBetweenWhere('address_id',51,1000000)
            ->notBetweenWhere('address_id',71,7000000)
            ->addFrom('Address');
        $this->assertCount(50,$builder->getQuery()->execute());
    }

    public function test_inWhere(){
        $builder =$this->modelsManager->createBuilder()
            ->inWhere('address_id',[])
            ->addFrom('Address');
        $this->assertCount(0,$builder->getQuery()->execute());

        $builder =$this->modelsManager->createBuilder()
            ->inWhere('address_id',[1])
            ->addFrom('Address');
        $this->assertCount(1,$builder->getQuery()->execute());

        $builder =$this->modelsManager->createBuilder()
            ->inWhere('address_id',[1,2,3,4,5])
            ->addFrom('Address');
        $this->assertCount(5,$builder->getQuery()->execute());

        $builder =$this->modelsManager->createBuilder()
            ->inWhere('address_id',[-3,-2,-1,0,1,2])
            ->addFrom('Address');
        $this->assertCount(2,$builder->getQuery()->execute());
    }

    public function test_orderBy(){
        $builder =$this->modelsManager->createBuilder()
            ->columns('address_id')
            ->addFrom('Address')
            ->where('address_id <=:max_address_id',['max_address_id'=>10])
            ->orderBy('address_id');
        $rows=$builder->getQuery()->execute();
        $this->assertCount(10,$builder->getQuery()->execute());

        /** @noinspection ForeachInvariantsInspection */
        for($i=0; $i<count($rows)-1;$i++){
            $this->assertTrue($rows[$i]['address_id'] <$rows[$i+1]['address_id']);
        }

        $builder =$this->modelsManager->createBuilder()
            ->columns('address_id')
            ->addFrom('Address')
            ->where('address_id <=:max_address_id',['max_address_id'=>10])
            ->orderBy('address_id ASC');
        $rows=$builder->getQuery()->execute();
        $this->assertCount(10,$builder->getQuery()->execute());

        /** @noinspection ForeachInvariantsInspection */
        for($i=0; $i<count($rows)-1;$i++){
            $this->assertTrue($rows[$i]['address_id'] <$rows[$i+1]['address_id']);
        }

        $builder =$this->modelsManager->createBuilder()
            ->columns('address_id')
            ->addFrom('Address')
            ->where('address_id <=:max_address_id',['max_address_id'=>10])
            ->orderBy('address_id DESC');
        $rows=$builder->getQuery()->execute();
        $this->assertCount(10,$builder->getQuery()->execute());

        /** @noinspection ForeachInvariantsInspection */
        for($i=0; $i<count($rows)-1;$i++){
            $this->assertTrue($rows[$i]['address_id'] >$rows[$i+1]['address_id']);
        }
    }

    public function test_having(){
        $builder=$this->modelsManager->createBuilder()
                ->columns('COUNT(city_id) as count_city, country_id')
                ->addFrom('City')
                ->groupBy('country_id')
                ->having('COUNT(city_id) >1');
        $rows=$builder->getQuery()->execute();
        $this->assertCount(67,$rows);
        foreach($rows as $row){
            $this->assertTrue($row['count_city']>1);
        }

        $builder=$this->modelsManager->createBuilder()
            ->columns('COUNT(city_id) as count_city, country_id')
            ->addFrom('City')
            ->groupBy('country_id')
            ->having('COUNT(city_id) >1')
            ->having('COUNT(city_id) <7');
        $rows=$builder->getQuery()->execute();
        $this->assertCount(46,$rows);
        foreach($rows as $row){
            $this->assertTrue($row['count_city']>1);
            $this->assertTrue($row['count_city']<7);
        }

        $builder=$this->modelsManager->createBuilder()
            ->columns('COUNT(city_id) as count_city, country_id')
            ->addFrom('City')
            ->groupBy('country_id')
            ->having('COUNT(city_id) >:min_count',['min_count'=>1])
            ->having('COUNT(city_id) <:max_count',['max_count'=>7]);
        $rows=$builder->getQuery()->execute();
        $this->assertCount(46,$rows);
    }

    public function test_limit(){
        //limit without offset
        $builder=$this->modelsManager->createBuilder()
            ->columns('city_id')
            ->addFrom('City')
            ->limit(1);
        $this->assertCount(1,$builder->getQuery()->execute());

        //limit with offset
        $builder=$this->modelsManager->createBuilder()
            ->columns('city_id')
            ->addFrom('City')
            ->orderBy('city_id')
            ->limit(10,20);

        $rows=$builder->getQuery()->execute();
        $this->assertCount(10,$rows);
        $this->assertEquals(21,$rows[0]['city_id']);
        $this->assertEquals(30,$rows[9]['city_id']);

        //there is no error during limiting equal to 0
        $builder=$this->modelsManager->createBuilder()
            ->columns('city_id')
            ->addFrom('City')
            ->limit(0);
        $this->assertCount(0,$builder->getQuery()->execute());
    }

    public function test_offset(){
        $builder=$this->modelsManager->createBuilder()
            ->columns('city_id')
            ->addFrom('City')
            ->andWhere('city_id <:city_id',['city_id'=>10])
            ->limit(2)
            ->offset(5);

        $rows =$builder->getQuery()->execute();
        $this->assertCount(2,$rows);
        $this->assertEquals(6,$rows[0]['city_id']);
    }

    public function test_groupBy(){
        $builder=$this->modelsManager->createBuilder()
            ->columns('COUNT(city_id) as count_city, country_id')
            ->addFrom('City')
            ->groupBy('country_id');
        $rows=$builder->getQuery()->execute();
        $this->assertCount(109,$rows);

        $builder=$this->modelsManager->createBuilder()
            ->columns('COUNT(payment_id) AS payment_times, customer_id, amount')
            ->addFrom('Payment')
            ->groupBy('customer_id,amount');
        $rows=$builder->getQuery()->execute();
        $this->assertCount(4812,$rows);
    }

    public function test_notInWhere(){
        $rowAddress=Address::count();
        $this->assertEquals(603,$rowAddress);

        $builder =$this->modelsManager->createBuilder()
            ->notInWhere('address_id',[])
            ->addFrom('Address');
        $this->assertCount(603,$builder->getQuery()->execute());

        $builder =$this->modelsManager->createBuilder()
            ->notInWhere('address_id',[1])
            ->addFrom('Address');
        $this->assertCount(602,$builder->getQuery()->execute());

        $builder =$this->modelsManager->createBuilder()
            ->notInWhere('address_id',[1,2,3])
            ->addFrom('Address');
        $this->assertCount(600,$builder->getQuery()->execute());

        $builder =$this->modelsManager->createBuilder()
            ->notInWhere('address_id',[-3,-2,-1,0,1,2])
            ->addFrom('Address');
        $this->assertCount(601,$builder->getQuery()->execute());
    }
}