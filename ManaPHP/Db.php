<?php 

namespace ManaPHP {

	use ManaPHP\Db\ConditionParser;
	use ManaPHP\Db\Exception;
	use ManaPHP\Db\PrepareEmulation;
	use ManaPHP\Events\EventsAware;
	use ManaPHP\Events\EventsAwareInterface;

	class Db implements EventsAwareInterface, DbInterface {
		use EventsAware;
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
		protected $_sqlBindParams;

		/**
		 * Active SQL Bind Types
		 *
		 * @var array
		 */
		protected $_sqlBindTypes;

		/**
		 * Current transaction level
		 * @var int
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
			$this->_connect();
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
		protected function _connect(){
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
		 * Executes a prepared statement binding. This function uses integer indexes starting from zero
		 *
		 *<code>
		 * $statement = $db->prepare('SELECT * FROM robots WHERE name = :name');
		 * $result = $connection->executePrepared($statement, array('name' => 'Voltron'));
		 *</code>
		 *
		 * @param \PDOStatement statement
		 * @param array $bindParams
		 * @param array $bindTypes
		 * @return \PDOStatement
		 * @throws \ManaPHP\Db\Exception
		 */
		protected function _executePrepared($statement, $bindParams, $bindTypes)
		{
			foreach($bindParams as $parameter=>$value){
				if(is_int($parameter)){
					$statement->bindValue($parameter+1, $value, $bindTypes[$parameter]);
				}else{
					$statement->bindValue($parameter, $value, $bindTypes[$parameter]);
				}
			}

			$statement->execute();

			return $statement;
		}


		protected function _parseBinds($binds, &$bindParams, &$bindTypes){
			$bindParams=null;
			$bindTypes=null;

			if($binds ===null ||count($binds) ===0){
				return ;
			}

			$bindParams=[];
			$bindTypes=[];

			foreach($binds as $key=>$value){
				if(is_int($key)){
					$finalKey=$key;
				}else if(is_string($key)){
					$finalKey=($key[0] ===':')?$key:(':'.$key);
				}else{
					throw new Exception('invalid binds field: '.json_encode($key));
				}

				if(is_scalar($value) ||$value===null){
					$data=$value;
					$type=null;
				}else if(is_array($value)){
					if(count($value) ===1){
						$data=$value[0];
						$type=null;
					}else if(count($value) ===2){
						$data=$value[0];
						$type=$value[1];
					}else{
						throw new Exception('one value of binds has invalid values: '.json_encode($value));
					}
				}else{
					throw new Exception('one value of binds has invalid value: '.json_encode($value));
				}

				if($type ===null){
					if(is_string($data)){
						$type =\PDO::PARAM_STR;
					}else if(is_int($data)){
						$type =\PDO::PARAM_INT;
					}else if(is_bool($data)){
						$type=\PDO::PARAM_BOOL;
					}else if($data ===null){
						$type=\PDO::PARAM_NULL;
					}else{
						$type=\PDO::PARAM_STR;
					}
				}

				$bindParams[$finalKey]=$data;
				$bindTypes[$finalKey]=$type;
			}
		}

		protected function _parseColumns($binds, &$columns,&$escapedColumns){
			$columns=null;
			$escapedColumns=null;
			if($binds ===null){
				return ;
			}

			$columns=[];
			$escapedColumns=[];
			foreach($binds as $key=>$value){
				if(is_int($key)){
					$finalKey=$key;
				}else if(is_string($key)){
					$finalKey=($key[0] ===':')?substr($key,1):$key;
				}else{
					throw new Exception('invalid binds field: '.json_encode($key));
				}

				$columns[]=$finalKey;
				$escapedColumns[]='`'.$finalKey.'`';
			}
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
		 * @param array $binds
		 * @param int $fetchMode
		 * @return \PdoStatement
		 * @throws \ManaPHP\Db\Exception
		 */
		public function query($sqlStatement, $binds=null, $fetchMode=\PDO::FETCH_ASSOC){
			$this->_parseBinds($binds,$bindParams, $bindTypes);

			$this->_sqlStatement = $sqlStatement;
			$this->_sqlBindParams = $bindParams;
			$this->_sqlBindTypes = $bindTypes;

			if($this->fireEvent('db:beforeQuery',$this) ===false){
				return false;
			}

			try{
				if($bindParams !==null){
					$statement =$this->_pdo->prepare($sqlStatement);
					$statement =$this->_executePrepared($statement,$bindParams,$bindTypes);
				}else{
					$statement =$this->_pdo->query($sqlStatement);
				}

				$statement->setFetchMode($fetchMode);
			}catch (\PDOException $e){
				throw new Exception($e->getMessage());
			}

			$this->fireEvent('db:afterQuery',$this);

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
		 * @param array $binds
		 * @return int
		 * @throws \ManaPHP\Db\Exception
		 */
		public function execute($sqlStatement, $binds=null){
			$this->_parseBinds($binds,$bindParams, $bindTypes);

			$this->_sqlStatement =$sqlStatement;
			$this->_sqlBindParams =$bindParams;
			$this->_sqlBindTypes =$bindTypes;

			$this->_affectedRows=0;

			$this->fireEvent('db:beforeQuery',$this);

			try{
				if($bindParams !==null){
					$statement =$this->_pdo->prepare($sqlStatement);
					$newStatement=$this->_executePrepared($statement,$bindParams,$bindTypes);
					$this->_affectedRows =$newStatement->rowCount();
				}else{
					$this->_affectedRows =$this->_pdo->exec($sqlStatement);
				}
			}catch (\PDOException $e){
				throw new Exception($e->getMessage());
			}

			if(is_int($this->_affectedRows)){
				$this->fireEvent('db:afterQuery',$this);
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
		 * Escapes a column/table/schema name
		 *
		 * <code>
		 * echo $connection->escapeIdentifier('my_table'); // `my_table`
		 * echo $connection->escapeIdentifier(['companies', 'name']); // `companies`.`name`
		 * <code>
		 *
		 * @param array $identifiers
		 * @return string
		 */
		public function _escapeIdentifiers($identifiers){
			$escaped_identifiers=[];
			foreach($identifiers as $identifier){
				$escaped_identifiers[]='`'.$identifier.'`';
			}

			return $escaped_identifiers;
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
		 * @param array $binds
		 * @param int $fetchMode
		 * @throws \ManaPHP\Db\Exception
		 * @return array|false
		 */
		public function fetchOne($sqlQuery,$binds=null,$fetchMode=\PDO::FETCH_ASSOC){
			$result =$this->query($sqlQuery, $binds, $fetchMode);
			return $result->fetch();
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
		 * @param array $binds
		 * @param int $fetchMode
		 * @throws \ManaPHP\Db\Exception
		 * @return array
		 */
		public function fetchAll($sqlQuery, $binds=null, $fetchMode=\PDO::FETCH_ASSOC){
			$result =$this->query($sqlQuery,$binds, $fetchMode);
			return $result->fetchAll();
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
		 * @param 	array $columnValues
		 * @return 	boolean
		 * @throws \ManaPHP\Db\Exception
		 */
		public function insert($table, $columnValues){
			if(count($columnValues) ===0){
				throw new Exception('Unable to insert into ' . $table . ' without data');
			}

			if(isset($columnValues[0])){
				$insertSql ='INSERT INTO '.$this->escapeIdentifier($table).
								' VALUES ('. rtrim(str_repeat('?,',count($columnValues)),',').')';
				return $this->execute($insertSql,$columnValues)===1;
			}else{
				$this->_parseColumns($columnValues,$columns,$escapedColumns);
				$insertSql='INSERT INTO '. $this->escapeIdentifier($table).
							' ('. implode(',',$escapedColumns).
							') VALUES (:'. implode(',:', $columns) .')';
				return $this->execute($insertSql,$columnValues)===1;
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
		 * @param 	array $columnValues
		 * @param 	string $whereCondition
		 * @return 	boolean
		 * @throws \ManaPHP\Db\Exception
		 */
		public function update($table, $whereCondition=null, $columnValues){
			$escapedTable=$this->escapeIdentifier($table);

			if(count($columnValues) ===0){
				throw new Exception('Unable to update ' . $table . ' without data');
			}

			if($whereCondition ==='' ||$whereCondition===null){
				throw new Exception('Danger DELETE \''. $escapedTable.'\'operation without any condition');
			}

			$where=(new ConditionParser())->parse($whereCondition,$binds);

			$this->_parseColumns($columnValues,$columns,$escapedColumns);
			$setColumns=[];
			foreach($columns as $key=>$column){
				$setColumns[]=$escapedColumns[$key].'=:'.$column;
			}
			$updateSql='UPDATE '. $this->escapeIdentifier($table). ' SET '.implode(',', $setColumns).' WHERE '. $where;

			$columnValues=array_merge($columnValues,$binds);

			return $this->execute($updateSql,$columnValues);
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
		 * @param  array $binds
		 * @return boolean
		 * @throws \ManaPHP\Db\Exception
		 */
		public function delete($table, $whereCondition, $binds=null){
			$escapedTable=$this->escapeIdentifier($table);

			if($whereCondition ==='' ||$whereCondition===null){
				throw new Exception('Danger DELETE \''. $escapedTable.'\'operation without any condition');
			}

			$sql ='DELETE FROM '.$this->escapeIdentifier($table).' WHERE '.$whereCondition;

			return $this->execute($sql, $binds);
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
		public function limit($sqlQuery, $number, $offset=null){
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
		 * Active SQL statement in the object with replace the bind with value
		 *
		 * @param int $preservedStrLength
		 * @return string
		 * @throws \ManaPHP\Db\Exception
		 */
		public function getEmulatePrepareSQLStatement($preservedStrLength=-1){
			return (new PrepareEmulation($this->_pdo))->emulate($this->_sqlStatement,$this->_sqlBindParams,$this->_sqlBindTypes,$preservedStrLength);
		}

		/**
		 * Active SQL statement in the object
		 *
		 * @return array
		 */
		public function getSQLBindParams(){
			return $this->_sqlBindParams;
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

			$this->fireEvent('db:beginTransaction',$this);

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

			$this->fireEvent('db:rollbackTransaction',$this);

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

			$this->fireEvent('db:commitTransaction',$this);


			$this->_transactionLevel--;
			return $this->_pdo->commit();
		}

		/**
		 * Returns insert id for the auto_increment column inserted in the last SQL statement
		 *
		 * @return int
		 */
		public function lastInsertId(){
			return (int)$this->_pdo->lastInsertId();
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
