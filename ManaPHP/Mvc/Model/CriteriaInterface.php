<?php 

namespace ManaPHP\Mvc\Model {

	/**
	 * ManaPHP\Mvc\Model\CriteriaInterface initializer
	 */
	
	interface CriteriaInterface {

		/**
		 * Set a model on which the query will be executed
		 *
		 * @param string $modelName
		 * @return static
		 */
		public function setModelName($modelName);


		/**
		 * Returns an internal model name on which the criteria will be applied
		 *
		 * @return string
		 */
		public function getModelName();


		/**
		 * Adds the bind parameter to the criteria
		 *
		 * @param string $bindParams
		 * @return static
		 */
		public function bind($bindParams);


		/**
		 * Sets the bind types in the criteria
		 * This method replaces all previously set bound parameters
		 *
		 * @param string $bindTypes
		 * @return static
		 */
		public function bindTypes($bindTypes);


		/**
		 * Sets the columns to be queried
		 *
		 *<code>
		 *	$criteria->columns(array('id', 'name'));
		 *</code>
		 *
		 * @param string|array $columns
		 * @return static
		 */
		public function columns($columns);


		/**
		 * Adds a join to the query
		 *
		 *<code>
		 *	$criteria->join('Robots');
		 *	$criteria->join('Robots', 'r.id = RobotsParts.robots_id');
		 *	$criteria->join('Robots', 'r.id = RobotsParts.robots_id', 'r');
		 *	$criteria->join('Robots', 'r.id = RobotsParts.robots_id', 'r', 'LEFT');
		 *</code>
		 *
		 * @param string $model
		 * @param string $conditions
		 * @param string $alias
		 * @param string $type
		 * @return static
		 */
		public function join($model, $conditions=null, $alias=null, $type=null);


		/**
		 * Adds the conditions parameter to the criteria
		 *
		 * @param string $conditions
		 * @param array $bindParams
		 * @param array $bindTypes
		 * @return static
		 */
		public function where($conditions,$bindParams,$bindTypes);


		/**
		 * Adds the conditions parameter to the criteria
		 *
		 * @param string $conditions
		 * @return static
		 */
		public function conditions($conditions);


		/**
		 * Adds the order-by parameter to the criteria
		 *
		 * @param string $orderColumns
		 * @return static
		 */
		public function orderBy($orderColumns);


		/**
		 * Sets the limit parameter to the criteria
		 *
		 * @param int $limit
		 * @param int $offset
		 * @return static
		 */
		public function limit($limit, $offset=null);


		/**
		 * Sets the "for_update" parameter to the criteria
		 *
		 * @param boolean $forUpdate
		 * @return static
		 */
		public function forUpdate($forUpdate=null);


		/**
		 * Sets the "shared_lock" parameter to the criteria
		 *
		 * @param boolean $sharedLock
		 * @return static
		 */
		public function sharedLock($sharedLock=null);


		/**
		 * Appends a condition to the current conditions using an AND operator
		 *
		 * @param string $conditions
		 * @param array $bindParams
		 * @param array $bindTypes
		 * @return static
		 */
		public function andWhere($conditions, $bindParams=null, $bindTypes=null);


		/**
		 * Appends a condition to the current conditions using an OR operator
		 *
		 * @param string $conditions
		 * @param array $bindParams
		 * @param array $bindTypes
		 * @return static
		 */
		public function orWhere($conditions, $bindParams=null, $bindTypes=null);


		/**
		 * Appends a BETWEEN condition to the current conditions
		 *
		 *<code>
		 *	$criteria->betweenWhere('price', 100.25, 200.50);
		 *</code>
		 *
		 * @param string $expr
		 * @param mixed $minimum
		 * @param mixed $maximum
		 * @return \ManaPHP\Mvc\Model\Query\Builder
		 */
		public function betweenWhere($expr, $minimum, $maximum);


		/**
		 * Appends a NOT BETWEEN condition to the current conditions
		 *
		 *<code>
		 *	$criteria->notBetweenWhere('price', 100.25, 200.50);
		 *</code>
		 *
		 * @param string $expr
		 * @param mixed $minimum
		 * @param mixed $maximum
		 * @return \ManaPHP\Mvc\Model\Query\Builder
		 */
		public function notBetweenWhere($expr, $minimum, $maximum);


		/**
		 * Appends an IN condition to the current conditions
		 *
		 *<code>
		 *	$criteria->inWhere('id', [1, 2, 3]);
		 *</code>
		 *
		 * @param string $expr
		 * @param array $values
		 * @return \ManaPHP\Mvc\Model\Query\Builder
		 */
		public function inWhere($expr, $values);


		/**
		 * Appends a NOT IN condition to the current conditions
		 *
		 *<code>
		 *	$criteria->notInWhere('id', [1, 2, 3]);
		 *</code>
		 *
		 * @param string $expr
		 * @param array $values
		 * @return \ManaPHP\Mvc\Model\Query\Builder
		 */
		public function notInWhere($expr, $values);


		/**
		 * Returns the conditions parameter in the criteria
		 *
		 * @return string
		 */
		public function getWhere();


		/**
		 * Returns the conditions parameter in the criteria
		 *
		 * @return string
		 */
		public function getConditions();


		/**
		 * Returns the limit parameter in the criteria
		 *
		 * @return string
		 */
		public function getLimit();


		/**
		 * Returns the order parameter in the criteria
		 *
		 * @return string
		 */
		public function getOrder();


		/**
		 * Returns all the parameters defined in the criteria
		 *
		 * @return string
		 */
		public function getParams();


		/**
		 * Builds a \ManaPHP\Mvc\Model\Criteria based on an input array like $_POST
		 *
		 * @param \ManaPHP\DiInterface $dependencyInjector
		 * @param string $modelName
		 * @param array $data
		 * @return static
		 */
		public static function fromInput($dependencyInjector, $modelName, $data);


		/**
		 * Executes a find using the parameters built with the criteria
		 *
		 * @return \ManaPHP\Mvc\Model\ResultsetInterface
		 */
		public function execute();

	}
}
