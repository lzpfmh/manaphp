<?php 

namespace ManaPHP\Db {

	use ManaPHP\Db;
	use \ManaPHP\Events\EventsAwareInterface;

	/**
	 * ManaPHP\Db\Adapter
	 *
	 * Base class for ManaPHP\Db adapters
	 */
	
	abstract class Adapter implements EventsAwareInterface, AdapterInterface {

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
					$statement->bindValue($parameter,$v);
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
				$statement =$this->_pdo->query($sqlStatement);
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
		 */
		public function execute($sqlStatement, $bindParams = null, $bindTypes = null){
			if(is_object($this->_eventsManager)){
				$this->_sqlStatement =$sqlStatement;
				$this->_sqlVariables =$bindParams;
				$this->_sqlBindTypes =$bindTypes;

				if($this->_eventsManager->fire('db:beforeQuery',$this,$bindParams) ===false){
					return false;
				}
			}

			$affectedRows=0;

			if(is_array($bindParams)){
				$statement =$this->_pdo->prepare($sqlStatement);
				if(is_object($sqlStatement)){
					$newStatement=$this->executePrepared($statement,$bindParams,$bindTypes);
					$affectedRows =$newStatement->rowCount();
				}
			}else{
				$affectedRows =$this->_pdo->exec($sqlStatement);
			}

			if(is_int($affectedRows)){
				$this->_affectedRows =$affectedRows;
				if(is_object($this->_eventsManager)){
					$this->_eventsManager->fire('db:afterQuery',$this,$bindParams);
				}
			}

			return true;
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
		 * @return array
		 */
		public function fetchOne($sqlQuery, $fetchMode=Db::FETCH_ASSOC, $bindParams=null){
			$result =$this->query($sqlQuery, $bindParams, null);
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
		 * @return array
		 */
		public function fetchAll($sqlQuery, $fetchMode=Db::FETCH_ASSOC, $bindParams=null){
			$result =[];
			$result =$this->query($sqlQuery,$bindParams, null);
			if($result !==null){
				$result->setFetchMode($fetchMode);
			}

			while(1){
				$row =$result->fetch();
			}
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
		 */
		public function insert($table, $values, $fields=null, $dataTypes=null){ }


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
		 */
		public function update($table, $fields, $values, $whereCondition=null, $dataTypes=null){ }


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
		 */
		public function delete($table, $whereCondition=null, $placeholders=null, $dataTypes=null){ }


		/**
		 * Gets a list of columns
		 *
		 * @param array $columnList
		 * @return string
		 */
		public function getColumnList($columnList){ }


		/**
		 * Appends a LIMIT clause to $sqlQuery argument
		 *
		 * <code>
		 * 	echo $connection->limit("SELECT * FROM robots", 5);
		 * </code>
		 *
		 * @param  	string $sqlQuery
		 * @param 	int $number
		 * @return 	string
		 */
		public function limit($sqlQuery, $number){ }


		/**
		 * Returns a SQL modified with a FOR UPDATE clause
		 *
		 * @param string $sqlQuery
		 * @return string
		 */
		public function forUpdate($sqlQuery){ }


		/**
		 * Returns a SQL modified with a LOCK IN SHARE MODE clause
		 *
		 * @param string $sqlQuery
		 * @return string
		 */
		public function sharedLock($sqlQuery){ }



		/**
		 * Returns the SQL column definition from a column
		 *
		 * @param \ManaPHP\Db\ColumnInterface $column
		 * @return string
		 */
		public function getColumnDefinition($column){ }


		/**
		 * Gets the active connection unique identifier
		 *
		 * @return string
		 */
		public function getConnectionId(){ }


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
		 * Returns type of database system the adapter is used for
		 *
		 * @return string
		 */
		public function getType(){ }

		/**
		 * Starts a transaction in the connection
		 *
		 * @return boolean
		 */
		public function begin(){
			if(!is_object($this->_pdo)){
				return false;
			}

			if(is_object($this->_eventsManager)){
				$this->_eventsManager->fire('db:beginTransaction',$this);
			}
			return $this->_pdo->beginTransaction();
		}

		/**
		 * Checks whether the connection is under a transaction
		 *
		 *<code>
		 *	$connection->begin();
		 *	var_dump($connection->isUnderTransaction()); //true
		 *</code>
		 */
		public function isUnderTransaction(){
			if($this->_pdo ===null){
				return false;
			}

			return $this->_pdo->inTransaction();
		}

		/**
		 * Rollbacks the active transaction in the connection
		 *
		 * @return boolean
		 */
		public function rollback(){
			if(!is_object($this->_pdo)){
				return false;
			}

			if(is_object($this->_eventsManager)){
				$this->_eventsManager->fire('db:rollbackTransaction',$this);
			}

			return $this->_pdo->rollBack();
		}


		/**
		 * Commits the active transaction in the connection
		 *
		 * @return boolean
		 */
		public function commit(){
			if($this->_pdo ===null){
				return false;
			}

			return $this->commit();
		}
	}
}
