<?php 

namespace ManaPHP\Mvc {

	/**
	 * ManaPHP\Mvc\ModelInterface initializer
	 */
	
	interface ModelInterface {

		/**
		 * Returns table name mapped in the model
		 *
		 * @return string
		 */
		public function getSource();


		/**
		 * Returns schema name where table mapped is located
		 *
		 * @return string
		 */
		public function getSchema();


		/**
		 * Sets both read/write connection services
		 *
		 * @param string $connectionService
		 */
		public function setConnectionService($connectionService);


		/**
		 * Sets the DependencyInjection connection service used to write data
		 *
		 * @param string $connectionService
		 */
		public function setWriteConnectionService($connectionService);


		/**
		 * Sets the DependencyInjection connection service used to read data
		 *
		 * @param string $connectionService
		 */
		public function setReadConnectionService($connectionService);


		/**
		 * Returns DependencyInjection connection service used to read data
		 *
		 * @return string
		 */
		public function getReadConnectionService();


		/**
		 * Returns DependencyInjection connection service used to write data
		 *
		 * @return string
		 */
		public function getWriteConnectionService();


		/**
		 * Gets internal database connection
		 *
		 * @return \ManaPHP\Db\AdapterInterface
		 */
		public function getReadConnection();


		/**
		 * Gets internal database connection
		 *
		 * @return \ManaPHP\Db\AdapterInterface
		 */
		public function getWriteConnection();


		/**
		 * Assigns values to a model from an array
		 *
		 * @param array $data
		 * @param array $columnMap
		 * @return \ManaPHP\Mvc\Model
		 */
		public function assign($data, $columnMap=null);


		/**
		 * Allows to query a set of records that match the specified conditions
		 *
		 * @param 	array $parameters
		 * @return  \ManaPHP\Mvc\Model\ResultsetInterface
		 */
		public static function find($parameters=null);


		/**
		 * Allows to query the first record that match the specified conditions
		 *
		 * @param array $parameters
		 * @return \ManaPHP\Mvc\ModelInterface
		 */
		public static function findFirst($parameters=null);


		/**
		 * Create a criteria for a special model
		 *
		 * @param \ManaPHP\DiInterface $dependencyInjector
		 * @return \ManaPHP\Mvc\Model\CriteriaInterface
		 */
		public static function query($dependencyInjector=null);


		/**
		 * Allows to count how many records match the specified conditions
		 *
		 * @param array $parameters
		 * @return int
		 */
		public static function count($parameters=null);


		/**
		 * Allows to calculate a summatory on a column that match the specified conditions
		 *
		 * @param array $parameters
		 * @return double
		 */
		public static function sum($parameters=null);


		/**
		 * Allows to get the maximum value of a column that match the specified conditions
		 *
		 * @param array $parameters
		 * @return mixed
		 */
		public static function maximum($parameters=null);


		/**
		 * Allows to get the minimum value of a column that match the specified conditions
		 *
		 * @param array $parameters
		 * @return mixed
		 */
		public static function minimum($parameters=null);


		/**
		 * Allows to calculate the average value on a column matching the specified conditions
		 *
		 * @param array $parameters
		 * @return double
		 */
		public static function average($parameters=null);


		/**
		 * Inserts or updates a model instance. Returning true on success or false otherwise.
		 *
		 * @param  array $data
		 * @param  array $whiteList
		 * @return boolean
		 */
		public function save($data=null, $whiteList=null);


		/**
		 * Inserts a model instance. If the instance already exists in the persistance it will throw an exception
		 * Returning true on success or false otherwise.
		 *
		 * @param  array $data
		 * @param  array $whiteList
		 * @return boolean
		 */
		public function create($data=null, $whiteList=null);


		/**
		 * Updates a model instance. If the instance doesn't exist in the persistance it will throw an exception
		 * Returning true on success or false otherwise.
		 *
		 * @param  array $data
		 * @param  array $whiteList
		 * @return boolean
		 */
		public function update($data=null, $whiteList=null);


		/**
		 * Deletes a model instance. Returning true on success or false otherwise.
		 *
		 * @return boolean
		 */
		public function delete();


		/**
		 * Refreshes the model attributes re-querying the record from the database
		 */
		public function refresh();
	}
}
