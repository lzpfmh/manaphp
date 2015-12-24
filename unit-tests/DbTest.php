<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2015/12/23
 * Time: 21:36
 */
defined('UNIT_TESTS_ROOT')||require 'bootstrap.php';

class DbTest extends TestCase{
    /**
     * @var \ManaPHP\DbInterface
     */
    protected $db;

    public function setUp(){
        $config= require 'config.database.php';
        $this->db=new ManaPHP\Db\Adapter\Mysql($config['mysql']);
        $this->db->attachEvent('db:beforeQuery',function($event,$source,$data){
           var_dump(['sql'=>$source->getRealSQLStatement(),'bind'=>$source->getSQLBindParams()]);
        });
    }

    public function test_query(){
        $result=$this->db->query('SELECT * FROM city LIMIT 3');
        $this->assertTrue(is_object($result));
        $this->assertInstanceOf('\PDOStatement',$result);
        for($i=0; $i<3; $i++){
            $row=$result->fetch();
            var_dump($row);
            $this->assertCount(4,$row);
        }

        $row =$result->fetch();
        $this->assertFalse($row);

        $result=$this->db->query('SELECT * FROM city LIMIT 5');
        $this->assertTrue(is_object($result));
        $rowCount=0;
        while($row =$result->fetch()){
            $rowCount++;
        }
        $this->assertEquals(5, $rowCount);

        $result=$this->db->query('SELECT * FROM city LIMIT 5');
        $result->setFetchMode(PDO::FETCH_NUM);
        $row=$result->fetch();
        $this->assertTrue(is_array($row));
        $this->assertCount(4,$row);
        $this->assertTrue(isset($row[0]));
        $this->assertFalse(isset($row['city']));

        $result=$this->db->query('SELECT * FROM city LIMIT 5');
        $result->setFetchMode(PDO::FETCH_ASSOC);
        $row=$result->fetch();
        $this->assertTrue(is_array($row));
        $this->assertCount(4,$row);
        $this->assertFalse(isset($row[0]));
        $this->assertTrue(isset($row['city']));

        $result=$this->db->query('SELECT * FROM city LIMIT 5');
        $result->setFetchMode(PDO::FETCH_OBJ);
        $row=$result->fetch();
        $this->assertTrue(is_object($row));
        $this->assertTrue(isset($row->city));
    }

    public function test_execute(){
        $success=$this->db->execute('TRUNCATE TABLE _student');
        $this->assertTrue($success);

        $success=$this->db->execute('INSERT INTO _student(id,age,name) VALUES(?,?,?)',[1,20,'mana']);
        $this->assertTrue($success);

        $success =$this->db->execute('UPDATE _student set age=?, name=?',[22,'mana2']);
        $this->assertTrue($success);

        $success =$this->db->execute('DELETE FROM _student WHERE id=?',[1]);
        $this->assertTrue($success);

        $success=$this->db->execute('TRUNCATE TABLE _student');
        $this->assertTrue($success);

        $success=$this->db->execute('INSERT INTO _student(id,age,name) VALUES(:id,:age,:name)',['id'=>11,'age'=>220,'name'=>'mana2']);
        $this->assertTrue($success);

        $success =$this->db->execute('UPDATE _student set age=:age, name=:name',['age'=>22,'name'=>'mana2']);
        $this->assertTrue($success);

        $success =$this->db->execute('DELETE FROM _student WHERE id=:id',['id'=>1]);
        $this->assertTrue($success);
    }

    public function test_insert(){
        $success=$this->db->execute('TRUNCATE TABLE _student');
        $this->assertTrue($success);

        $success=$this->db->insert('_student',[1,2,'mana'],['id','age','name']);
        $this->assertTrue($success);

        $success=$this->db->insert('_student',[2,2,'mana'],['id','age','name']);
        $this->assertTrue($success);

        for($i =0; $i<50; $i++){
            $success =$this->db->insert('_student',[$i,'mana'.$i],['age','name']);
            $this->assertTrue($success);
        }

    }


}