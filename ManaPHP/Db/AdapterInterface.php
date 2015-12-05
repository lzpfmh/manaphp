<?php 

namespace ManaPHP\Db {

	/**
	 * \ManaPHP\Db\AdapterInterface initializer
	 */
	
	interface AdapterInterface {

		/**
		 * Returns the first row in a SQL query result
		 *
		 * @param string $sqlQuery
		 * @param int $fetchMode
		 * @param int $placeholders
		 * @return array
		 */
		public function fetchOne($sqlQuery, $fetchMode=null, $placeholders=null);


		/**
		 * Dumps the complete result of a query into an array
		 *
		 * @param string $sqlQuery
		 * @param int $fetchMode
		 * @param int $placeholders
		 * @return array
		 */
		public function fetchAll($sqlQuery, $fetchMode=null, $placeholders=null);


		/**
		 * Inserts data into a table using custom RBDMS SQL syntax
		 *
		 * @param 	string $table
		 * @param 	array $values
		 * @param 	array $fields
		 * @param 	array $dataTypes
		 * @return 	boolean
		 */
		public function insert($table, $values, $fields=null, $dataTypes=null);


		/**
		 * Updates data on a table using custom RBDMS SQL syntax
		 *
		 * @param 	string $table
		 * @param 	array $fields
		 * @param 	array $values
		 * @param 	string $whereCondition
		 * @param 	array $dataTypes
		 * @return 	boolean
		 */
		public function update($table, $fields, $values, $whereCondition=null, $dataTypes=null);


		/**
		 * Deletes data from a table using custom RBDMS SQL syntax
		 *
		 * @param  string $table
		 * @param  string $whereCondition
		 * @param  array $placeholders
		 * @param  array $dataTypes
		 * @return boolean
		 */
		public function delete($table, $whereCondition=null, $placeholders=null, $dataTypes=null);


		/**
		 * Gets a list of columns
		 *
		 * @param array $columnList
		 * @return string
		 */
		public function getColumnList($columnList);


		/**
		 * Appends a LIMIT clause to $sqlQuery argument
		 *
		 * @param  	string $sqlQuery
		 * @param 	int $number
		 * @return 	string
		 */
		public function limit($sqlQuery, $number);


		/**
		 * Returns a SQL modified with a FOR UPDATE clause
		 *
		 * @param string $sqlQuery
		 * @return string
		 */
		public function forUpdate($sqlQuery);


		/**
		 * Returns a SQL modified with a LOCK IN SHARE MODE clause
		 *
		 * @param string $sqlQuery
		 * @return string
		 */
		public function sharedLock($sqlQuery);


		/**
		 * Active SQL statement in the object
		 *
		 * @return string
		 */
		public function getSQLStatement();


		/**
		 * Active SQL statement in the object without replace bound parameters
		 *
		 * @return string
		 */
		public function getRealSQLStatement();


		/**
		 * Active SQL statement in the object
		 *
		 * @return array
		 */
		public function getSQLVariables();


		/**
		 * Sends SQL statements to the database server returning the success state.
		 * Use this method only when the SQL statement sent to the server return rows
		 *
		 * @param  string $sqlStatement
		 * @param  array $placeholders
		 * @param  array $dataTypes
		 * @return \ManaPHP\Db\ResultInterface
		 */
		public function query($sqlStatement, $placeholders=null, $dataTypes=null);


		/**
		 * Sends SQL statements to the database server returning the success state.
		 * Use this method only when the SQL statement sent to the server don't return any row
		 *
		 * @param  string $sqlStatement
		 * @param  array $placeholders
		 * @param  array $dataTypes
		 * @return boolean
		 */
		public function execute($sqlStatement, $placeholders=null, $dataTypes=null);


		/**
		 * Returns the number of affected rows by the last INSERT/UPDATE/DELETE reported by the database system
		 *
		 * @return int
		 */
		public function affectedRows();


		/**
		 * Escapes a column/table/schema name
		 *
		 * @param string $identifier
		 * @return string
		 */
		public function escapeIdentifier($identifier);


		/**
		 * Returns insert id for the auto_increment column inserted in the last SQL statement
		 *
		 * @param string $sequenceName
		 * @return int
		 */
		public function lastInsertId($sequenceName=null);


		/**
		 * Starts a transaction in the connection
		 *
		 * @return boolean
		 */
		public function begin();


		/**
		 * Checks whether the connection is under a transaction
		 *
		 *<code>
		 *	$connection->begin();
		 *	var_dump($connection->isUnderTransaction()); //true
		 *</code>
		 */
		public function isUnderTransaction();
		/**
		 * Rollbacks the active transaction in the connection
		 *
		 * @return boolean
		 */
		public function rollback();


		/**
		 * Commits the active transaction in the connection
		 *
		 * @return boolean
		 */
		public function commit();


		/**
		 * Return internal PDO handler
		 *
		 * @return \PDO
		 */
		public function getInternalHandler();


		/**
		 * Returns an array of \ManaPHP\Db\Column objects describing a table
		 *
		 * @param string $table
		 * @param string $schema
		 * @return \ManaPHP\Db\ColumnInterface[]
		 */
		public function describeColumns($table, $schema=null);

	}
}
