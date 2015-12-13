<?php 

namespace ManaPHP\Db {

	use ManaPHP\Db;
	use \ManaPHP\Events\EventsAwareInterface;

	/**
	 * ManaPHP\Db\Adapter
	 *
	 * Base class for ManaPHP\Db adapters
	 */
	
	class Adapter implements EventsAwareInterface, AdapterInterface {

		/**
		 * Event Manager
		 *
		 * @var \ManaPHP\Events\ManagerInterface
		 */
		protected $_eventsManager;

		/**
		 * Descriptor used to connect to a database
		 *
		 * @var array
		 */
		protected $_descriptor;

		/**
		 * Type of database system driver is used for
		 *
		 * @var string
		 */
		protected $_type;

		/**
		 * Active SQL Statement
		 *
		 * @var string
		 */
		protected $_sqlStatement;

		/**
		 * Active SQL bound parameter variables
		 *
		 * @var array
		 */
		protected $_sqlVariables;

		/**
		 * Active SQL Bind Types
		 *
		 * @var array
		 */
		protected $_sqlBindTypes;

		/**
		 * Current transaction level
		 */
		protected $_transactionLevel = 0;

		/**
		 * @var \PDO
		 */
		protected $_pdo;

		/**
		 * Last affected rows
		 * @var int
		 */
		protected $_affectedRows;

		/**
		 * \ManaPHP\Db\Adapter constructor
		 *
		 * @param array $descriptor
		 */
		public function __construct($descriptor){
			$this->_type ='mysql';
			$this->_descriptor =$descriptor;
			$this->connect();
		}


		/**
		 * Sets the event manager
		 *
		 * @param \ManaPHP\Events\ManagerInterface $eventsManager
		 */
		public function setEventsManager($eventsManager){
			$this->_eventsManager =$eventsManager;
		}


		/**
		 * Returns the internal event manager
		 *
		 * @return \ManaPHP\Events\ManagerInterface
		 */
		public function getEventsManager(){
			return $this->_eventsManager;
		}

		/**
		 * This method is automatically called in Phalcon\Db\Adapter\Pdo constructor.
		 * Call it when you need to restore a database connection
		 *
		 *<code>
		 * //Make a connection
		 * $connection = new \Phalcon\Db\Adapter\Pdo\Mysql(array(
		 *  'host' => '192.168.0.11',
		 *  'username' => 'sigma',
		 *  'password' => 'secret',
		 *  'dbname' => 'blog',
		 * ));
		 *
		 * //Reconnect
		 * $connection->connect();
		 * </code>
		 *
		 * @return 	boolean
		 */
		protected function connect(){
			$descriptor =$this->_descriptor;

			$username =isset($descriptor['username'])?$descriptor['username']:null;
			$password =isset($descriptor['password'])?$descriptor['password']:null;
			$options =isset($descriptor['options'])?$descriptor['options']:[];
			unset($descriptor['username'],$descriptor['password'],$descriptor['options']);

			if(isset($descriptor['dsn'])){
				$dsn =$descriptor['dsn'];
			}else{
				$dsn_parts=[];
				foreach($descriptor as $k=>$v){
					$dsn_parts[]=$k.'='.$v;
				}
				$dsn=implode(';',$dsn_parts);
			}

 			$options[\PDO::ATTR_ERRMODE]=\PDO::ERRMODE_EXCEPTION;
			$this->_pdo=new \PDO($this->_type.':'.$dsn, $username, $password, $options);
		}

		/**
		 * Returns a PDO prepared statement to be executed with 'executePrepared'
		 *
		 *<code>
		 * $statement = $db->prepare('SELECT * FROM robots WHERE name = :name');
		 * $result = $connection->executePrepared($statement, array('name' => 'Voltron'));
		 *</code>
		 *
		 * @param string $sqlStatement
		 * @return \PDOStatement
		 */
		public function prepare($sqlStatement)
		{
			return $this->_pdo->prepare($sqlStatement);
		}

		/**
		 * Executes a prepared statement binding. This function uses integer indexes starting from zero
		 *
		 *<code>
		 * $statement = $db->prepare('SELECT * FROM robots WHERE name = :name');
		 * $result = $connection->executePrepared($statement, array('name' => 'Voltron'));
		 *</code>
		 *
		 * @param \PDOStatement statement
		 * @param array $placeholders
		 * @param array $dataTypes
		 * @return \PDOStatement
		 * @throws \ManaPHP\Db\Exception
		 */
		public function executePrepared($statement, $placeholders, $dataTypes)
		{
			foreach($placeholders as $parameter=>$v){
				if(!is_string($parameter)){
					throw new Exception('Invalid bind parameter: '.$parameter);
				}

				if(is_array($dataTypes)){
					throw new Exception('dataTypes not support');
				}

				if(!is_array($v)){
					if(isset($dataTypes[$parameter])){
						$data_type =$dataTypes[$parameter];
					}else{
						if(is_int($v)){
							$data_type =\PDO::PARAM_INT;
						}else{
							$data_type=\PDO::PARAM_STR;
						}
					}

					$statement->bindValue($parameter,$v,$data_type);
				}else{
					throw new Exception('array data bind not support: '.$parameter);
				}
			}

			$statement->execute();
			return $statement;
		}


		/**
		 * Sends SQL statements to the database server returning the success state.
		 * Use this method only when the SQL statement sent to the server is returning rows
		 *
		 *<code>
		 *	//Querying data
		 *	$resultset = $connection->query("SELECT * FROM robots WHERE type='mechanical'");
		 *	$resultset = $connection->query("SELECT * FROM robots WHERE type=?", array("mechanical"));
		 *</code>
		 *
		 * @param string $sqlStatement
		 * @param array $bindParams
		 * @param array $bindTypes
		 * @return \PdoStatement
		 * @throws \ManaPHP\Db\Exception
		 */
		public function query($sqlStatement, $bindParams = null, $bindTypes = null){
			$this->_sqlStatement = $sqlStatement;
			$this->_sqlVariables = $bindParams;
			$this->_sqlBindTypes = $bindTypes;

			if(is_object($this->_eventsManager)) {
				if($this->_eventsManager->fire('db:beforeQuery',$this,$bindParams) ===false){
					return false;
				}
			}

			if(is_array($bindParams)){
				$statement =$this->_pdo->prepare($sqlStatement);

				if(is_object($statement)){
					$statement =$this->executePrepared($statement,$bindParams,$bindTypes);
				}
			}else{
				try{
					$statement =$this->_pdo->query($sqlStatement);
				}catch (\PDOException $e){
					throw new Exception($e->getMessage());
				}

			}

			if(is_object($statement)){
				if(is_object($this->_eventsManager)){
					$this->_eventsManager->fire('db:afterQuery',$this,$bindParams);
				}

				return $statement;
			}

			return $statement;
		}

		/**
		 * Sends SQL statements to the database server returning the success state.
		 * Use this method only when the SQL statement sent to the server doesn't return any rows
		 *
		 *<code>
		 *	//Inserting data
		 *	$success = $connection->execute("INSERT INTO robots VALUES (1, 'Astro Boy')");
		 *	$success = $connection->execute("INSERT INTO robots VALUES (?, ?)", array(1, 'Astro Boy'));
		 *</code>
		 * @param string $sqlStatement
		 * @param array $bindParams
		 * @param array $bindTypes
		 * @return int
		 * @throws \ManaPHP\Db\Exception
		 */
		public function execute($sqlStatement, $bindParams = null, $bindTypes = null){
			$this->_sqlStatement =$sqlStatement;
			$this->_sqlVariables =$bindParams;
			$this->_sqlBindTypes =$bindTypes;

			if(is_object($this->_eventsManager)){
				$this->_eventsManager->fire('db:beforeQuery',$this,$bindParams);
			}

			$this->_affectedRows=0;

			if(is_array($bindParams)){
				$statement =$this->_pdo->prepare($sqlStatement);
				if(is_object($statement)){
					$newStatement=$this->executePrepared($statement,$bindParams,$bindTypes);
					$this->_affectedRows =$newStatement->rowCount();
				}
			}else{
				$this->_affectedRows =$this->_pdo->exec($sqlStatement);
			}

			if(is_int($this->_affectedRows)){
				if(is_object($this->_eventsManager)){
					$this->_eventsManager->fire('db:afterQuery',$this,$bindParams);
				}
			}

			return $this->_affectedRows;
		}

		/**
		 * Escapes a column/table/schema name
		 *
		 * <code>
		 * echo $connection->escapeIdentifier('my_table'); // `my_table`
		 * echo $connection->escapeIdentifier(['companies', 'name']); // `companies`.`name`
		 * <code>
		 *
		 * @param string|array identifier
		 * @return string
		 */
		public function escapeIdentifier($identifier){
			if(is_array($identifier)){
				return '`'.$identifier[0].'`.`'.$identifier[1].'`';
			}else{
				return '`'.$identifier.'`';
			}
		}
		/**
		 * Returns the number of affected rows by the last INSERT/UPDATE/DELETE reported by the database system
		 *
		 * @return int
		 */
		public function affectedRows(){
			return $this->_affectedRows;
		}
		/**
		 * Returns the first row in a SQL query result
		 *
		 *<code>
		 *	//Getting first robot
		 *	$robot = $connection->fetchOne("SELECT * FROM robots");
		 *	print_r($robot);
		 *
		 *	//Getting first robot with associative indexes only
		 *	$robot = $connection->fetchOne("SELECT * FROM robots", \ManaPHP\Db::FETCH_ASSOC);
		 *	print_r($robot);
		 *</code>
		 *
		 * @param string $sqlQuery
		 * @param int $fetchMode
		 * @param array $bindParams
		 * @param array $bindTypes
		 * @throws \ManaPHP\Db\Exception
		 * @return array
		 */
		public function fetchOne($sqlQuery, $fetchMode=\PDO::FETCH_ASSOC, $bindParams=null,$bindTypes=null){
			$result =$this->query($sqlQuery, $bindParams, $bindTypes);
			if(is_object($result)){
				$result->setFetchMode($fetchMode);
				return $result->fetch();
			}else{
				return [];
			}
		}


		/**
		 * Dumps the complete result of a query into an array
		 *
		 *<code>
		 *	//Getting all robots with associative indexes only
		 *	$robots = $connection->fetchAll("SELECT * FROM robots", \ManaPHP\Db::FETCH_ASSOC);
		 *	foreach ($robots as $robot) {
		 *		print_r($robot);
		 *	}
		 *
		 *  //Getting all robots that contains word "robot" withing the name
		 *  $robots = $connection->fetchAll("SELECT * FROM robots WHERE name LIKE :name",
		 *		ManaPHP\Db::FETCH_ASSOC,
		 *		array('name' => '%robot%')
		 *  );
		 *	foreach($robots as $robot){
		 *		print_r($robot);
		 *	}
		 *</code>
		 *
		 * @param string $sqlQuery
		 * @param int $fetchMode
		 * @param array $bindParams
		 * @param array $bindTypes
		 * @throws \ManaPHP\Db\Exception
		 * @return array
		 */
		public function fetchAll($sqlQuery, $fetchMode=\PDO::FETCH_ASSOC, $bindParams=null,$bindTypes=null){
			$results =[];
			$result =$this->query($sqlQuery,$bindParams, $bindTypes);
			if(is_object($result)){
				$result->setFetchMode($fetchMode);
			}

			while(true){
				$row =$result->fetch();
				if($row ===false){
					break;
				}else{
					$results[]=$row;
				}
			}

			return $results;
		}


		/**
		 * Inserts data into a table using custom RBDMS SQL syntax
		 *
		 * <code>
		 * //Inserting a new robot
		 * $success = $connection->insert(
		 *     "robots",
		 *     array("Astro Boy", 1952),
		 *     array("name", "year")
		 * );
		 *
		 * //Next SQL sentence is sent to the database system
		 * INSERT INTO `robots` (`name`, `year`) VALUES ("Astro boy", 1952);
		 * </code>
		 *
		 * @param 	string $table
		 * @param 	array $values
		 * @param 	array $fields
		 * @param 	array $dataTypes
		 * @return 	boolean
		 * @throws \ManaPHP\Db\Exception
		 */
		public function insert($table, $values, $fields=null, $dataTypes=null){
			if(count($values) ===0){
				throw new Exception('Unable to insert into ' . $table . ' without data');
			}

			if($fields !==null &&count($values) !==count($fields)){
				throw new Exception('The number of values in the insert is not the same as fields');
			}

			$value_parts=[];
			$bindParams=[];

			foreach($values as $k=>$v){
				if(is_object($v)){
					$v =(string)$v;
				}

				if($fields ===null){
					$value_parts[]='?';
					$bindParams[$k+1]=$v;
				}else{
					$bindKey =':'.$fields[$k];
					$value_parts[]=$bindKey;
					$bindParams[$bindKey]=$v;
				}
			}

			if(is_array($fields)){
				$field_parts=[];

				foreach($fields as $field){
					$field_parts[]=$this->escapeIdentifier($field);
				}

				$insertSql='INSERT INTO '. $this->escapeIdentifier($table).' ('. implode(', ',$field_parts).') VALUES ('. implode(',', $value_parts) .')';
			}else{
				$insertSql ='INSERT INTO '.$this->escapeIdentifier($table).' VALUES ('. implode(', ',$value_parts).')';
			}

			$affectRows=$this->execute($insertSql,$bindParams) ;
			if(is_int($affectRows)){
				return $affectRows>0;
			}else{
				return $affectRows;
			}
		}


		/**
		 * Updates data on a table using custom RBDMS SQL syntax
		 *
		 * <code>
		 * //Updating existing robot
		 * $success = $connection->update(
		 *     "robots",
		 *     array("name"),
		 *     array("New Astro Boy"),
		 *     "id = 101"
		 * );
		 *
		 * //Next SQL sentence is sent to the database system
		 * UPDATE `robots` SET `name` = "Astro boy" WHERE id = 101
		 * </code>
		 *
		 * @param 	string $table
		 * @param 	array $fields
		 * @param 	array $values
		 * @param 	string $whereCondition
		 * @param 	array $dataTypes
		 * @return 	boolean
		 * @throws \ManaPHP\Db\Exception
		 */
		public function update($table, $fields, $values, $whereCondition=null, $dataTypes=null){
			if(count($fields) !==count($values)){
				throw new Exception('The number of values in the update is not the same as fields');
			}

		}


		/**
		 * Deletes data from a table using custom RBDMS SQL syntax
		 *
		 * <code>
		 * //Deleting existing robot
		 * $success = $connection->delete(
		 *     "robots",
		 *     "id = 101"
		 * );
		 *
		 * //Next SQL sentence is generated
		 * DELETE FROM `robots` WHERE `id` = 101
		 * </code>
		 *
		 * @param  string $table
		 * @param  string $whereCondition
		 * @param  array $placeholders
		 * @param  array $dataTypes
		 * @return boolean
		 * @throws \ManaPHP\Db\Exception
		 */
		public function delete($table, $whereCondition, $placeholders=null, $dataTypes=null){
			$escapedTable=$this->escapeIdentifier($table);

			if($whereCondition ==='' ||$whereCondition===null){
				throw new Exception('Danger DELETE \''. $escapedTable.'\'operation without any condition');
			}

			$sql ='DELETE FROM '.$this->escapeIdentifier($table).' WHERE '.$whereCondition;

			return $this->execute($sql,$placeholders,$dataTypes);
		}


		/**
		 * Appends a LIMIT clause to $sqlQuery argument
		 *
		 * <code>
		 * 	echo $connection->limit("SELECT * FROM robots", 5);
		 * </code>
		 *
		 * @param  	string $sqlQuery
		 * @param 	int $number
		 * @param   int $offset
		 * @return 	string
		 */
		public function limit($sqlQuery, $number,$offset=null){
			return $sqlQuery.' LIMIT '.$number.($offset ===null?'':(' OFFSET '.$offset));
		}


		/**
		 * Returns a SQL modified with a FOR UPDATE clause
		 *
		 * @param string $sqlQuery
		 * @return string
		 */
		public function forUpdate($sqlQuery){
			return $sqlQuery.' FOR UPDATE';
		}


		/**
		 * Returns a SQL modified with a LOCK IN SHARE MODE clause
		 *
		 * @param string $sqlQuery
		 * @return string
		 */
		public function sharedLock($sqlQuery){
			return $sqlQuery .' LOCK IN SHARE MODE';
		}


		/**
		 * Active SQL statement in the object
		 *
		 * @return string
		 */
		public function getSQLStatement(){
			return $this->_sqlStatement;
		}

		/**
		 * Active SQL statement in the object
		 *
		 * @return array
		 */
		public function getSQLVariables(){
			return $this->_sqlStatement;
		}


		/**
		 * Active SQL statement in the object
		 *
		 * @return array
		 */
		public function getSQLBindTypes(){
			return $this->_sqlBindTypes;
		}


		/**
		 * Starts a transaction in the connection
		 *
		 * @return boolean
		 * @throws \ManaPHP\Db\Exception
		 */
		public function begin(){
			if($this->_transactionLevel !==0){
				throw new Exception('There is in a active transaction already.');
			}

			if(is_object($this->_eventsManager)){
				$this->_eventsManager->fire('db:beginTransaction',$this);
			}

			$this->_transactionLevel++;
			return $this->_pdo->beginTransaction();
		}

		/**
		 * Checks whether the connection is under a transaction
		 *
		 *<code>
		 *	$connection->begin();
		 *	var_dump($connection->isUnderTransaction()); //true
		 *</code>
		 * @return bool
		 */
		public function isUnderTransaction(){
			return $this->_pdo->inTransaction();
		}

		/**
		 * Rollbacks the active transaction in the connection
		 *
		 * @return boolean
		 * @throws \ManaPHP\Db\Exception
		 */
		public function rollback(){
			if($this->_transactionLevel ===0){
				throw new Exception('There is no active transaction');
			}

			if(is_object($this->_eventsManager)){
				$this->_eventsManager->fire('db:rollbackTransaction',$this);
			}

			$this->_transactionLevel--;
			return $this->_pdo->rollBack();
		}


		/**
		 * Commits the active transaction in the connection
		 *
		 * @return boolean
		 * @throws \ManaPHP\Db\Exception
		 */
		public function commit(){
			if($this->_transactionLevel ===0){
				throw new Exception('There is no active transaction');
			}

			if(is_object($this->_eventsManager)){
				$this->_eventsManager->fire('db:commitTransaction',$this);
			}

			$this->_transactionLevel--;
			return $this->_pdo->commit();
		}

		/**
		 * Returns insert id for the auto_increment column inserted in the last SQL statement
		 *
		 * @param string $sequenceName
		 * @return int
		 */
		public function lastInsertId($sequenceName=null){
			return $this->_pdo->lastInsertId($sequenceName);
		}

		/**
		 * Active SQL statement in the object without replace bound parameters
		 *
		 * @return string
		 */
		public function getRealSQLStatement(){
			return $this->_sqlStatement;
		}



		/**
		 * Return internal PDO handler
		 *
		 * @return \PDO
		 */
		public function getInternalHandler(){
			return $this->_pdo;
		}
	}

}
