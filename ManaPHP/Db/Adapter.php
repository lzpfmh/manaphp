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

		protected $_eventsManager;

		protected $_descriptor;

		protected $_dialectType;

		protected $_type;

		protected $_dialect;

		protected $_connectionId;

		protected $_sqlStatement;

		protected $_sqlVariables;

		protected $_sqlBindTypes;

		protected $_transactionLevel;

		protected $_transactionsWithSavepoints;

		protected static $_connectionConsecutive;

		/**
		 * \ManaPHP\Db\Adapter constructor
		 *
		 * @param array $descriptor
		 */
		public function __construct(){ }


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
		public function getSQLStatement(){ }


		/**
		 * Active SQL statement in the object without replace bound parameters
		 *
		 * @return string
		 */
		public function getRealSQLStatement(){ }


		/**
		 * Active SQL statement in the object
		 *
		 * @return array
		 */
		public function getSQLVariables(){ }


		/**
		 * Active SQL statement in the object
		 *
		 * @return array
		 */
		public function getSQLBindTypes(){ }


		/**
		 * Returns type of database system the adapter is used for
		 *
		 * @return string
		 */
		public function getType(){ }

	}
}
